<?php

namespace src\pasarela\application;

use src\actividades\domain\entity\TiposActividades;
use src\pasarela\domain\ContribucionReserva;

/**
 * Devuelve el listado del parámetro `contribucion_reserva` listo para serializar.
 *
 * Estructura: `{default, excepciones: [{id_tipo_activ, etiqueta, valor}]}`.
 */
final class ContribucionReservaLista
{
    public static function execute(): array
    {
        $oContribucionReserva = new ContribucionReserva();

        $a_excepciones_raw = $oContribucionReserva->getExcepciones();
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
            'default' => (string)$oContribucionReserva->getDefault(),
            'excepciones' => $a_excepciones,
        ];
    }
}
