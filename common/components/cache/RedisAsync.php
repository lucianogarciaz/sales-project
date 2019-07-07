<?php

namespace common\components\cache;

use Yii;
use Amp\Promise;
use yii\redis\Cache;
use Amp\Redis\Client;
use yii\caching\Dependency;
use Amp\Redis\SubscribeClient;
use function Amp\Promise\wait;
use function Amp\Promise\timeout;

/**
 * Componente de caché para Yii. Hereda del componente oficial de caché para Redis, pero se diferencia en que
 * el método getOrSet, mediante la librería AMP, evita que la misma función para obtener los datos se ejecute
 * varias veces para diferentes requests, bloqueando en espera del resultado de la primera.
 */
class RedisAsync extends Cache
{
    const WAIT_TIMEOUT = 30 * 1000; // 30 segundos
    const PREFIJO_FLAG_KEY = 'loading-';

    /**
     * @inheritdoc
     */
    public function getOrSet($key, $callable, $duration = null, $dependency = null)
    {
        if (($value = $this->getAsync($key)) !== false) {
            return $value;
        }

        $value = call_user_func($callable, $this);
        if (!$this->setAsync($key, $value, $duration, $dependency)) {
            Yii::warning('Failed to set cache value for key ' . json_encode($key), __METHOD__);
        }

        return $value;
    }

    /**
     * Reemplaza a la función getValue para get asincrónico.
     *
     * @param string $key
     * @return mixed Valor almacenado en el caché
     */
    protected function getValueAsync($key)
    {
        $data = $this->getValue($key);

        if ($data !== null) {
            return $data;
        }

        if ($this->estaEjecutandose($key)) {
            try {
                return $this->esperarResultado($key);
            } catch (\Exception $e) {
                Yii::warning("Caché GET ({$key}) : " . $e->getMessage(), __METHOD__);
                return null;
            }
        } else {
            $this->marcarEjecutandose($key);
            return null;
        }
    }

    /**
     * Reemplaza a la función setValue para set asincrónico.
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return bool
     */
    protected function setValueAsync($key, $value, $expire): bool
    {
        try {
            $salida = $this->setValue($key, $value, $expire);
            $this->marcarEjecucionFinalizada($key);
            $this->publicarResultado($key, $value);

            return $salida;
        } catch (\Exception $e) {
            Yii::warning('Caché SET: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Indica si otro request está ejecutando la consulta.
     *
     * @param string $key
     * @return boolean
     */
    private function estaEjecutandose(string $key): bool
    {
        $flagKey = $this->generarFlagKey($key);
        return (bool) $this->getValue($flagKey);
    }

    /**
     * Marca a la consulta como ejecutándose en el request actual.
     *
     * @param string $key
     * @return bool
     */
    private function marcarEjecutandose(string $key): bool
    {
        $flagKey = $this->generarFlagKey($key);
        return $this->setValue($flagKey, true, self::WAIT_TIMEOUT / 1000);
    }

    /**
     * Informa el resultado a todos los requests que lo estaban esperando.
     *
     * @param string $key
     * @param mixed $value
     */
    private function publicarResultado(string $key, $value)
    {
        $client = new Client($this->urlRedis());
        $this->waitConTimeout($client->publish($key, $value));
    }

    /**
     * Marca a la ejecución de la consulta como finalizada.
     *
     * @param string $key
     */
    private function marcarEjecucionFinalizada(string $key)
    {
        $this->deleteValue($this->generarFlagKey($key));
    }

    /**
     * Bloquea el proceso hasta que se reciba la respuesta de una consulta ejecutada en otro
     * proceso.
     *
     * @param string $key
     * @return mixed Valor de la consulta enviado desde el proceso que ejecutó la consulta.
     */
    private function esperarResultado(string $key)
    {
        $client = new SubscribeClient($this->urlRedis());

        $iterator = $this->waitConTimeout($client->subscribe($key));
        $this->waitConTimeout($iterator->advance());
        $data = $iterator->getCurrent();

        $client->close();
        return $data;
    }

    /**
     * Url de redis obtenida desde la configuración del componente.
     *
     * @return string Url de redis
     */
    private function urlRedis(): string
    {
        $redis = $this->redis;
        return 'tcp://' . $redis->hostname . ':' . $redis->port;
    }

    /**
     * Retorna la key del flag usado para indicar la ejecución de una consulta.
     *
     * @param string $key
     * @return string
     */
    private function generarFlagKey(string $key): string
    {
        $flagKey = self::PREFIJO_FLAG_KEY . $key;
        return $this->buildKey($flagKey);
    }

    /**
     * Setea el timeout a una Promise en WAIT_TIMEOUT y espera a que se resuelva, retornando el resultado.
     *
     * @param Promise $promise
     * @return mixed Resultado de $promise.
     */
    private function waitConTimeout(Promise $promise)
    {
        return wait(timeout($promise, self::WAIT_TIMEOUT));
    }

    /**
     * Reemplazo del método get para get asincrónico.
     *
     * @param string $key
     * @return mixed
     */
    private function getAsync($key)
    {
        $key = $this->buildKey($key);
        $value = $this->getValueAsync($key);
        if ($value === false || $this->serializer === false) {
            return $value;
        } elseif ($this->serializer === null) {
            $value = unserialize($value);
        } else {
            $value = call_user_func($this->serializer[1], $value);
        }
        if (is_array($value) && !($value[1] instanceof Dependency && $value[1]->isChanged($this))) {
            return $value[0];
        }

        return false;
    }

    /**
     * Reemplazo del método set para set asincrónico.
     *
     * @param string $key
     * @param mixed $value
     * @param int $duration
     * @param Dependency $dependency
     * @return bool
     */
    private function setAsync($key, $value, $duration = null, $dependency = null): bool
    {
        if ($duration === null) {
            $duration = $this->defaultDuration;
        }

        if ($dependency !== null && $this->serializer !== false) {
            $dependency->evaluateDependency($this);
        }
        if ($this->serializer === null) {
            $value = serialize([$value, $dependency]);
        } elseif ($this->serializer !== false) {
            $value = call_user_func($this->serializer[0], [$value, $dependency]);
        }
        $key = $this->buildKey($key);

        return $this->setValueAsync($key, $value, $duration);
    }
}
