<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\AsignaturasPendientes;
use src\notas\domain\value_objects\CursoStgr;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\is_true;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qid_asignatura = (int)filter_input(INPUT_POST, 'id_asignatura');
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$Qc2 = (string)filter_input(INPUT_POST, 'c2');

if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (empty($Qpersonas_n) && empty($Qpersonas_agd)) {
    exit(_("Debe marcar un grupo de personas (n o agd)"));
}

if ($Qb_c === 'b') {
    $curso = CursoStgr::BIENIO;
    $curso_txt = 'bienio';
} else {
    $c1 = is_true($Qc1);
    $c2 = is_true($Qc2);
    if (empty($Qc1) && empty($Qc2)) {
        $c1 = true;
        $c2 = true;
    }
    if ($c1 && $c2) {
        $curso = CursoStgr::CUADRIENIO;
        $curso_txt = 'cuadrienio';
    } elseif ($c2) {
        $curso = CursoStgr::C2;
        $curso_txt = 'cuadrienio años II-IV';
    } elseif ($c1) {
        $curso = CursoStgr::C1;
        $curso_txt = 'cuadrienio año I';
    }
}
if (is_true($Qpersonas_n)) {
    $personas = 'p_numerarios';
    $gente = 'numerarios';
    $obj_pau = 'PersonaN';
}
if (is_true($Qpersonas_agd)) {
    $personas = 'p_agregados';
    $gente = 'agregados';
    $obj_pau = 'PersonaAgd';
}
if (is_true($Qpersonas_n) && is_true($Qpersonas_agd)) {
    $personas = 'personas_dl';
    $gente = 'numerarios y agregados';
    $obj_pau = 'PersonaDl';
}

$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
$oAsignatura = $AsignaturaRepository->findById($Qid_asignatura);
if ($oAsignatura === null) {
    throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $Qid_asignatura));
}
$nom_asignatura = $oAsignatura->getNombre_corto();
$id_tipo_asignatura = $oAsignatura->getId_tipo(); // tipo 8 = OPCIONAL

$Pendientes = new AsignaturasPendientes($personas);
$aId_nom = $Pendientes->personasQueLesFaltaAsignatura($Qid_asignatura, $curso, $id_tipo_asignatura);

$a_botones = [
    ['txt' => _("modificar stgr"), 'click' => "fnjs_modificar(\"#seleccionados\")"],
    ['txt' => _("ver tessera"), 'click' => "fnjs_tesera(\"#seleccionados\")"],
];

$a_cabeceras = [
    ucfirst(_("tipo")),
    ['name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'],
    ucfirst(_("centro")),
    ucfirst(_("stgr")),
    ['name' => _("telf."), 'width' => 80],
    ['name' => _("mails"), 'width' => 100],
];

$i = 0;
$a_valores = [];
if (!empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
$PersonaFinderService = $GLOBALS['container']->get(PersonaFinderService::class);
$telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
$CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
foreach ($aId_nom as $id_nom => $aAsignaturas) {
    $oPersona = $PersonaFinderService->findPersonaEnDl($id_nom);
    if ($oPersona === null) {
        continue;
    }
    $i++;
    $id_tabla = $oPersona->getId_tabla();
    $stgr = $oPersona->getNivel_stgr();
    $nom = $oPersona->getPrefApellidosNombre();
    if (ConfigGlobal::mi_ambito() === 'rstgr') {
        $nombre_ubi = $oPersona->getDl();
    } else {
        $id_ctr = $oPersona->getId_ctr();
        $oCentroDl = $CentroDlRepository->findById($id_ctr);
        $nombre_ubi = $oCentroDl->getNombre_ubi();
    }

    $telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, "telf", " / ", "*", false);
    $telfs_movil = $telecoService->getTelecosPorTipo($id_nom, "móvil", " / ", "*", false);
    if (!empty($telfs_fijo) && !empty($telfs_movil)) {
        $telfs = $telfs_fijo . ' / ' . $telfs_movil;
    } else {
        $telfs = ($telfs_fijo ?? '') . ($telfs_movil ?? '');
    }
    $mails = $telecoService->getTelecosPorTipo($id_nom, "e-mail", " / ", "*", false);

    $pagina = Hash::link(ConfigGlobal::getWeb() . '/apps/personas/controller/home_persona.php?' . http_build_query(['id_nom' => $id_nom, 'obj_pau' => $obj_pau]));

    $a_valores[$i]['sel'] = "$id_nom#$id_tabla";
    $a_valores[$i][1] = $id_tabla;
    $a_valores[$i][2] = ['ira' => $pagina, 'valor' => $nom];
    $a_valores[$i][3] = $nombre_ubi;
    $a_valores[$i][4] = $stgr;
    $a_valores[$i][5] = $telfs;
    $a_valores[$i][6] = $mails;
}
if (!empty($a_valores) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}

$titulo = sprintf(_("lista de %s de %s a los que falta la asignatura %s (%s)"), $gente, $curso_txt, $nom_asignatura, $i);

$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setArraycamposHidden(['pau' => 'p', 'obj_pau' => $obj_pau]);

$oTabla = new Lista();
$oTabla->setId_tabla('asig_faltan_personas_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('asig_faltan_personas_select.phtml', $a_campos);
