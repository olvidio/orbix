<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;

/**
 * Caso de uso: devuelve las fases posibles para el `id_tipo_activ` y la
 * `dl_propia` actual, incluyendo la opcion seleccionada por
 * `id_fase_sel`.
 *
 * Respuesta conforme al contrato de `refactor.md` para desplegables
 * (payload JSON con `id`, `opciones`, `selected`, `blanco`, `action`).
 * El frontend construye el `<select>` con el helper JS estandar.
 */
class FasesActivCambioGet
{
    /**
     * @return array{
     *     id:string,
     *     opciones:array<string,string>,
     *     selected:string,
     *     blanco:bool,
     *     action:string
     * }
     */
    public function execute(array $input): array
    {
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qdl_propia = (string)($input['dl_propia'] ?? '');
        $Qid_fase_sel = (string)($input['id_fase_sel'] ?? '');

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, $Qdl_propia);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $aOpciones = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos, true);

        return [
            'id' => 'id_fase_nueva',
            'opciones' => $aOpciones,
            'selected' => $Qid_fase_sel,
            'blanco' => true,
            'action' => 'fnjs_lista()',
        ];
    }
}
