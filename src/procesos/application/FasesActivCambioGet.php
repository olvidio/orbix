<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use web\Desplegable;

/**
 * Caso de uso: devuelve el Desplegable con las fases posibles para el
 * id_tipo_activ actual y la dl_propia, con la opcion seleccionada por id_fase_sel.
 */
class FasesActivCambioGet
{
    public function execute(array $input): string
    {
        $Qid_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $Qdl_propia = (string)($input['dl_propia'] ?? '');
        $Qid_fase_sel = (string)($input['id_fase_sel'] ?? '');

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, $Qdl_propia);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $aOpciones = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos, true);
        $oDesplFasesIni = new Desplegable();
        $oDesplFasesIni->setBlanco(true);
        $oDesplFasesIni->setOpciones($aOpciones);
        $oDesplFasesIni->setNombre('id_fase_nueva');
        $oDesplFasesIni->setOpcion_sel($Qid_fase_sel);
        $oDesplFasesIni->setAction('fnjs_lista()');

        return $oDesplFasesIni->desplegable();
    }
}
