<?php

namespace notas\model;

use notas\model\entity\GestorPersonaNotaDB;
use src\notas\domain\contracts\NotaRepositoryInterface;

class getDatosActa
{

    public static function getNotasActa(string $acta): array
    {
        $aWhere = [];
        $aOperador = [];

        $NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
        $aIdSuperadas = $NotaRepository->getArrayNotasSuperadas();

        $aWhere['id_situacion'] = implode(',', $aIdSuperadas);
        $aOperador['id_situacion'] = 'IN';
        $aWhere['acta'] = $acta;
        $aWhere['tipo_acta'] = PersonaNota::FORMATO_ACTA;

        $GesPersonaNotas = new GestorPersonaNotaDB();
        return $GesPersonaNotas->getPersonaNotas($aWhere, $aOperador);
    }

}