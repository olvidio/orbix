<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use dossiers\model\entity\TipoDossier;
use dossiers\model\PermisoDossier;
use web\Hash;
use function core\is_true;

/**
 * Página de visualización de los permisos de los dossiers
 * Le llegan las variables $tipo y $id_tipo
 *
 * Tiene include de ficha.php la cual permite guardar en la tabla
 * d_tipos_dossiers
 *
 * @package    delegacion
 * @subpackage    system
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qid_tipo_dossier = (integer)filter_input(INPUT_POST, 'id_tipo_dossier');

$a_dataUrl = array('tipo' => $Qtipo);
$go_to = Hash::link(ConfigGlobal::getWeb() . '/apps/dossiers/controller/perm_dossiers.php?' . http_build_query($a_dataUrl));

$url_update = "apps/dossiers/controller/perm_dossier_update.php";

$oTipoDossier = new TipoDossier(array('id_tipo_dossier' => $Qid_tipo_dossier));
$depende_modificar = $oTipoDossier->getDepende_modificar();

$botones = 0;
/*
1: guardar cambios
2: eliminar
*/
$perm_admin = FALSE;
if ($_SESSION['oPerm']->have_perm_oficina('admin_sv') || $_SESSION['oPerm']->have_perm_oficina('admin_sf')) {
    $botones = "1,2";
    $perm_admin = TRUE;
}

$oCuadros = new PermisoDossier();

$chk = (is_true($depende_modificar)) ? 'checked' : '';
$campos_chk = 'depende_modificar!permiso_lectura!permiso_escritura';

$oHash = new Hash();
$oHash->setCamposForm('id_tipo_dossier!id_tipo_dossier_rel!tabla_from!tabla_to!campo_to!descripcion!app!class');
$oHash->setcamposNo('que!' . $campos_chk);
$a_camposHidden = array(
    'go_to' => $go_to,
    'campos_chk' => $campos_chk
);
$oHash->setArraycamposHidden($a_camposHidden);

$txt_eliminar = _("¿Está seguro que desea eliminar este dossier?");

$a_campos = [
    'oHash' => $oHash,
    'oCuadros' => $oCuadros,
    'url_update' => $url_update,
    'txt_eliminar' => $txt_eliminar,
    'perm_admin' => $perm_admin,
    'id_tipo_dossier' => $Qid_tipo_dossier,
    'descripcion' => $oTipoDossier->getDescripcion(),
    'tabla_from' => $oTipoDossier->getTabla_from(),
    'tabla_to' => $oTipoDossier->getTabla_to(),
    'campo_to' => $oTipoDossier->getCampo_to(),
    'id_tipo_dossier_rel' => $oTipoDossier->getId_tipo_dossier_rel(),
    'permiso_lectura' => $oTipoDossier->getPermiso_lectura(),
    'permiso_escritura' => $oTipoDossier->getPermiso_escritura(),
    'app' => $oTipoDossier->getApp(),
    'class' => $oTipoDossier->getClass(),
    'chk' => $chk,
    'botones' => $botones,
    'go_to' => $go_to,
];

$oView = new ViewPhtml('dossiers\controller');
$oView->renderizar('perm_dossier_pres.phtml', $a_campos);
