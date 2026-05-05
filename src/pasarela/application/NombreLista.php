<?php

namespace src\pasarela\application;

use src\actividades\domain\entity\TiposActividades;
use src\pasarela\domain\Nombre;

/**
 * Devuelve el listado del parámetro `nombre` listo para serializar.
 *
 * Estructura: `{excepciones: [{id_tipo_activ, etiqueta, valor}]}`.
 * (El parámetro `nombre` no tiene valor por defecto.)
 */
final class NombreLista
{
    public static function execute(): array
    {
        $oNombre = new Nombre();

        $a_excepciones_raw = $oNombre->getExcepciones();
        $a_excepciones = [];
        foreach ($a_excepciones_raw as $id_tipo_activ => $valor) {
            $oActividadTipo = new TiposActividades((string)$id_tipo_activ);
            $a_excepciones[] = [
                'id_tipo_activ' => (string)$id_tipo_activ,
                'etiqueta' => $oActividadTipo->getNom(),
                'valor' => (string)$valor,
            ];
        }

        return [
            'excepciones' => $a_excepciones,
        ];
    }
}
