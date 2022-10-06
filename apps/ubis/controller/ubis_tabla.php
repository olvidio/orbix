<?php

use core\ConfigGlobal;
use usuarios\model\entity as usuarios;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\urlsafe_b64decode;
use function core\urlsafe_b64encode;
use function core\strtoupper_dlb;

/**
 * Esta página muestra una tabla con los ubis seleccionados.
 *
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oPosicion->recordar();


$oMiUsuario = new usuarios\Usuario(ConfigGlobal::mi_id_usuario());
$miSfsv = ConfigGlobal::mi_sfsv();

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qloc = (string)filter_input(INPUT_POST, 'loc');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qsimple = (integer)filter_input(INPUT_POST, 'simple');
if ($Qsimple == 1) {
    $Qtipo = 'tot';
    $Qloc = 'tot';
}
$sWhere = (string)filter_input(INPUT_POST, 'sWhere');
$sOperador = (string)filter_input(INPUT_POST, 'sOperador');
$sGestor = (string)filter_input(INPUT_POST, 'sGestor');
$sWhereD = (string)filter_input(INPUT_POST, 'sWhereD');
$sOperadorD = (string)filter_input(INPUT_POST, 'sOperadorD');
$sGestorDir = (string)filter_input(INPUT_POST, 'sGestorDir');
$metodo = (string)filter_input(INPUT_POST, 'metodo');
$titulo = (string)filter_input(INPUT_POST, 'titulo');
$Qcmb = (string)filter_input(INPUT_POST, 'cmb');

$tipo_ubi = $Qtipo . $Qloc;
// si es sf, el tipi_ubi = ctrsf
if ($tipo_ubi == 'ctrdl' && ConfigGlobal::mi_sfsv() == 2) {
    $tipo_ubi = 'ctrsf';
}

$Qnombre_ubi = '';
$Qdl = '';
$Qregion = '';
/*miro las condiciones. las variables son: nombre_ubi,ciudad,region,pais */
if (empty($sWhere)) {
    $aWhere = array();
    $aOperador = array();
    $aWhereD = array();
    $aOperadorD = array();
    $Qnombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');
    if (!empty($Qnombre_ubi)) {
        $nom_ubi = str_replace("+", "\+", $Qnombre_ubi); // para los centros de la sss+
        $aWhere['nombre_ubi'] = $nom_ubi;
        $aOperador['nombre_ubi'] = 'sin_acentos';
        //$aWhere['_ordre'] = 'nombre_ubi';
        $aWhere['_ordre'] = 'tipo_ubi,nombre_ubi';
    }
    $Qregion = (string)filter_input(INPUT_POST, 'region');
    if (!empty($Qregion)) {
        $aWhere['region'] = $Qregion;
        $aWhere['_ordre'] = 'nombre_ubi';
    }
    $Qdl = (string)filter_input(INPUT_POST, 'dl');
    if (!empty($Qdl)) {
        $aWhere['dl'] = $Qdl;
        $aOperador['dl'] = 'sin_acentos';
        $aWhere['_ordre'] = 'dl';
    }
    $Qtipo_ctr = (string)filter_input(INPUT_POST, 'tipo_ctr');
    if (!empty($Qtipo_ctr)) {
        $aWhere['tipo_ctr'] = $Qtipo_ctr;
        $aOperador['tipo_ctr'] = 'sin_acentos';
        $aWhere['_ordre'] = 'tipo_ctr';
    }
    $Qtipo_casa = (string)filter_input(INPUT_POST, 'tipo_casa');
    if (!empty($Qtipo_casa)) {
        $aWhere['tipo_casa'] = $Qtipo_casa;
        $aOperador['tipo_casa'] = 'sin_acentos';
        $aWhere['_ordre'] = 'tipo_casa';
    }


    $Qciudad = (string)filter_input(INPUT_POST, 'ciudad');
    if (!empty($Qciudad)) {
        $aWhereD['poblacion'] = $Qciudad;
        $aOperadorD['poblacion'] = 'sin_acentos';
        $aWhereD['_ordre'] = 'poblacion';
    }
    $Qpais = (string)filter_input(INPUT_POST, 'pais');
    if (!empty($Qpais)) {
        $aWhereD['pais'] = $Qpais;
        $aOperadorD['pais'] = 'sin_acentos';
        $aWhereD['_ordre'] = 'pais';
    }

    switch ($Qtipo) {
        case "ctr":
            switch ($Qloc) {
                case "dl":
                    $titulo = ucfirst(_("tabla de centros de la delegación"));
                    $Gestor = "ubis\\model\\entity\\GestorCentroDl";
                    $metodo = 'getCentros';
                    $GestorDir = "ubis\\model\\entity\\GestorDireccionCtrDl";
                    // Añado una condición para el caso de no poner, que salgan todos.
                    // Si no se pone, dice que hay que poner algun criterio de busqueda
                    $aWhere['status'] = 't';
                    break;
                case "ex":
                    $Gestor = "ubis\\model\\entity\\GestorCentroEx";
                    $metodo = 'getCentros';
                    $GestorDir = 'ubis\model\entity\GestorDireccionCtrEx';
                    $titulo = ucfirst(_("tabla de centros de fuera de la delegación"));
                    break;
                case "sf":
                    if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                        $Gestor = "ubis\\model\\entity\\GestorCentroDl";
                        $Gestor->setoDbl($GLOBALS['oDBE']);
                        $metodo = 'getCentros';
                        $GestorDir = 'ubis\\model\\entity\\GestorDireccionCtrDl';
                        $titulo = ucfirst(_("tabla de centros de la delegación femenina"));
                    }
                    break;
                case "tot":
                    $Gestor = "ubis\\model\\entity\\GestorCentro";
                    $metodo = 'getCentros';
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    switch ($miSfsv) {
                        case 1: // sv
                            $aWhere['sv'] = 't';
                            $GestorDir = 'ubis\\model\\entity\\GestorDireccionCtr';
                            break;
                        case 2: //sf
                            $aWhere['sf'] = 't';
                            $GestorDir = 'GestorDireccionSf';
                            break;
                    }
                    break;
            }
            break;
        case "cdc":
            switch ($Qloc) {
                case "dl":
                    $Gestor = "ubis\\model\\entity\\GestorCasaDl";
                    $metodo = 'getCasas';
                    $titulo = ucfirst(_("tabla de casas de la delegación"));
                    $GestorDir = "ubis\\model\\entity\\GestorDireccionCdcDl"; // Las casas tienen las mismas direcciones que sv.
                    // Añado una condición para el caso de no poner, que salgan todos.
                    // Si no se pone, dice que hay que poner algun criterio de busqueda
                    $aWhere['status'] = 't';
                    break;
                case "ex":
                    $Gestor = "ubis\\model\\entity\\GestorCasaEx";
                    $metodo = 'getCasas';
                    $titulo = ucfirst(_("tabla de casas de fuera de la delegación"));
                    $GestorDir = "ubis\\model\\entity\\GestorDireccionCdcEx";
                    break;
                case "sf":
                    if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                        $Gestor = "ubis\\model\\entity\\GestorCasaDl";
                        $metodo = 'getCasas';
                        $GestorDir = "ubis\\model\\entity\\GestorDireccionCdcDl";
                        $aWhere['sf'] = 't';
                        $titulo = ucfirst(_("tabla de casas de la sf"));
                    }
                    break;
                case "tot":
                    $Gestor = "ubis\\model\\entity\\GestorCasa";
                    $metodo = 'getCasas';
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    $GestorDir = "ubis\\model\\entity\\GestorDireccionCdc";
                    break;
            }
            break;
        case "tot":
            switch ($Qloc) {
                case "dl":
                    $Gestor = "ubis\\model\\entity\\GestorUbi";
                    $metodo = 'getUbis';
                    $titulo = ucfirst(_("tabla de casas y centros de la delegación"));
                    $GestorDir = "ubis\\model\\entity\\GestorDireccion";
                    break;
                case "ex":
                    $Gestor = "ubis\\model\\entity\\GestorUbi";
                    $metodo = 'getUbis';
                    $titulo = ucfirst(_("tabla de casas y centros de fuera de la delegación"));
                    $GestorDir = "ubis\\model\\entity\\GestorDireccion";
                    /*
                    switch ($miSfsv) {
                        case 1: // sv
                            if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                                ///// FALTA ARREGLAR ESTO /////
                                //$cond="(u.dl!='".core\ConfigGlobal::$dele."' OR dl is null)";
                                $aWhere['dl']=core\ConfigGlobal::$dele;
                                $aWhere['sv']='t';
                                $aWhere['tipo_ubi']='ctrsf';
                                $aOperador['tipo_ubi']='!=';
                            } else {
                                $aWhere['dl']=core\ConfigGlobal::$dele;
                                $aOperador['dl']='!=';
                                $aWhere['sv']='t';
                                $aWhere['tipo_ubi']='ctrsf';
                                $aOperador['tipo_ubi']='!=';
                            }
                            break;
                        case 2:
                            $aWhere['dl']=core\ConfigGlobal::$dele;
                            $aOperador['dl']='!=';
                            $aWhere['sf']='t';
                            break;
                    }
                    */
                    break;
                case "sf":
                    /*
                    $Gestor= "ubis\\model\\entity\\GestorUbi";
                    $metodo = 'getUbis';
                    $titulo=ucfirst(_("tabla de toda las casas y centros"));
                    switch ($miSfsv) {
                        case 1: // sv
                            if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                                $aWhere['tipo_ubi']='ctrsf|cdcdl|cdcex';
                                $aOperador['tipo_ubi']='~';
                                $aWhere['sf']='t';
                            }
                            break;
                        case 2:
                            $aWhere['dl']=core\ConfigGlobal::$dele;
                            $aOperador['dl']='!=';
                            $aWhere['sf']='t';
                            break;
                    }
                    */
                    break;
                case "tot":
                    $Gestor = "ubis\\model\\entity\\GestorUbi";
                    $metodo = 'getUbis';
                    $GestorDir = "ubis\\model\\entity\\GestorDireccion";
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    break;
            }
            break;
    }

} else {
    $aWhere = unserialize(urlsafe_b64decode($sWhere));
    $aOperador = unserialize(urlsafe_b64decode($sOperador));
    $Gestor = unserialize(urlsafe_b64decode($sGestor));
    $aWhereD = unserialize(urlsafe_b64decode($sWhereD));
    $aOperadorD = unserialize(urlsafe_b64decode($sOperadorD));
    $GestorDir = unserialize(urlsafe_b64decode($sGestorDir));
    $metodo = $metodo;
}

