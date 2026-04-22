<?php

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Calcula las asignaturas opcionales (3000-5000) que todavia puede
 * cursar una persona (no las tiene superadas).
 *
 * Devuelve un diccionario `[id_asignatura => nombre_corto]`.
 */
final class PosiblesOpcionalesData
{
    public static function execute(array $input): array
    {
        $id_nom = (int)($input['id_nom'] ?? 0);

        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $cOpcionales = $AsignaturaRepository->getAsignaturas(
            [
                'active' => 't',
                'id_nivel' => '3000,5000',
                '_ordre' => 'nombre_corto',
            ],
            ['id_nivel' => 'BETWEEN']
        );

        $aSuperadas = NotaSituacion::getArraySuperadas();
        $PersonaNotaRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $cOpSuperadas = $PersonaNotaRepository->getPersonaNotas(
            [
                'id_situacion' => implode(',', $aSuperadas),
                'id_nom' => $id_nom,
                'id_asignatura' => 3000,
            ],
            [
                'id_situacion' => 'IN',
                'id_asignatura' => '>',
            ]
        );

        $aOpSuperadas = [];
        foreach ($cOpSuperadas as $oPN) {
            $aOpSuperadas[$oPN->getId_asignatura()] = $oPN->getId_asignatura();
        }

        $aFaltan = [];
        foreach ($cOpcionales as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            if (array_key_exists($id_asignatura, $aOpSuperadas)) {
                continue;
            }
            $aFaltan[$id_asignatura] = $oAsignatura->getNombre_corto();
        }

        return $aFaltan;
    }
}
