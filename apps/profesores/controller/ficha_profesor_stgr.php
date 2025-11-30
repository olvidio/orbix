<?php

use core\ConfigGlobal;
use dossiers\model\entity\TipoDossier;
use dossiers\model\PermDossier;
use personas\model\entity\Persona;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\profesores\domain\entity\ProfesorCongreso;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\profesores\domain\InfoProfesorAmpliacion;
use src\profesores\domain\InfoProfesorCongreso;
use src\profesores\domain\InfoProfesorDirector;
use src\profesores\domain\InfoProfesorDocenciaStgr;
use src\profesores\domain\InfoProfesorJuaramento;
use src\profesores\domain\InfoProfesorLatin;
use src\profesores\domain\InfoProfesorPublicacion;
use src\profesores\domain\InfoProfesorStgr;
use src\profesores\domain\InfoProfesorTituloEst;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use web\Hash;
use function core\is_true;

/**
 * Esta página sirve para la ficha de profesor del stgr.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        21/3/2007.
 *
 */

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom = (integer)strtok($a_sel[0], "#");
    $Qid_tabla = (string)strtok("#");
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_nom = empty($Qid_nom) ? $Qid_pau : $Qid_nom;
    $Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

// Sobre-escribe el scroll_id que se pueda tener
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
} else {
    $stack = '';
}
//Si vengo por medio de Posicion, borro la última
if ($stack != '') {
    // No me sirve el de global_object, sino el de la session
    $oPosicion2 = new web\Posicion();
    if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
        $Qid_sel = $oPosicion2->getParametro('id_sel');
        $Qscroll_id = $oPosicion2->getParametro('scroll_id');
        $oPosicion2->olvidar($stack);
    }
}

$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qdepende = (string)filter_input(INPUT_POST, 'depende');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qprint = (integer)filter_input(INPUT_POST, 'print');

// No uso el que viene por POST porque en la lista de personas si se 
// cambia el permiso también afecta a otros botones.
if ($_SESSION['oPerm']->have_perm_oficina('est')) {
    $Qpermiso = 3;
}


// Para cr stgr
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $Qprint = 1;
}

/*
// Permisos por dossiers:
1012 | stgr: publicaciones                     | d_publicaciones
1017 | stgr: titulos                           | d_titulo_est
1018 | stgr: nombramientos profesor            | d_profesor_stgr
1019 | stgr: ampliacion docencia               | d_profesor_ampliacion
1020 | stgr: director departamento             | d_profesor_director 
1021 | stgr: juramento                         | d_profesor_juramento
1022 | stgr: latin                             | d_profesor_latin
1024 | stgr: congresos                         | d_congresos
1025 | stgr: docencia                          | d_docencia_stgr
*/

$a_tipos_dossier = [
    1012 => 'publicaciones',
    1017 => 'curriculum',
    1018 => 'nombramientos',
    1019 => 'ampliacion',
    1020 => 'director',
    1021 => 'juramento',
    1022 => 'latin',
    1024 => 'congresos',
    1025 => 'docencia',
];

foreach ($a_tipos_dossier as $id_tipo_dossier => $nom_dossier) {
    $oTipoDossier = new TipoDossier($id_tipo_dossier);
    $permiso_lectura = $oTipoDossier->getPermiso_lectura();
    $permiso_escritura = $oTipoDossier->getPermiso_escritura();
    $depende_modificar = $oTipoDossier->getDepende_modificar();
    $pau = 'p';

    $oPermDossier = new PermDossier();
    $perm_a = $oPermDossier->permiso($permiso_lectura, $permiso_escritura, $depende_modificar, $pau, $id_nom);
    // 1: no tiene permisos
    // 2: sólo lectura
    // 3: lectura y escritura
    $aPerm[$nom_dossier] = $perm_a;
}

$aWhere = array('id_nom' => $id_nom, '_ordre' => 'f_nombramiento');
$aOperador = [];
if (!empty($Qprint)) {
    $aWhere['f_cese'] = 'NULL';
    $aOperador['f_cese'] = 'IS NULL';
}