if (empty($aWhere) && empty($aWhereD)) {
    printf(_("debe poner algún criterio de búsqueda"));
    die();
}

if (!empty($aWhere)) {
    if (empty($Qcmb)) {
        $aWhere['status'] = 't';
    }
    // En el caso de las casas, hay que distinguir. Lo pongo aqui
    //porque si no hay una condición where anterior, busca todas las casas/centros sf o sv
    switch ($miSfsv) {
        case 1: // sv
            $aWhere['sv'] = 't';
            break;
        case 2: //sf
            $aWhere['sf'] = 't';
            break;
    }
    $oUbisGes = new $Gestor;
    $cUbis = $oUbisGes->$metodo($aWhere, $aOperador);
} else {
    $cUbis = array();
}
if (!empty($aWhereD)) {
    $oDireccionesGes = new $GestorDir;
    $cDirecciones = $oDireccionesGes->getDirecciones($aWhereD, $aOperadorD);
    $cUbisD = array();
    foreach ($cDirecciones as $oDireccion) {
        $cUbisD = array_merge($cUbisD, $oDireccion->getUbis());
    }
}

// Si hay las dos colleciones, hay que buscar la interseccion.
$aUbisIntersec = array();
if (isset($cUbis) && is_array($cUbis) && count($cUbis) && isset($cUbisD) && is_array($cUbisD) && count($cUbisD)) {
    $aUbis = array();
    foreach ($cUbis as $key => $oUbi) {
        $id_ubi = $oUbi->getId_ubi();
        $aUbis[] = $id_ubi;
    }
    $aUbisD = array();
    foreach ($cUbisD as $key => $oUbi) {
        $id_ubi = $oUbi->getId_ubi();
        $aUbisD[] = $id_ubi;
    }
    foreach ($aUbis as $id_ubi) {
        if (in_array($id_ubi, $aUbisD)) {
            //me lo quedo
            $aUbisIntersec[] = $id_ubi;
        }
    }
} else {
    if (isset($cUbisD) && is_array($cUbisD) && count($cUbisD)) {
        $cUbis = $cUbisD;
    }
}


