<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionUbiDireccionRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\is_true;
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

// Clase auxiliar para usar el trait en contexto procedural
$repositoryProvider = new class {
    use ProvidesRepositories;

    public function getRepo(string $entityType): object
    {
        return $this->getRepository($entityType);
    }

    public function getRepoClass(string $entityType): string
    {
        return $this->getRepositoryClass($entityType);
    }

    public function getMet(string $entityType): string
    {
        return $this->getMetodo($entityType);
    }

    public function getDirRepoClass(string $entityType): string
    {
        return $this->getDireccionRepositoryClass($entityType);
    }
};

function getRepository(string $obj_pau): object
{
    global $repositoryProvider;
    return $repositoryProvider->getRepo($obj_pau);
}

function getRepositoryClass(string $obj_pau): string
{
    global $repositoryProvider;
    return $repositoryProvider->getRepoClass($obj_pau);
}

function getMetodo(string $obj_pau): string
{
    global $repositoryProvider;
    return $repositoryProvider->getMet($obj_pau);
}

function getDireccionRepositoryClass(string $obj_pau): string
{
    global $repositoryProvider;
    return $repositoryProvider->getDirRepoClass($obj_pau);
}

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
$sWhereD = (string)filter_input(INPUT_POST, 'sWhereD');
$sOperadorD = (string)filter_input(INPUT_POST, 'sOperadorD');
$metodo = (string)filter_input(INPUT_POST, 'metodo');
$titulo = (string)filter_input(INPUT_POST, 'titulo');
$Qcmb = (string)filter_input(INPUT_POST, 'cmb');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$tipo_ubi = $Qtipo . $Qloc;
// si es sf, el tipi_ubi = ctrsf
if ($tipo_ubi === 'ctrdl' && ConfigGlobal::mi_sfsv() == 2) {
    $tipo_ubi = 'ctrsf';
}

