<?php

/**
 * Form de alta / edicion de la asistencia de una persona a una actividad.
 *
 * Sucesor de `apps/asistentes/controller/form_1301.php`. URL canonica del dossier
 * `actividades_de_una_persona` (id_tipo_dossier 1301).
 *
 * @param integer $_POST['id_pau']      id de la persona
 * @param string  $_POST['obj_pau']     clase de la persona
 * @param integer $_POST['id_dossier']  id_tipo_dossier de la vista padre
 * @param string  $_POST['mod']         nuevo|editar|eliminar
 * En caso de modificar:
 * @param integer $_POST['permiso']     1,2,3
 * @param integer $_POST['scroll_id']
 * @param array   $_POST['sel']         seleccion grid: id_activ
 * En caso de nuevo:
 * @param string  $_POST['que_dl']      dl propia o vacio para otras
 * @param integer $_POST['id_tipo']     filtro tipo_activ
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use web\Desplegable;
use web\Hash;
use function core\is_true;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qid_nom = (int) filter_input(INPUT_POST, 'id_pau');
$obj_pau = (string) filter_input(INPUT_POST, 'obj_pau');
$id_tipo = (string) filter_input(INPUT_POST, 'id_tipo');
$que_dl = (string) filter_input(INPUT_POST, 'que_dl');

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$id_activ = '';
if (!empty($a_sel)) {
    $id_activ = (int) strtok($a_sel[0], "#");
}

$oDesplActividades = [];
$oDesplegablePlaza = null;
$oDesplPosiblesPropietarios = null;
$h1 = '';
$url_ajax = '';
$nom_activ = '';

if (!empty($id_activ)) {
    $mod = "editar";
    $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    $oActividad = $ActividadAllRepository->findById($id_activ);
    $nom_activ = $oActividad->getNom_activ();

    $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
    $AsistenteRepositoryInterface = $AsistenteActividadService->getRepoAsistente($Qid_nom, $id_activ);
    $AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
    $oAsistente = $AsistenteRepository->findById($id_activ, $Qid_nom);
    $obj = get_class($oAsistente);

    $id_activ_real = $id_activ;
    $propio = $oAsistente->isPropio();
    $falta = $oAsistente->isFalta();
    $est_ok = $oAsistente->isEst_ok();
    $observ = $oAsistente->getObserv();
    $plaza = $oAsistente->getPlaza();
    $propietario = $oAsistente->getPropietario();

    if (ConfigGlobal::is_app_installed('actividadplazas')) {
        if (!empty($propietario)) {
            $padre = strtok($propietario, '>');
            $child = strtok('>');
            if ($obj_pau !== 'PersonaEx' && $child !== ConfigGlobal::mi_delef()) {
                exit(sprintf(_("los datos de asistencia los modifica el propietario de la plaza: %s"), $child));
            }
        }
    }
} else {
    $mod = "nuevo";
    $id_activ_real = '';
    if (empty($id_tipo)) {
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $id_tipo = '^' . $mi_sfsv;
    } else {
        $id_tipo = '^' . $id_tipo;
    }

    $condicion = "AND status = " . StatusId::ACTUAL;
    if (!empty($que_dl)) {
        $condicion .= " AND dl_org = '$que_dl'";
    } else {
        $condicion .= " AND dl_org != '" . ConfigGlobal::mi_delef() . "'";
    }

    $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
    $oOpciones = $ActividadRepository->getArrayActividadesDeTipo($id_tipo, $condicion);
    $oDesplActividades = new Desplegable();
    $oDesplActividades->setOpciones($oOpciones);
    $oDesplActividades->setNombre('id_activ');

    if (ConfigGlobal::is_app_installed('actividadplazas')) {
        $oDesplActividades->setAction('fnjs_cmb_propietario()');
    }

    $propio = "t";
    $falta = "f";
    $est_ok = "f";
    $observ = "";
    $plaza = PlazaId::PEDIDA;
    $propietario = '';
    $obj = 'AsistenteDl';
}
$propio_chk = (!empty($propio) && is_true($propio)) ? 'checked' : '';
$falta_chk = (!empty($falta) && is_true($falta)) ? 'checked' : '';
$est_chk = (!empty($est_ok) && is_true($est_ok)) ? 'checked' : '';

if (ConfigGlobal::is_app_installed('actividadplazas')) {
    $aOpciones = PlazaId::getArrayPosiblesPlazas();
    $oDesplegablePlaza = new Desplegable();
    $oDesplegablePlaza->setNombre('plaza');
    $oDesplegablePlaza->setOpciones($aOpciones);
    $oDesplegablePlaza->setOpcion_sel($plaza);

    $dl_de_paso = false;
    if ($obj_pau === 'PersonaEx' && !empty($Qid_nom)) {
        $PersonaExRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        $oPersona = $PersonaExRepository->findById($Qid_nom);
        $dl_de_paso = $oPersona->getDl();
    }
    $gesActividadPlazas = $GLOBALS['container']->get(ResumenPlazasService::class);
    if (!empty($id_activ)) {
        $gesActividadPlazas->setId_activ($id_activ);
        $oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
        $oDesplPosiblesPropietarios->setNombre('propietario');
        $oDesplPosiblesPropietarios->setOpcion_sel($propietario);
    } else {
        $oDesplPosiblesPropietarios = new Desplegable('propietario', [], '');
    }

    $url_ajax = rtrim(ConfigGlobal::getWeb(), '/') . '/src/actividadplazas/posibles_propietarios_data';
    $oHash1 = new Hash();
    $oHash1->setUrl($url_ajax);
    $oHash1->setCamposForm('id_activ!id_nom');
    $h1 = $oHash1->linkSinValParams();
}

$oHash = new Hash();
$camposForm = 'observ';
if (ConfigGlobal::is_app_installed('actividadplazas')) {
    $camposForm .= '!plaza!propietario';
}
$oHash->setCamposNo('propio!falta!est_ok');
$a_camposHidden = [
    'pau' => 'p',
    'id_nom' => $Qid_nom,
    'obj_pau' => $obj_pau,
    'mod' => $mod,
];
if (!empty($id_activ_real)) {
    $a_camposHidden['id_activ'] = $id_activ_real;
} else {
    $camposForm .= '!id_activ';
}
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_guardar = $web . '/src/asistentes/asistente_guardar';
$url_self = $web . '/frontend/asistentes/controller/form_actividades_de_una_persona.php';

$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h1' => $h1,
    'url_ajax' => $url_ajax,
    'url_guardar' => $url_guardar,
    'url_self' => $url_self,
    'id_nom' => $Qid_nom,
    'id_activ_real' => $id_activ_real,
    'nom_activ' => $nom_activ,
    'oDesplActividades' => $oDesplActividades,
    'propio_chk' => $propio_chk,
    'falta_chk' => $falta_chk,
    'est_chk' => $est_chk,
    'observ' => $observ,
    'oDesplegablePlaza' => $oDesplegablePlaza,
    'oDesplPosiblesPropietarios' => $oDesplPosiblesPropietarios,
];

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('form_actividades_de_una_persona.phtml', $a_campos);