// para descartar duplicados y ordenar
$aUbis = array();
$cUbisTot = array();
$a_region = [];
$a_nom = [];
foreach ($cUbis as $key => $oUbi) {
    $id_ubi = $oUbi->getId_ubi();
    if (!empty($aUbisIntersec) && !in_array($id_ubi, $aUbisIntersec)) {
        continue;
    }
    if (in_array($id_ubi, $aUbis)) {
        continue;
    }
    $aUbis[] = $id_ubi;
    $cUbisTot[$key] = $oUbi;
    $a_region[$key] = strtolower($oUbi->getRegion());
    $a_nom[$key] = strtolower($oUbi->getNombre_ubi());
}

$sWhere = urlsafe_b64encode(serialize($aWhere));
$sOperador = urlsafe_b64encode(serialize($aOperador));
$sGestor = urlsafe_b64encode(serialize($Gestor));
$sWhereD = urlsafe_b64encode(serialize($aWhereD));
$sOperadorD = urlsafe_b64encode(serialize($aOperadorD));
$sGestorDir = urlsafe_b64encode(serialize($GestorDir));

//si no existe la ficha, hacer una nueva	
$nueva_ficha = '';
$pagina_link = '';
if ($Qtipo == "tot" || $Qloc == "tot") {
    if (is_array($cUbisTot) && count($cUbisTot) == 0) {
        $nueva_ficha = 'especificar';
        $pagina_link = web\Hash::link('apps/ubis/controller/ubis_buscar.php?' . http_build_query(array('simple' => '2')));
    }
} else {
    $nueva_ficha = 'nueva';
    $nombre_ubi = $Qnombre_ubi;
    $a_link = array('sGestor' => $sGestor,
        'tipo_ubi' => $tipo_ubi,
        'nombre_ubi' => $Qnombre_ubi,
        'nuevo' => 1,
        'dl' => $Qdl,
        'region' => $Qregion,
    );
    $pagina_link = Hash::link(ConfigGlobal::getWeb() . '/apps/ubis/controller/ubis_editar.php?' . http_build_query($a_link));
    if (is_array($cUbisTot) && count($cUbisTot) == 0) {
        $nueva_ficha = 'aviso';
    }
}
array_multisort($a_region, SORT_LOCALE_STRING, SORT_ASC, $a_nom, SORT_LOCALE_STRING, SORT_ASC, $cUbisTot);

