<?php

namespace notas\model;

use notas\model\entity\GestorPersonaNotaDB;
use src\notas\application\repositories\NotaRepository;

class getDatosActa
{

    public static function getNotasActa(string $acta): array
    {
        $aWhere = [];
        $aOperador = [];

        $NotaRepository = new NotaRepository();
        $aIdSuperadas = $NotaRepository->getArrayNotasSuperadas();

        $aWhere['id_situacion'] = implode(',', $aIdSuperadas);
        $aOperador['id_situacion'] = 'IN';
        $aWhere['acta'] = $acta;
        $aWhere['tipo_acta'] = PersonaNota::FORMATO_ACTA;

        $GesPersonaNotas = new GestorPersonaNotaDB();
        return $GesPersonaNotas->getPersonaNotas($aWhere, $aOperador);
    }

}