$num_txt = '';
$agd_txt = '';
$sacd_txt = '';
$latin_txt = '';
switch ($Qid_tabla) {
    case "n":
        $num_txt = "si";
        break;
    case "a":
        $agd_txt = "si";
        break;
    case "pn":
    case "pa":
        break;
}

$oPersona = Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
    $msg_err = "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}
$nom_ap = $oPersona->getNombreApellidosCrSin();
$sacd = $oPersona->getSacd();
$id_ctr = $oPersona->getid_ctr();

if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $oCentroDl = $GLOBALS['container']->get(CentroRepositoryInterface::class)->findById($id_ctr);
} else {
    $oCentroDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class)->findById($id_ctr);
}
$nombre_ubi = $oCentroDl->getNombre_ubi();

$go_to = Hash::link(ConfigGlobal::getWeb() . '/apps/profesores/controller/ficha_profesor_stgr.php?' . http_build_query(array('id_nom' => $id_nom, 'id_tabla' => $Qid_tabla, 'permiso' => $Qpermiso, 'depende' => $Qdepende)));

$latin = $GLOBALS['container']->get(ProfesorLatinRepositoryInterface::class)->findById($id_nom)?->isLatin();

$go_cosas['print'] = Hash::link(ConfigGlobal::getWeb() . '/apps/profesores/controller/ficha_profesor_stgr.php?' . http_build_query(array('id_nom' => $id_nom, 'id_tabla' => $Qid_tabla, 'print' => '1')));

$a_cosas = array('clase_info' => InfoProfesorLatin::class,
    'pau' => 'p',
    'id_pau' => $id_nom,
    'obj_pau' => $Qobj_pau,
    'permiso' => $Qpermiso,
    'depende' => $Qdepende,
    'go_to' => $go_to);
$go_cosas['latin'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

if (is_true($sacd)) {
    $sacd_txt = "si";
}
if (is_true($latin)) {
    $latin_txt = "si";
}

$ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
$cProfesores = $ProfesorRepository->getProfesoresStgr($aWhere, $aOperador);
$DepartamentoRepository = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);
$a_nombramientos = [];
$dep = '';
$ProfesorTipoRepository = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
foreach ($cProfesores as $oProfesor) {
    $id_departamento = $oProfesor->getId_departamento();
    $escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
    $f_nombramiento = $oProfesor->getF_nombramiento()->getFromLocal();
    $f_cese = $oProfesor->getF_cese()->getFromLocal();
    $escrito_cese = $oProfesor->getEscrito_cese();
    $id_tipo_profesor = $oProfesor->getId_tipo_profesor();

    $departamento = $DepartamentoRepository->findById($id_departamento)->getNombreDepartamentoVo()->value();

    $oProfesorTipo = $ProfesorTipoRepository->findById($id_tipo_profesor);
    $tipo_profesor = $oProfesorTipo->getTipo_profesor();

    $a_nombramientos[] = array('departamento' => $departamento, 'tipo_profesor' => $tipo_profesor, 'f_nombramiento' => $f_nombramiento, 'escrito_nombramiento' => $escrito_nombramiento);
    if (empty($f_cese)) {
        $dep .= empty($dep) ? '' : '; ';
        $dep .= $departamento;
    }
}