/*
if (is_array($cUbisTot) && count($cUbisTot) == 0) {
	$nombre_ubi=$Qnombre_ubi;
	$a_link = array('sGestor' => $sGestor,
					'tipo_ubi' => $tipo_ubi,
					'nombre_ubi' => $Qnombre_ubi,
					'nuevo' => 1,
					'dl' => $Qdl,
					'region' => $Qregion,
					); 
	
	if ($Qtipo=="tot" || $Qloc=="tot") {
	    $pagina=web\Hash::link('apps/ubis/controller/ubis_buscar.php?'.http_build_query(array('simple'=>'2'))); 
	    ?>
	    <span style="font-size:large">
		<?= _("no existe este centro o casa"); ?>.<br>
		<br>
		<?= _("OJO!: para crear un centro/casa debe especificar el tipo de centro/casa") ?>.
		<br>
		<?= _("Para ello debe buscar a través de 'ver más opciones' definiendo el tipo y la localización distinto a 'todos'."); ?>
		<br>
	    </span>
	    <br>
	    <input id="b_mas" name="b_mas" TYPE="button" VALUE="<?= _("buscar otras opciones"); ?>" onclick="fnjs_update_div('#main','<?= $pagina ?>')" >
	    <?php
	} else {
        $pagina=Hash::link(ConfigGlobal::getWeb().'/apps/ubis/controller/ubis_editar.php?'.http_build_query($a_link));
	    ?>
	    <span style="font-size:large">
		<?= _("no existe este nombre de centro o casa. Puede crear una nueva ficha"); ?>.
	    </span>
	    <br>
	    <input id="b_mas" name="b_mas" TYPE="button" VALUE="<?= _("nuevo centro o casa"); ?>" onclick="fnjs_update_div('#main','<?= $pagina ?>')" >
		<?php
	}
	die();
} else {
	array_multisort($a_region,SORT_LOCALE_STRING, SORT_ASC,$a_nom,SORT_LOCALE_STRING, SORT_ASC, $cUbisTot);
}
*/

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
    'tipo' => $Qtipo,
    'loc' => $Qloc,
    'sWhere' => $sWhere,
    'sOperador' => $sOperador,
    'sGestor' => $sGestor,
    'sWhereD' => $sWhereD,
    'sOperadorD' => $sOperadorD,
    'sGestorDir' => $sGestorDir,
    'metodo' => $metodo,
    'titulo' => $titulo
);
$oPosicion->setParametros($aGoBack, 1);
//$oPosicion->recordar();

