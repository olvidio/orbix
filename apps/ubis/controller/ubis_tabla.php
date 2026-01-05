<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\urlsafe_b64encode;

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
if ($tipo_ubi === 'ctrdl' && ConfigGlobal::mi_sfsv() == 2) {
    $tipo_ubi = 'ctrsf';
}

$Qnombre_ubi = '';
$Qdl = '';
$Qregion = '';
/*miro las condiciones. las variables son: nombre_ubi,ciudad,region,pais */
if (empty($sWhere)) {
    $aWhere = [];
    $aOperador = [];
    $aWhereD = [];
    $aOperadorD = [];
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
                    $repoUbi = CentroDlRepositoryInterface::class;
                    $metodo = 'getCentros';
                    $repoDir = DireccionCentroDlRepositoryInterface::class;
                    // Añado una condición para el caso de no poner, que salgan todos.
                    // Si no se pone, dice que hay que poner algún criterio de búsqueda
                    $aWhere['active'] = 't';
                    break;
                case "ex":
                    $titulo = ucfirst(_("tabla de centros de fuera de la delegación"));
                    $repoUbi = CentroExRepositoryInterface::class;
                    $metodo = 'getCentros';
                    $repoDir = DireccionCentroExRepositoryInterface::class;
                    break;
                case "sf":
                    if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                        $titulo = ucfirst(_("tabla de centros de la delegación femenina"));
                        $repoUbi = CentroDlRepositoryInterface::class;
                        $metodo = 'getCentros';
                        $repoUbi->setoDbl($GLOBALS['oDBE']);
                        $repoDir = DireccionCentroDlRepositoryInterface::class;
                    }
                    break;
                case "tot":
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    $repoUbi = CentroRepositoryInterface::class;
                    $metodo = 'getCentros';
                    switch ($miSfsv) {
                        case 1: // sv
                            $aWhere['sv'] = 't';
                            $repoDir = DireccionCentroRepositoryInterface::class;
                            break;
                        case 2: //sf
                            $aWhere['sf'] = 't';
                            $repoDir = DireccionCentroSfRepositoryInterface::class;
                            break;
                    }
                    break;
            }
            break;
        case "cdc":
            switch ($Qloc) {
                case "dl":
                    $titulo = ucfirst(_("tabla de casas de la delegación"));
                    $repoUbi = CasaDlRepositoryInterface::class;
                    $metodo = 'getCasas';
                    $repoUbi = DireccionCasaDlRepositoryInterface::class;
                    // Añado una condición para el caso de no poner, que salgan todos.
                    // Si no se pone, dice que hay que poner algun criterio de busqueda
                    $aWhere['active'] = 't';
                    break;
                case "ex":
                    $titulo = ucfirst(_("tabla de casas de fuera de la delegación"));
                    $repoUbi = CasaExRepositoryInterface::class;
                    $metodo = 'getCasas';
                    $repoUbi = DireccionCasaExRepositoryInterface::class;
                    break;
                case "sf":
                    if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                        $titulo = ucfirst(_("tabla de casas de la sf"));
                        $repoUbi = CasaDlRepositoryInterface::class;
                        $metodo = 'getCasas';
                        $repoUbi = DireccionCasaDlRepositoryInterface::class;
                        $aWhere['sf'] = 't';
                    }
                    break;
                case "tot":
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    $repoUbi = CasaRepositoryInterface::class;
                    $metodo = 'getCasas';
                    $repoUbi = DireccionCasaRepositoryInterface::class;
                    break;
            }
            break;
        case "tot":
            switch ($Qloc) {
                case "dl":
                    $titulo = ucfirst(_("tabla de casas y centros de la delegación"));
                    $Gestor = "src\\ubis\\application\\repositories\\UbiRepository";
                    $metodo = 'getUbis';
                    $GestorDir = "src\\ubis\\application\\repositories\\DireccionRepository";
                    break;
                case "ex":
                    $Gestor = "src\\ubis\\application\\repositories\\UbiRepository";
                    $metodo = 'getUbis';
                    $titulo = ucfirst(_("tabla de casas y centros de fuera de la delegación"));
                    $GestorDir = "src\\ubis\\application\\repositories\\DireccionRepository";
                    /*
                    switch ($miSfsv) {
                        case 1: // sv
                            if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
                                ///// FALTA ARREGLAR ESTO /////
                                //$cond="(u.dl!='".ConfigGlobal::$dele."' OR dl is null)";
                                $aWhere['dl']=ConfigGlobal::$dele;
                                $aWhere['sv']='t';
                                $aWhere['tipo_ubi']='ctrsf';
                                $aOperador['tipo_ubi']='!=';
                            } else {
                                $aWhere['dl']=ConfigGlobal::$dele;
                                $aOperador['dl']='!=';
                                $aWhere['sv']='t';
                                $aWhere['tipo_ubi']='ctrsf';
                                $aOperador['tipo_ubi']='!=';
                            }
                            break;
                        case 2:
                            $aWhere['dl']=ConfigGlobal::$dele;
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
                            $aWhere['dl']=ConfigGlobal::$dele;
                            $aOperador['dl']='!=';
                            $aWhere['sf']='t';
                            break;
                    }
                    */
                    break;
                case "tot":
                    $Gestor = "src\\ubis\\application\\repositories\\UbiRepository";
                    $metodo = 'getUbis';
                    $GestorDir = "src\\ubis\\application\\repositories\\DireccionRepository";
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    break;
            }
            break;
    }

} else {
    $aWhere = json_decode(core\urlsafe_b64decode($sWhere));
    $aOperador = json_decode(core\urlsafe_b64decode($sOperador));
    $Gestor = json_decode(core\urlsafe_b64decode($sGestor));
    $aWhereD = json_decode(core\urlsafe_b64decode($sWhereD));
    $aOperadorD = json_decode(core\urlsafe_b64decode($sOperadorD));
    $GestorDir = json_decode(core\urlsafe_b64decode($sGestorDir));
}