if (empty($Qprint)) { // si no es para imprimir muestro todos los datos
    // director departamento //////////////////////////////////
    $ProfesorDirectorRepository = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
    $cDirectores = $ProfesorDirectorRepository->getProfesorDirectores($aWhere, $aOperador);
    $a_director = [];
    foreach ($cDirectores as $oProfesorDirector) {
        $id_departamento = $oProfesorDirector->getId_departamento();
        $escrito_nombramiento = $oProfesorDirector->getEscrito_nombramiento();
        $f_nombramiento = $oProfesorDirector->getF_nombramiento()->getFromLocal();
        $f_cese = $oProfesorDirector->getF_cese()->getFromLocal();
        $escrito_cese = $oProfesorDirector->getEscrito_cese();

        $departamento = $DepartamentoRepository->findById($id_departamento)->getNombreDepartamentoVo()->value();

        $a_director[] = array('departamento' => $departamento, 'f_nombramiento' => $f_nombramiento, 'escrito_nombramiento' => $escrito_nombramiento, 'f_cese' => $f_cese, 'escrito_cese' => $escrito_cese);
    }
    $a_cosas['clase_info'] = InfoProfesorDirector::class;
    $go_cosas['director'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

    // juramento //////////////////////////
    $JuramentoRepository = $GLOBALS['container']->get(ProfesorJuramentoRepositoryInterface::class);
    $cJuramento = $JuramentoRepository->getProfesorJuramentos(['id_nom' => $id_nom]);
    if (!empty($cJuramento[0])) {
        $oJuramento = $cJuramento[0];
        $f_juramento = $oJuramento->getF_juramento()->getFromLocal();
    } else {
        $f_juramento = '';
    }

    $a_cosas['clase_info'] = InfoProfesorJuaramento::class;
    $go_cosas['juramento'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

    //publicaciones  ///////////////////////////////////
    $ProfesorPublicacionRepository = $GLOBALS['container']->get(ProfesorPublicacionRepositoryInterface::class);
    $cProfesorPublicaciones = $ProfesorPublicacionRepository->getProfesorPublicaciones(array('id_nom' => $id_nom, '_ordre' => 'f_publicacion'));
    $a_publicaciones = [];
    foreach ($cProfesorPublicaciones as $oProfesorPublicacion) {
        $pendiente = $oProfesorPublicacion->getPendiente();
        $tipo_publicacion = $oProfesorPublicacion->getTipo_publicacion();
        $titulo = $oProfesorPublicacion->getTitulo();
        $editorial = $oProfesorPublicacion->getEditorial();
        $coleccion = $oProfesorPublicacion->getColeccion();
        $f_publicacion = $oProfesorPublicacion->getF_publicacion()->getFromLocal();
        $referencia = $oProfesorPublicacion->getReferencia();
        $lugar = $oProfesorPublicacion->getLugar();
        $observ = $oProfesorPublicacion->getObserv();

        $a_publicaciones[] = array('pendiente' => $pendiente, 'tipo_publicacion' => $tipo_publicacion, 'titulo' => $titulo, 'editorial' => $editorial, 'coleccion' => $coleccion, 'f_publicacion' => $f_publicacion, 'referencia' => $referencia, 'lugar' => $lugar, 'observ' => $observ);

    }
    $a_cosas['clase_info'] = InfoProfesorPublicacion::class;
    $go_cosas['publicaciones'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));
}

// Curriculum  ///////////////////
$ProfesorTituloEstRepository = $GLOBALS['container']->get(ProfesorTituloEstRepositoryInterface::class);
$cTitulosEst = $ProfesorTituloEstRepository->getProfesorTitulosEst(array('id_nom' => $id_nom, '_ordre' => 'year'));
$a_curriculum = [];
foreach ($cTitulosEst as $oProfesorTituloEst) {
    $eclesiastico = $oProfesorTituloEst->isEclesiastico();
    $titulo = $oProfesorTituloEst->getTitulo();
    $centro_dnt = $oProfesorTituloEst->getCentro_dnt();
    $year = $oProfesorTituloEst->getYear();

    $a_curriculum[] = array('eclesiastico' => $eclesiastico, 'titulo' => $titulo, 'centro_dnt' => $centro_dnt, 'year' => $year);
}
$a_cosas['clase_info'] = InfoProfesorTituloEst::class;
$go_cosas['curriculum'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

// Nombramientos  ///////////////////////////
$ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
$cProfesores = $ProfesorRepository->getProfesoresStgr($aWhere, $aOperador);
$a_nombramientos = [];
$id_departamento = '';
$ProfesorTipoRepository = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
foreach ($cProfesores as $oProfesor) {
    $id_departamento = $oProfesor->getId_departamento();
    $escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
    $f_nombramiento = $oProfesor->getF_nombramiento()->getFromLocal();
    $escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
    $f_cese = $oProfesor->getF_cese()->getFromLocal();
    $escrito_cese = $oProfesor->getEscrito_cese();
    $id_tipo_profesor = $oProfesor->getId_tipo_profesor();

    $departamento = $DepartamentoRepository->findById($id_departamento)->getNombreDepartamentoVo()->value();

    $oProfesorTipo = $ProfesorTipoRepository->findById($id_tipo_profesor);
    $tipo_profesor = $oProfesorTipo->getTipo_profesor();

    $a_nombramientos[] = array('departamento' => $departamento, 'tipo_profesor' => $tipo_profesor, 'f_nombramiento' => $f_nombramiento, 'escrito_nombramiento' => $escrito_nombramiento, 'f_cese' => $f_cese, 'escrito_cese' => $escrito_cese);
}
$a_cosas['clase_info'] = InfoProfesorStgr::class;
$go_cosas['nombramientos'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

// Ampliación docencia  ///////////////////
$ProfesorAmpliacionRepository = $GLOBALS['container']->get(ProfesorAmpliacionRepositoryInterface::class);
$cProfesorAmpliaciones = $ProfesorAmpliacionRepository->getProfesorAmpliaciones($aWhere, $aOperador);
$a_ampliacion = [];
$id_departamento = '';
$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
foreach ($cProfesorAmpliaciones as $oProfesorAmpliacion) {
    $id_asignatura = $oProfesorAmpliacion->getId_asignatura();
    $f_nombramiento = $oProfesorAmpliacion->getF_nombramiento()->getFromLocal();
    $escrito_nombramiento = $oProfesorAmpliacion->getEscrito_nombramiento();
    $f_cese = $oProfesorAmpliacion->getF_cese()->getFromLocal();
    $escrito_cese = $oProfesorAmpliacion->getEscrito_cese();

    $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
    if ($oAsignatura === null) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();


    $a_ampliacion[] = array('nombre_corto' => $nombre_corto, 'f_nombramiento' => $f_nombramiento, 'escrito_nombramiento' => $escrito_nombramiento, 'f_cese' => $f_cese, 'escrito_cese' => $escrito_cese);
}
$a_cosas['clase_info'] = InfoProfesorAmpliacion::class;
$go_cosas['ampliacion'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

// Convivencias y congresos  //////////////////////////////
$ProfesorCongresoReposiroty = $GLOBALS['container']->get(ProfesorCongresoRepositoryInterface::class);
$a_tipos_congreso = ProfesorCongreso::getTiposCongreso();
$cProfesorCongresos = $ProfesorCongresoReposiroty->getProfesorCongresos(array('id_nom' => $id_nom, '_ordre' => 'f_ini'));
$a_cosas['clase_info'] = InfoProfesorCongreso::class;
$go_cosas['congresos'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

// Actividad docente  ////////////////////////////////////
$ProfesorDocenciaStgr = $GLOBALS['container']->get(ProfesorDocenciaStgrRepositoryInterface::class);
$a_tipos_docencia = ProfesorDocenciaStgr::getTiposActividad();
$cDocencias = $ProfesorDocenciaStgr->getProfesorDocenciasStgr(array('id_nom' => $id_nom, '_ordre' => 'curso_inicio,id_asignatura'));
$a_cosas['clase_info'] = InfoProfesorDocenciaStgr::class;
$go_cosas['docencia'] = Hash::link(ConfigGlobal::getWeb() . '/frontend/shared/controller/tablaDB_lista_ver.php?' . http_build_query($a_cosas));

echo $oPosicion->mostrar_left_slide(1);

if (!empty($Qprint)) {
    include("../view/ficha_profesor_stgr.print.phtml");
} else {
    include("../view/ficha_profesor_stgr.phtml");
}
