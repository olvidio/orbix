<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\PeriodoQue;
use zonassacd\model\entity\GestorZona;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$aOpciones = array(
    'esta_semana' => _("esta semana"),
    'este_mes' => _("este mes"),
    'proxima_semana' => _("próxima semana de lunes a domingo"),
    'proximo_mes' => _("próximo mes natural"),
    'separador' => '---------',
    'otro' => _("otro")
);

$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel('este_mes');
$oFormP->setisDesplAnysVisible(FALSE);

$ohoy = new DateTimeLocal(date('Y-m-d'));
$shoy = $ohoy ->format('d/m/Y');

$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$id_nom_jefe = '';

$UsuarioRepository = new UsuarioRepository();
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = new RoleRepository();
$aRoles = $RoleRepository->getArrayRoles();

if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {

    if ($_SESSION['oConfig']->is_jefeCalendario()) {
        $id_nom_jefe = '';
    } else {
        $id_nom_jefe = $oMiUsuario->getId_pauAsString();
        if (empty($id_nom_jefe)) {
            exit(_("No tiene permiso para ver esta página"));
        }
    }
}

$oGestorZona = new GestorZona();
$aOpciones = $oGestorZona->getArrayZonas($id_nom_jefe);
$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($aOpciones);
$oDesplZonas->setBlanco(FALSE);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$a_Orden = array(
    'orden' => 'orden',
    'prioridad' => 'prioridad',
    'desc_enc' => 'alfabético',
);

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_Orden);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_cuadricula_zona()');

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaPeriodo = new Hash();
$oHashZonaPeriodo->setUrl($url_ver_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_periodo = $oHashZonaPeriodo->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplOrden' => $oDesplOrden,
    'oFormP' => $oFormP,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_periodo' => $h_zona_periodo,
];
 
$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_plan_de_misas.html.twig', $a_campos);