if (empty($aWhere) && empty($aWhereD)) {
    printf(_("debe poner algún criterio de búsqueda"));
    die();
}

if (!empty($aWhere)) {
    if (empty($Qcmb)) {
        $aWhere['active'] = 't';
    }
    // En el caso de las casas, hay que distinguir. Lo pongo aquí
    //porque si no hay una condición where anterior, busca todas las casas/centros sf o sv
    switch ($miSfsv) {
        case 1: // sv
            $aWhere['sv'] = 't';
            break;
        case 2: //sf
            $aWhere['sf'] = 't';
            break;
    }
    $UbiRepository = $GLOBALS['container']->get($repoUbi);
    $cUbis = $UbiRepository->$metodo($aWhere, $aOperador);
} else {
    $cUbis = [];
}
if (!empty($aWhereD)) {
    $DireccioneRepository = $GLOBALS['container']->get($repoDir);
    $cDirecciones = $DireccioneRepository->getDirecciones($aWhereD, $aOperadorD);
    $cUbisD = [];
    foreach ($cDirecciones as $oDireccion) {
        array_push($cUbisD, ...$oDireccion->getUbis());
    }
}

// Si hay las dos colecciones, hay que buscar la intersección.
$aUbisIntersec = [];
if (isset($cUbis) && is_array($cUbis) && count($cUbis) && isset($cUbisD) && is_array($cUbisD) && count($cUbisD)) {
    $aUbis = [];
    foreach ($cUbis as $key => $oUbi) {
        $id_ubi = $oUbi->getId_ubi();
        $aUbis[] = $id_ubi;
    }
    $aUbisD = [];
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
$aUbis = [];
$cUbisTot = [];
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
    $a_region[$key] = strtolower($oUbi->getRegion() ?? '');
    $a_nom[$key] = strtolower($oUbi->getNombre_ubi() ?? '');
}

$sWhere = urlsafe_b64encode(json_encode($aWhere), JSON_THROW_ON_ERROR);
$sOperador = urlsafe_b64encode(json_encode($aOperador), JSON_THROW_ON_ERROR);
$sGestor = urlsafe_b64encode(json_encode($Gestor), JSON_THROW_ON_ERROR);
$sWhereD = urlsafe_b64encode(json_encode($aWhereD), JSON_THROW_ON_ERROR);
$sOperadorD = urlsafe_b64encode(json_encode($aOperadorD), JSON_THROW_ON_ERROR);
$sGestorDir = urlsafe_b64encode(json_encode($GestorDir), JSON_THROW_ON_ERROR);

//si no existe la ficha, hacer una nueva	
$nueva_ficha = '';
$pagina_link = '';
if ($Qtipo === "tot" || $Qloc === "tot") {
    if (is_array($cUbisTot) && count($cUbisTot) == 0) {
        $nueva_ficha = 'especificar';
        $pagina_link = Hash::link('apps/ubis/controller/ubis_buscar.php?' . http_build_query(array('simple' => '2')));
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
	    $pagina=Hash::link('apps/ubis/controller/ubis_buscar.php?'.http_build_query(array('simple'=>'2'))); 
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

$a_valores = [];
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
$oHash->setCamposForm('!sel');
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

$oView = new ViewPhtml('ubis\controller');
$oView->renderizar('ubis_tabla.phtml', $a_campos);