$a_botones = array(
    array('txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)")
);
if ($_SESSION['oPerm']->have_perm_oficina('scl')) {
    $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_borrar(this.form)");
}

$a_cabeceras = array(array('name' => ucfirst(_("nombre del centro")), 'formatter' => 'clickFormatter'),
    _("tipo"),
    _("dl"),
    ucfirst(_("región")),
    ucfirst(_("dirección")),
    _("cp"),
    ucfirst(_("ciudad"))
);

$a_valores = array();
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
$i = 0;
foreach ($cUbisTot as $oUbi) {
    $i++;
    $id_ubi = $oUbi->getId_ubi();
    $tipo_ubi = $oUbi->getTipo_ubi();
    $nombre_ubi = $oUbi->getNombre_ubi();
    $dl = $oUbi->getDl();
    $region = $oUbi->getRegion();

    $cDirecciones = $oUbi->getDirecciones();
    if (is_array($cDirecciones) & !empty($cDirecciones)) {
        foreach ($cDirecciones as $oDireccion) {
            $poblacion = $oDireccion->getPoblacion();
            $pais = $oDireccion->getPais();
            $direccion = $oDireccion->getDireccion();
            $c_p = $oDireccion->getC_p();
        }
    } else {
        $poblacion = '';
        $pais = '';
        $direccion = '';
        $c_p = '';
    }

    $pagina = Hash::link('apps/ubis/controller/home_ubis.php?' . http_build_query(array('pau' => 'u', 'id_ubi' => $id_ubi)));

    $a_valores[$i]['sel'] = $id_ubi;
    $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $nombre_ubi);
    $a_valores[$i][2] = $tipo_ubi;
    $a_valores[$i][3] = $dl;
    $a_valores[$i][4] = $region;
    $a_valores[$i][5] = $direccion;
    $a_valores[$i][6] = $c_p;
    $a_valores[$i][7] = $poblacion;
}

$oTabla = new Lista();
$oTabla->setId_tabla('ubis_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setcamposForm('!sel');
$oHash->setcamposNo('!scroll_id');
$a_camposHidden = array(
    'tipo' => $Qtipo,
    'loc' => $Qloc,
    'sWhere' => $sWhere,
    'sOperador' => $sOperador,
    'sGestor' => $sGestor,
    'metodo' => $metodo,
    'titulo' => $titulo
);
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = [
    'oHash' => $oHash,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'nueva_ficha' => $nueva_ficha,
    'pagina_link' => $pagina_link,
];

$oView = new core\View('ubis\controller');
echo $oView->render('ubis_tabla.phtml', $a_campos);
