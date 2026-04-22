<?php

use src\notas\application\ActividadesBuscarData;
use src\notas\application\BuscarActaData;
use src\notas\application\PosiblesOpcionalesData;
use src\notas\application\PosiblesPreceptoresData;
use web\Desplegable;
use web\Hash;

/**
 * Shim AJAX consumido por `form_1011.phtml` y `form_1303.phtml`.
 *
 * @deprecated Cuando las vistas migren (slice 4), cada case se movera a
 * un endpoint propio bajo `src/notas/infrastructure/ui/http/controllers/`
 * que responda JSON con `ContestarJson`. De momento se preserva el
 * contrato historico (JSON literal para `buscar_acta`, HTML inline para
 * los demas casos), pero la logica de negocio ya vive en application
 * services reutilizables.
 */
require_once 'apps/core/global_header.inc';
require_once 'apps/core/global_object.inc';

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'buscar_acta':
        echo json_encode(BuscarActaData::execute($_POST), JSON_THROW_ON_ERROR);
        break;

    case 'frm_buscar':
        $datos = ActividadesBuscarData::execute($_POST);

        $oDesplDelegaciones = Desplegable::desdeOpciones($datos['delegaciones'], 'dl_org');
        $oDesplDelegaciones->setOpcion_sel($datos['dl_org_sel']);
        $oDesplDelegaciones->setAction('fnjs_buscar_ca()');

        $oDesplActividades = new Desplegable();
        $oDesplActividades->setOpciones($datos['actividades']);
        $oDesplActividades->setBlanco(1);
        $oDesplActividades->setNombre('id_activ_sel');
        $oDesplActividades->setOpcion_sel($datos['id_activ_sel']);

        $oHash = new Hash();
        $oHash->setCamposForm('pres_nom!pres_telf!pres_mail!zona!observ');
        $oHash->setCamposNo('scroll_id!sel');

        $txt = "<form id='frm_buscar'>";
        $txt .= '<h3>' . _("seleccionar la actividad") . '</h3>';
        $txt .= $oHash->getCamposHtml();
        $txt .= '<br>' . _("organniza") . '<br>' . $oDesplDelegaciones->desplegable();
        $txt .= '<br>' . _("actividad") . '<br>' . $oDesplActividades->desplegable();
        $txt .= '<br><br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_update_activ('#frm_buscar');\">";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\">";
        $txt .= '</form>';
        echo $txt;
        break;

    case 'posibles_opcionales':
        $aFaltan = PosiblesOpcionalesData::execute($_POST);
        $oDespl = new Desplegable();
        $oDespl->setNombre('id_asignatura');
        $oDespl->setOpciones($aFaltan);
        $oDespl->setBlanco(1);
        echo $oDespl->desplegable();
        break;

    case 'posibles_preceptores':
        $aProfesores = PosiblesPreceptoresData::execute();
        $oDespl = new Desplegable();
        $oDespl->setOpciones($aProfesores);
        $oDespl->setBlanco(1);
        $oDespl->setNombre('id_preceptor');
        echo $oDespl->desplegable();
        break;
}
