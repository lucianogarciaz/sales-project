<?php
namespace common\components;

use Yii;
use yii\helpers\BaseArrayHelper;

class NinjaArrayHelper extends BaseArrayHelper
{
    /**
     * Agrupa los elementos de un array según la lista de $keys recibidas. Para cada elemento del array original,
     * se crea un subarray bajo la key $keyGrupo con las claves que no están en el listado. Si $saltarPrimero = true
     * se omite el primer elemento de cada grupo.
     * 
     * Ej: $array = [
     *                  ['a' => 1, 'b' => 2, 'c' => 3, 'd' = 4],
     *                  ['a' => 1, 'b' => 2, 'c' => 5, 'd' = 6],
     *              ]
     * 
     * NinjaArrayHelper::groupBy($array, ['a', 'b'], 'Otras', false) retorna:
     * 
     * [
     *      [
     *          'a' => 1,
     *          'b' => 2,
     *          'Otras' => [
     *                  ['c' => 3, 'd' => 4],
     *                  ['c' => 5, 'd' => 6]
     *              ]
     *      ]
     * ]
     *
     * @param array $array Array origen
     * @param array $keys Lista de claves por las que se agrupa
     * @param string $keyGrupo Clave a la que se le asigna el resto de keys
     * @param boolean $saltarPrimero Omitir el primer elemento de cada grupo
     * @return array
     */
    public static function groupBy(array $array, array $keys, string $keyGrupo, bool $saltarPrimero = false, array $mantenerDelPrimero = []): array
    {
        $keys = (array) $keys;
        $array = self::agrupar($array, $keys);
        $out = [];

        foreach ($array as $grupo) {
            $elemento = self::getMultipleKeys($grupo[0], $keys);

            if(count($mantenerDelPrimero)){
                $elementosAMantener = self::getMultipleKeys($grupo[0], $mantenerDelPrimero);
                $elemento = self::merge($elemento, $elementosAMantener);
            }
            
            if($saltarPrimero) {
                $grupo = array_splice($grupo, 1);
            }

            $elemento[$keyGrupo] = array_map(function($item) use ($keys) {
                return self::removeKeys($item, $keys);
            }, $grupo);

            $out[] = $elemento;
        }

        return $out;
    }

    /**
     * Retorna un array que sólo contiene las claves indicadas en $keys del array original.
     *
     * @param array $array Array origen
     * @param array $keys Lista de claves a mantener
     * @return array
     */
    public static function getMultipleKeys(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }
    
    /**
     * Retorna un array sin las claves contenidas en $keys.
     *
     * @param array $array Array origen
     * @param array $keys Lista de claves a quitar del array.
     * @return array
     */
    public static function removeKeys(array $array, array $keys):array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Renombra una key de un array
     *
     * @param array $array Array origen
     * @param string $key Clave anterior
     * @param string $nombreNuevo Clave nueva
     * @return void
     */
    public static function renameKey(array $array, string $key, string $nombreNuevo)
    {
        $array[$nombreNuevo] = $array[$key];
        unset($array[$key]);
        return $array;
    }

    private static function agrupar(array $array, array $keys): array
    {
        $out = [];

        foreach ($array as $fila) {
            $comunes = self::getMultipleKeys($fila, $keys);
            $claveFila = implode('_', $comunes);
            $out[$claveFila][] = $fila;
        }

        return $out;
    }
}
