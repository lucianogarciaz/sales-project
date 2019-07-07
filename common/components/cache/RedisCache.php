<?php

namespace common\components\cache;

use Yii;
use Exception;
use yii\redis\Cache;

/**
 * Nueva versión del cache
 *
 * Componente de caché para Yii. Hereda del componente oficial de caché para Redis.
 * Para evitar múltiples consultas simultaneas a la DB se utiliza la implementación de mutex de Redis
 *
 * Se debe configurar el mutex en el archivo main
 *
 * 'components' => [
 *      'mutexCache' => [
 *          'class' => 'yii\redis\Mutex',
 *      ],
 *      ...
 * ]
 */
class RedisCache extends Cache
{
    const DELTA_PREFIX = 'delta_';
    public $defaultDelta = 1.5 * 1000; // Tiempo de espera por defecto para una consulta a la base de datos (milisegundos)
    public $minTimeout = 1 * 1000; // Tiempo mínimo de espera para una consulta a la base de datos (milisegundos)
    public $maxTimeout = 10 * 1000; // Tiempo máximo de espera para una consulta a la base de datos (milisegundos)
    public $pollingGap = 0.1; // Porcentaje del delta que es utilizado como intervalo de tiempo en la espera activa
    public $tolerance = 1.5; // Multiplicador del tiempo de espera para una consulta
    public $beta = 1; // Variable de ajuste de la función XFetch
    
    /**
     * @inheritdoc
     */
    public function getOrSet($key, $callable, $duration = null, $dependency = null)
    {
        // Busca los datos en el caché. Por la naturaleza de Redis las consultas se serializan
        $value = $this->get($key);
        // Se calcula el timeout esperado para la consulta
        $delta = $this->get(self::DELTA_PREFIX . $key);
        if ($delta === false) {
            $delta = $this->defaultDelta;
        }
        
        /*
            Si el dato se encuentra en el cache, se decide si ir a buscarlo igualmente o no (siguiente la función xFetch).
            En caso de no ir a buscar el dato se devuelve el dato actual (válido).
        */
        if ($value !== false && !$this->xFetch($key, $delta)) {
            return $value;
        }
        $timeout = max(min($delta * $this->tolerance, $this->maxTimeout), $this->minTimeout);

        /*
            Se serializa la consulta a la base de datos con un semáforo. Si se adquiere el lock
            se realiza la consulta, sino comprueba si el valor del cache aún es valido, de ser así,
            lo devuelve, en caso contrario espera el resultado de la consulta
        */
        Yii::$app->mutexCache->expire = 1 + floor($timeout / 1000);
        if (Yii::$app->mutexCache->acquire($key)) {
            $value = $this->callAndSet($key, $callable, $duration, $dependency);
            Yii::$app->mutexCache->release($key);
            return $value;
        } elseif ($value) {
            return $value;
        } else {
            try {
                $value = $this->waitForResult($key, $timeout);
                if ($value !== false) {
                    return $value;
                }
            } catch (\Exception $e) {
                Yii::warning("Caché GET ({$key}) : " . $e->getMessage(), __METHOD__);
            }
        }
        // Si todo falla buscar en la base de datos
        return $this->callAndSet($key, $callable, $duration, $dependency);
    }

    /**
     * A través de una decisión probabilística, indica si se debe renovar el valor de la
     * memoria caché antes de que el mismo expire. La probabilidad de realizar la cansulta
     * anticipada a la base de datos aumenta a medida que se acerca el vencimiento de este.
     *
     * @param string $key
     * @param float $delta
     * @return boolean Valor booleano que indica si debe realizarse la consulta a la base de datos.
     */
    public function xfetch($key, $delta)
    {
        $expiry = Yii::$app->redis->executeCommand("PTTL", [$this->buildKey($key)]);
        if ($expiry < 0) {
            return false;
        }
        // Si BETA > 1 la consulta a la base de datos se realizará con mayor anticipación,
        // si BETA < 1 la consulta se ralizará de forma mas tardía
        return $expiry + ($delta * $this->beta * log(rand(1, 1000) / 1000)) <= 0;
    }

    /**
     * Realiza el llamado a la base de datos, setea el cache y
     * actualiza el timeout para la consulta.
     *
     * @param string $key
     * @param $callable
     * @param float $duration
     * @return mixed Valor de la consulta.
     */
    public function callAndSet($key, $callable, $duration, $dependency)
    {
        $startTime = microtime(true);
        $value = call_user_func($callable, $this);
        $delta = (microtime(true) - $startTime) * 1000;
        $this->set(self::DELTA_PREFIX . $key, $delta);
        
        if (!$this->set($key, $value, $duration, $dependency)) {
            Yii::warning('Failed to set cache value for key ' . json_encode($key), __METHOD__);
        }
        return $value;
    }

    /**
     * Bloquea el proceso hasta que se encuentren datos válidos en el caché o retorna una vez
     * superado el timeout.
     *
     * @param string $key
     * @param float $timeout
     * @return mixed Valor de la consulta o false si no se encuentra.
     */
    private function waitForResult($key, $timeout)
    {
        $count = 0;
        while ($count < $timeout) {
            $value = $this->get($key);
            if ($value !== false) {
                return $value;
            }
            $count += $this->pollingGap * $timeout;
            usleep($this->pollingGap * $timeout * 1000);
        }
        return false;
    }
}
