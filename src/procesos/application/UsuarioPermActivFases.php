<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;

/**
 * Caso de uso: devuelve las opciones disponibles para el desplegable
 * `fase_ref[]` de la pantalla usuario_perm_activ, filtradas por el tipo
 * de actividad y la delegacion.
 */
class UsuarioPermActivFases
{
    /**
     * @return array{opciones:array<string,string>}
     */
    public static function execute(array $input): array
    {
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qdl_propia = (string)($input['dl_propia'] ?? '');

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, $Qdl_propia);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $aOpciones = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);

        return ['opciones' => $aOpciones];
    }
}
