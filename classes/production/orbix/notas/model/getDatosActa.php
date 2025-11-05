<?php

namespace notas\model;

use notas\model\entity\GestorNota;
use notas\model\entity\GestorPersonaNotaDB;

class getDatosActa
{

    public static function getNotas(string $acta): array
    {
        $aWhere = [];
        $aOperador = [];

        $GesNotas = new GestorNota();
        $aIdSuperadas = $GesNotas->getArrayNotasSuperadas();
        $superadas_txt = "{" . implode(', ', $aIdSuperadas) . "}";

        $aWhere['id_situacion'] = $superadas_txt;
        $aOperador['id_situacion'] = 'ANY';
        $aWhere['acta'] = $acta;
        $aWhere['tipo_acta'] = PersonaNota::FORMATO_ACTA;

        $GesPersonaNotas = new GestorPersonaNotaDB();
        return $GesPersonaNotas->getPersonaNotas($aWhere, $aOperador);
    }

}