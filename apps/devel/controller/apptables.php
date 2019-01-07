<?php
use devel\model\entity\GestorApp;

/**
 * La idea de esta página es poder crear y eliminar 
 * las tablas correspondientes a cada app.
 * Al activar un módulo, se debería crear las tablas en el esquema correspondiente,
 * pero por aqui se pueden grear en el esquema global y en otros.
 * 
 */ 


// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/**
 * copiada de permisos/controller/loguin_obj.php
 * @param string $default
 * @return boolean|string
 */
function posibles_esquemas($default='') {
    $txt = '';
    // Lista de posibles esquemas (en comun)
    $oConfig = new core\Config('comun');
    $config = $oConfig->getEsquema('public');
    $oConexion = new core\dbConnection($config);
    $oDBP = $oConexion->getPDO();
    
    $sQuery = "select nspname from pg_namespace where nspowner > 1000 ORDER BY nspname";
    if (($oDblSt = $oDBP->query($sQuery)) === false) {
        $sClauError = 'Schemas.lista';
        $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
        return false;
    }
    if (is_object($oDblSt)) {
        $oDblSt->execute();
        $txt = "<select id=\"esquema\" name=\"esquema\" >";
        foreach($oDblSt as $row) {
            if (!isset($row[1])) { $a = 0; } else { $a = 1; } // para el caso de sólo tener un valor.
            if ($row[0] == 'public') continue;
            if ($row[0] == 'resto') continue;
            if ($row[0] == 'global') continue;
            $sf = $row[0].'f';
            $sv = $row[0].'v';
            if (!empty($default) && $sf == $default) { $sel_sf = 'selected'; } else { $sel_sf = ''; }
            if (!empty($default) && $sv == $default) { $sel_sv = 'selected'; } else { $sel_sv = ''; }
            $txt .= "<option value=\"$sf\" $sel_sf>$sf</option>";
            $txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
        }
        $txt .= '</select>';
    }
    return $txt;
}

$oGesApps = new GestorApp();
$cApps = $oGesApps->getApps();
$a_apps = [];
foreach ($cApps as $oApp) {
    $id_app = $oApp->getId_app();
    $nom_app = $oApp->getNom();
    $a_apps[$id_app] = $nom_app;
}

$oDeslpApps = new web\Desplegable([],['_ordre' => 'id_app']);
$oDeslpApps->setNombre('id_app');
$oDeslpApps->setOpciones($a_apps);

$oHash = new web\Hash();
$oHash->setcamposForm('id_app!esquema');
$oHash->setcamposNo('accion');
$oHash->setArraycamposHidden(['accion' => 'x']);

$alerta = _("ojo es un modulo principal");
$a_campos = [
    'oHash' => $oHash,
    'oDesplApps' => $oDeslpApps,
    'alerta' => $alerta,
];
$esquema = core\ConfigGlobal::mi_region_dl();
$a_campos['oDesplEsquemas'] = posibles_esquemas($esquema);


$oView = new core\View('devel\controller');
echo $oView->render('apptables.phtml',$a_campos);