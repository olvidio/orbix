<?php

namespace notas\model;

use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDBRepositoryInterface;
use src\notas\domain\entity\PersonaNotaDB;

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
        $aWhere['tipo_acta'] = PersonaNotaDB::FORMATO_ACTA;

        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
        return $PersonaNotaDBRepository->getPersonaNotas($aWhere, $aOperador);
    }

}