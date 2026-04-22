<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\value_objects\NivelStgrId;
use src\notas\application\AsignaturasPendientes;
use src\notas\domain\value_objects\CursoStgr;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;
use web\Lista;
use function core\is_true;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qnumero = (int)filter_input(INPUT_POST, 'numero');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$Qlista = (string)filter_input(INPUT_POST, 'lista');

if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (!is_true($Qpersonas_n) && !is_true($Qpersonas_agd)) {
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

$lista = is_true($Qlista);
$Pendientes = new AsignaturasPendientes($personas);
$aId_nom = $lista
    ? $Pendientes->listarFaltantesPorPersona($Qnumero, $curso)
    : $Pendientes->contarFaltantesPorPersona($Qnumero, $curso);

$a_botones = [
    ['txt' => _("modificar stgr"), 'click' => "fnjs_modificar(\"#seleccionados\")"],
    ['txt' => _("ver tessera"), 'click' => "fnjs_tesera(\"#seleccionados\")"],
];

$a_cabeceras = [
    ucfirst(_("tipo")),
    ['name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'],
    ucfirst(_("centro")),
    ucfirst(_("stgr")),
    ucfirst(_("asignaturas")),
    ['name' => _("telf."), 'width' => 80],
    ['name' => _("mails"), 'width' => 100],
];

$titulo = sprintf(
    _("lista de %s a los que faltan %d o menos asignaturas para finalizar el %s"),
    $gente,
    $Qnumero,
    $curso_txt
);

$i = 0;
$a_valores = [];
$repositoryProvider = new class {
    use ProvidesRepositories;

    public function get(string $entityType): object
    {
        return $this->getRepository($entityType);
    }
};

try {
    if ($obj_pau === 'PersonaDl') {
        $repoPersona = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
    } else {
        $repoPersona = $repositoryProvider->get($obj_pau);
    }
} catch (\InvalidArgumentException) {
    echo "No existe la clase de la persona";
    die();
}

$a_NivelStgr = NivelStgrId::getArrayNivelStgr();
$telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
$CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);

foreach ($aId_nom as $id_nom => $aAsignaturas) {
    $i++;
    $oPersona = $repoPersona->findById($id_nom);
    $id_tabla = $oPersona->getId_tabla();
    $nom = $oPersona->getPrefApellidosNombre();
    $nivel_stgr = $oPersona->getNivelStgrVo()->value();
    $stgr = $a_NivelStgr[$nivel_stgr];

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

    if ($lista) {
        $as = '';
        foreach ($aAsignaturas as $asig) {
            $as .= empty($as) ? '' : ' / ';
            $as .= $asig;
        }
    } else {
        $as = $aAsignaturas;
    }
    $a_valores[$i]['sel'] = "$id_nom#$id_tabla";
    $a_valores[$i][1] = $id_tabla;
    $a_valores[$i][2] = ['ira' => $pagina, 'valor' => $nom];
    $a_valores[$i][3] = $nombre_ubi;
    $a_valores[$i][4] = $stgr;
    $a_valores[$i][5] = $as;
    $a_valores[$i][6] = $telfs;
    $a_valores[$i][7] = $mails;
}
if (!empty($a_valores) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}

$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setArraycamposHidden(['pau' => 'p', 'obj_pau' => $obj_pau]);

$oTabla = new Lista();
$oTabla->setId_tabla('asig_faltan_select');
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
$oView->renderizar('asig_faltan_select.phtml', $a_campos);