$Qnombre_ubi = '';
$Qdl = '';
$Qregion = '';
$repoDir = '';
$aWhere = [];
$aOperador = [];
$aWhereD = [];
$aOperadorD = [];
/*miro las condiciones. las variables son: nombre_ubi,ciudad,region,pais */
if (empty($sWhere)) {
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

    $permisoSf = ($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'));
    switch ($Qtipo) {
        case "ctr":
            switch ($Qloc) {
                case "dl":
                    $titulo = ucfirst(_("tabla de centros de la delegación"));
                    $Qobj_pau = 'CentroDl';
                    break;
                case "ex":
                    $titulo = ucfirst(_("tabla de centros de fuera de la delegación"));
                    $Qobj_pau = 'CentroEx';
                    break;
                case "sf":
                    if ($permisoSf) {
                        $titulo = ucfirst(_("tabla de centros de la delegación femenina"));
                        $Qobj_pau = 'CentroDl';
                    }
                    break;
                case "tot":
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    $Qobj_pau = 'Centro';
                    switch ($miSfsv) {
                        case 1: // sv
                            $aWhere['sv'] = 't';
                            break;
                        case 2: //sf
                            $aWhere['sf'] = 't';
                            break;
                    }
                    break;
            }
            break;
        case "cdc":
            switch ($Qloc) {
                case "dl":
                    $titulo = ucfirst(_("tabla de casas de la delegación"));
                    $Qobj_pau = 'CasaDl';
                    // Añado una condición para el caso de no poner, que salgan todos.
                    // Si no se pone, dice que hay que poner algún criterio de búsqueda
                    $aWhere['active'] = 't';
                    break;
                case "ex":
                    $titulo = ucfirst(_("tabla de casas de fuera de la delegación"));
                    $Qobj_pau = 'CasaEx';
                    break;
                case "sf":
                    if ($permisoSf) {
                        $titulo = ucfirst(_("tabla de casas de la sf"));
                        $Qobj_pau = 'CasaDl';
                        $aWhere['sf'] = 't';
                    }
                    break;
                case "tot":
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    $Qobj_pau = 'Casa';
                    break;
            }
            break;
        case "tot":
            switch ($Qloc) {
                case "dl":
                    $titulo = ucfirst(_("tabla de casas y centros de la delegación"));
                    $Qobj_pau = 'Centro';
                    break;
                case "ex":
                    $titulo = ucfirst(_("tabla de casas y centros de fuera de la delegación"));
                    $Qobj_pau = 'Centro';
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
                    if ($permisoSf) {
                        $titulo = ucfirst(_("tabla de toda las casas y centros"));
                        $Qobj_pau = 'Centro';
                        $aWhere['sf'] = 't';
                    }
                    break;
                case "tot":
                    $Qobj_pau = 'Centro';
                    $titulo = ucfirst(_("tabla de toda las casas y centros"));
                    break;
            }
            break;
    }

    if (!empty($Qobj_pau)) {
        $metodo = getMetodo($Qobj_pau);
        $repoDir = getDireccionRepositoryClass($Qobj_pau);
    }

} else {
    $aWhere = json_decode(core\urlsafe_b64decode($sWhere), true) ?: [];
    $aOperador = json_decode(core\urlsafe_b64decode($sOperador), true) ?: [];
    $aWhereD = json_decode(core\urlsafe_b64decode($sWhereD), true) ?: [];
    $aOperadorD = json_decode(core\urlsafe_b64decode($sOperadorD), true) ?: [];
    if (!empty($Qobj_pau)) {
        $metodo = getMetodo($Qobj_pau);
        $repoDir = getDireccionRepositoryClass($Qobj_pau);
    }
}

if (empty($aWhere) && empty($aWhereD)) {
    printf(_("debe poner algún criterio de búsqueda"));
    die();
}

// Buscar por nombre Centro/Casa
if (!empty($aWhere)) {
    if (!is_true($Qcmb)) {
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
    if (!empty($Qobj_pau)) {
        $metodo = getMetodo($Qobj_pau);
        $UbiRepository = getRepository($Qobj_pau);
        $cUbis = $UbiRepository->$metodo($aWhere, $aOperador);
    } else {
        $cUbis = [];
    }
} else {
    $cUbis = [];
}

// Buscar por dirección
$cUbisD = [];
if (!empty($aWhereD)) {
    if (!empty($repoDir)) {
        $DireccionesRepository = $GLOBALS['container']->get($repoDir);
        $cDirecciones = $DireccionesRepository->getDirecciones($aWhereD, $aOperadorD) ?: [];
        $repoRelacionMap = [
            DireccionCentroDlRepositoryInterface::class => RelacionCentroDlDireccionRepositoryInterface::class,
            DireccionCentroExRepositoryInterface::class => RelacionCentroExDireccionRepositoryInterface::class,
            DireccionCentroRepositoryInterface::class => RelacionCentroDireccionRepositoryInterface::class,
            DireccionCasaDlRepositoryInterface::class => RelacionCasaDlDireccionRepositoryInterface::class,
            DireccionCasaExRepositoryInterface::class => RelacionCasaExDireccionRepositoryInterface::class,
            DireccionCasaRepositoryInterface::class => RelacionCasaDireccionRepositoryInterface::class,
        ];
        $repoRelacion = $repoRelacionMap[$repoDir] ?? RelacionUbiDireccionRepositoryInterface::class;
        $RelacionRepository = $GLOBALS['container']->get($repoRelacion);
        foreach ($cDirecciones as $oDireccion) {
            $id_direccion = $oDireccion->getId_direccion();
            $cIdUbis = $RelacionRepository->getUbisPorDireccion($id_direccion);
            foreach ($cIdUbis as $aUbi) {
                $oUbi = Ubi::NewUbi($aUbi['id_ubi']);
                if ($oUbi === null) {
                    continue;
                }
                if (!is_true($Qcmb) && method_exists($oUbi, 'isActive') && !$oUbi->isActive()) {
                    continue;
                }
                $cUbisD[] = $oUbi;
            }
        }
    }
}

// Si hay las dos colecciones, hay que buscar la intersección.
$aUbisIntersec = [];
if (!empty($cUbis) && !empty($cUbisD)) {
    $aUbis = array_map(static fn($oUbi) => $oUbi->getId_ubi(), $cUbis);
    $aUbisD = array_map(static fn($oUbi) => $oUbi->getId_ubi(), $cUbisD);
    $aUbisIntersec = array_values(array_intersect($aUbis, $aUbisD));
} elseif (!empty($cUbisD)) {
    $cUbis = $cUbisD;
}


// para descartar duplicados y ordenar
$aUbis = [];
$aUbisIntersecLookup = !empty($aUbisIntersec) ? array_flip($aUbisIntersec) : [];
$cUbisTot = [];
$a_region = [];
$a_nom = [];
foreach ($cUbis as $key => $oUbi) {
    $id_ubi = $oUbi->getId_ubi();
    if (!empty($aUbisIntersecLookup) && !isset($aUbisIntersecLookup[$id_ubi])) {
        continue;
    }
    if (isset($aUbis[$id_ubi])) {
        continue;
    }
    $aUbis[$id_ubi] = true;
    $cUbisTot[$key] = $oUbi;
    $a_region[$key] = strtolower($oUbi->getRegion() ?? '');
    $a_nom[$key] = strtolower($oUbi->getNombre_ubi() ?? '');
}

$sWhere = urlsafe_b64encode(json_encode($aWhere), JSON_THROW_ON_ERROR);
$sOperador = urlsafe_b64encode(json_encode($aOperador), JSON_THROW_ON_ERROR);
$sWhereD = urlsafe_b64encode(json_encode($aWhereD), JSON_THROW_ON_ERROR);
$sOperadorD = urlsafe_b64encode(json_encode($aOperadorD), JSON_THROW_ON_ERROR);

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
    $a_link = array('obj_pau' => $Qobj_pau,
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
	$a_link = array('obj_pau' => $Qobj_pau,
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
    'obj_pau' => $Qobj_pau,
    'sWhereD' => $sWhereD,
    'sOperadorD' => $sOperadorD,
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
    if (is_array($cDirecciones) && !empty($cDirecciones)) {
        foreach ($cDirecciones as $oDireccion) {
            $poblacion = $oDireccion->getPoblacionVo()->value();
            $pais = $oDireccion->getPaisVo()?->value() ?? '';
            $direccion = $oDireccion->getDireccionVo()?->value() ?? '';
            $c_p = $oDireccion->getCodigoPostalVo()?->value() ?? '';
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
    'obj_pau' => $Qobj_pau,
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
