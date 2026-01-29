<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewTwig;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$id_nom_jefe = null;
$id_sacd = '';
$id_ubi = '';


$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$aRoles = $RoleRepository->getArrayRoles();
//echo $aRoles[$id_role];


$aCentros = [];


if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'Centro sv' || $aRoles[$id_role] === 'Centro sf')) {
    $id_ubi = $oMiUsuario->getId_pauAsString();
    $oCentro = Ubi::newUbi($id_ubi);
    $nombre_ubi = $oCentro->getNombreUbiVo()->value();
    $aCentros[$id_ubi] = $nombre_ubi;
    $aOpciones[-1] = 'centros encargos';
    $oDesplZonas = new Desplegable();
    $oDesplZonas->setOpciones($aOpciones);
    $oDesplZonas->setBlanco(FALSE);
    $oDesplZonas->setNombre('id_zona');
    $oDesplZonas->setAction('fnjs_buscar_plan_ctr()');
    $oDesplZonas->setOpcion_sel($Qid_zona);
}  else {
    if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
        if (!$_SESSION['oConfig']->is_jefeCalendario()) {
            $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
            if (empty($id_nom_jefe)) {
                exit(_("No tiene permiso para ver esta página"));
            }
        }

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $aOpciones = $ZonaRepository->getArrayZonas($id_nom_jefe);
        if ($Qid_zona === 0) {
//    $Qid_zona=array_key_first($aOpciones);
            $Qid_zona = -1;
        }
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
            $aOpciones[-1]='centros encargos';
        }
//foreach($aOpciones as $a1=>$a2)
//    echo 'opcion: '.$a1.'-'.$a2.'<br>';

        $oDesplZonas = new Desplegable();
        $oDesplZonas->setOpciones($aOpciones);
        $oDesplZonas->setBlanco(FALSE);
        $oDesplZonas->setNombre('id_zona');
        $oDesplZonas->setAction('fnjs_buscar_plan_ctr()');
        $oDesplZonas->setOpcion_sel($Qid_zona);

        if (isset($Qid_zona)) {
            if($Qid_zona>0) {
                $aWhere = [];
                $aWhere['status'] = 't';
                $aWhere['id_zona'] = $Qid_zona;
                $aWhere['_ordre'] = 'nombre_ubi';
                $CentroEllosRepository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
                $cCentrossv = $CentroEllosRepository->getCentros($aWhere);
                $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $cCentrosSf = $CentroEllasRepository->getCentros($aWhere);
                $cCentros = array_merge($cCentrossv, $cCentrosSf);
                foreach ($cCentros as $oCentro) {
                    $id_ubi = $oCentro->getId_ubi();
                    $nombre_ubi = $oCentro->getNombre_ubi();
                    $aCentros[$id_ubi] = $nombre_ubi;
                }
            } else {
                $id_sacd = $oMiUsuario->getCsvIdPauAsString();
                /* busco los datos del encargo que se tengan */
                $EncargosSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
                // No los personales:
                $aWhereES = [];
                $aOperadorES = [];
                $aWhereES['id_nom'] = $id_sacd;
                $aWhereES['f_fin'] = 'x';
                $aOperadorES['f_fin'] = 'IS NULL';
                $aWhereES['_ordre'] = 'modo, f_ini DESC';
                $cEncargosSacd1 = $EncargosSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

                $oF_hoy = new DateTimeLocal(date('Y-m-d')); //Hoy sólo fecha, no hora
                $hoy = $oF_hoy->getIso();

                $aWhereES['f_fin'] = "'$hoy'";
                $aOperadorES['f_fin'] = '>';
                $cEncargosSacd2 = $EncargosSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

                $cEncargosSacd = $cEncargosSacd1 + $cEncargosSacd2;

                $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
                foreach ($cEncargosSacd as $oEncargoSacd) {
                    $id_enc = $oEncargoSacd->getId_enc();

                    $oEncargo = $EncargoRepository->findById($id_enc);
                    $id_tipo_enc = $oEncargo->getId_tipo_enc();
            // Si es un encargo personal (7 o 4) me lo salto
                    if (substr($id_tipo_enc, 0, 1) <= 3) {
                        $id_ubi = $oEncargo->getId_ubi();
                        $oCentro = Ubi::newUbi($id_ubi);
//                $oCentro=new CentroDl($id_ubi);
                        $nombre_ubi = $oCentro->getNombre_ubi();
                        $aCentros[$id_ubi] = $nombre_ubi;
                    }
                }
            }
        }
    }
}

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones($aCentros);
if (isset($id_ubi)) {
    $oDesplCentros->setOpcion_sel($id_ubi);
}
$oDesplCentros->setAction('fnjs_ver_plan_ctr()');

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
$oFormP->setDesplPeriodosOpcion_sel('esta_semana');
$oFormP->setisDesplAnysVisible(FALSE);

$ohoy = new DateTimeLocal(date('Y-m-d'));
$shoy = $ohoy->format('d/m/Y');

$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$url_buscar_plan_ctr = 'apps/misas/controller/buscar_plan_ctr.php';
$oHashBuscarPlanCtr = new Hash();
$oHashBuscarPlanCtr->setUrl($url_buscar_plan_ctr);
$oHashBuscarPlanCtr->setCamposForm('id_zona');
$h_buscar_plan_ctr = $oHashBuscarPlanCtr->linkSinVal();

$url_ver_plan_ctr = 'apps/misas/controller/ver_plan_ctr.php';
$oHashPlanCtr = new Hash();
$oHashPlanCtr->setUrl($url_ver_plan_ctr);
$oHashPlanCtr->setCamposForm('id_zona!id_ubi!periodo!empiezamin!empiezamax');
$h_plan_ctr = $oHashPlanCtr->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplCentros' => $oDesplCentros,
    'id_ubi' => $id_ubi,
    'oFormP' => $oFormP,
    'url_buscar_plan_ctr' => $url_buscar_plan_ctr,
    'url_ver_plan_ctr' => $url_ver_plan_ctr,
    'h_plan_ctr' => $h_plan_ctr,
];

$oView = new ViewTwig('misas/controller');
if ($aRoles[$id_role] === 'p-sacd') {
    echo $oView->render('buscar_plan_ctr.html.twig', $a_campos);
}
if (strpos($aRoles[$id_role], 'Centro') !== false) {
    echo $oView->render('buscar_plan_un_ctr.html.twig', $a_campos);
}