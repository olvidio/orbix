<?php

namespace src\actividadestudios\domain;

use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;

/**
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/11/2016
 */
class PosiblesCa
{
    public function __construct(
        private PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }

    /**
     * @param array<int, array{nombre_asignatura: mixed, creditos: mixed}|string> $aAsignaturas
     * @return array{suma: int|float, lista: array<int, array{nombre_asignatura: mixed, creditos: mixed}>}
     */
    function contar_creditos(int $id_nom, array $aAsignaturas): array
    {
        $suma_creditos = 0;
        $aNotas = NotaSituacion::getArraySuperadas();
        $aSuperadas = [];
        foreach ($aNotas as $id_situacion) {
            $aSuperadas[$id_situacion] = 't';
        }
        $cPersonaNotas = $this->personaNotaRepository->getPersonaNotas(array('id_nom' => $id_nom));
        $num_opcionales = 0;
        $todas_asig_p = [];
        foreach ($cPersonaNotas as $oPersonaNota) {
            $id_situacion = $oPersonaNota->getIdSituacionVo()->value();
            $id_asignatura = $oPersonaNota->getIdAsignaturaVo()->value();
            $id_nivel = $oPersonaNota->getIdNivelVo()->value();
            if (array_key_exists($id_situacion, $aSuperadas)) {
                $todas_asig_p[] = $id_asignatura;
                // Apunto las opcionales a parte.
                $idNivelStr = (string) $id_nivel;
                if ((int)substr($idNivelStr, 0, 3) === 243 || (int)substr($idNivelStr, 0, 3) === 123) {
                    $num_opcionales++;
                }
            }
        }
        $aLista = [];
        foreach ($aAsignaturas as $id_asignatura => $datosAsignatura) {
            if (!is_array($datosAsignatura)) {
                continue;
            }
            $creditos = $datosAsignatura['creditos'];
            // Ojo con las opcionales
            if ($id_asignatura > 3000) {
                if ($num_opcionales >= 7) continue;
            }
            if (!in_array($id_asignatura, $todas_asig_p)) {
                $suma_creditos += is_numeric($creditos) ? (float) $creditos : 0;
                $aLista [$id_asignatura] = $datosAsignatura;
            }
        }
        $result = ['suma' => $suma_creditos, 'lista' => $aLista];
        return $result;
    }

}
