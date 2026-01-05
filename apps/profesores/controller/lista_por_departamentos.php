<?php
/**
 * Esta página sirve para listar los profesores del stgr por departamentos.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        13/1/2017.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ViewPhtml;
use core\ViewTwig;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qfiltro = (integer)filter_input(INPUT_POST, 'filtro', FILTER_DEFAULT);


$rstgr = FALSE;
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $rstgr = TRUE;
}

if (ConfigGlobal::mi_ambito() === 'rstgr' && $Qfiltro !== 1) {

    $aChecked = $Qdl;
    $region_stgr = ConfigGlobal::mi_dele();
    $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
    $a_delegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$region_stgr]);

    $oCuadros = new Desplegable();
    $oCuadros->setNombre('dl');
    $oCuadros->setChecked($aChecked);
    $oCuadros->setOpciones($a_delegacionesStgr);

    $oHash = new Hash();
    $oHash->setCamposForm('dl');
    $camposNo = 'dl';
    $oHash->setcamposNo($camposNo);
    $oHash->setArrayCamposHidden(['filtro' => 1]);

    $url = 'apps/profesores/controller/lista_por_departamentos.php';
    $a_campos = [
        'oHash' => $oHash,
        'url' => $url,
        'boton_txt' => _("Aplicar filtro"),
        'oCuadros' => $oCuadros,
    ];

    $oView = new ViewTwig('ubis/controller');
    $oView->renderizar('dl_rstgr_que.html.twig', $a_campos);
    exit();
}

// tipos de profesores
$ProfesorTipoRepository = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
$cProfesorTipo = $ProfesorTipoRepository->getProfesorTipos();
$cTipoProfesor = [];
foreach ($cProfesorTipo as $oProfesorTipo) {
    $id_tipo = $oProfesorTipo->getId_tipo_profesor();
    $tipo = $oProfesorTipo->getTipo_profesor();
    $cTipoProfesor[$id_tipo] = $tipo;
}
//lista de departamentos.
$DepartamentoRepository = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);
$cDepartamentos = $DepartamentoRepository->getDepartamentos(['_ordre' => 'departamento']);


//por cada departamento:
// orden alfabético personas.
$aClaustro = [];
$ProfesorDirectorRepository = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
foreach ($cDepartamentos as $oDepartamento) {
    $id_departamento = $oDepartamento->getId_departamento();
    $departamento = $oDepartamento->getDepartamento();
    // director.
    $aWhere = ['id_departamento' => $id_departamento,
        'f_cese' => 'NULL',
    ];
    $aOperador = ['f_cese' => 'IS NULL'];
    if (!empty($Qdl)) {
        $dl_csv = implode(',', $Qdl);
        $aWhere['id_dl'] = $dl_csv;
        $aOperador['id_dl'] = 'IN';
        $aWhere['_ordre'] = 'id_dl';
    }

    $cProfesorDirector = $ProfesorDirectorRepository->getProfesoresDirectores($aWhere, $aOperador);
    $aProfesores = [];
    $aDirs = [];
    $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
    foreach ($cProfesorDirector as $oProfesorDirector) {
        $id_nom = $oProfesorDirector->getId_nom();
        $oPersonaDl = $PersonaDlRepository->findById($id_nom);
        if ($oPersonaDl === null) {
            continue;
        }
        if ($oPersonaDl->getSituacion() !== 'A') {
            continue;
        }
        $dl = $oPersonaDl->getDl();
        $ap_orden = $dl . '*' . $oPersonaDl->getApellido1() . $oPersonaDl->getApellido2() . $oPersonaDl->getNom();
        $ap_nom = $oPersonaDl->getPrefApellidosNombre() . " (" . $oPersonaDl->getCentro_o_dl() . ")";
        $aDirs[$ap_orden][$dl] = $ap_nom;
    }
    ksort($aDirs);
    $aProfesores['director'] = $aDirs;
    // tipo de profesor: ayudante, encargado...
    $ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
    foreach ($cTipoProfesor as $id_tipo => $tipo) {
        $aWhere = ['id_departamento' => $id_departamento,
            'id_tipo_profesor' => $id_tipo,
            'f_cese' => 'NULL',
        ];
        $aOperador = ['f_cese' => 'IS NULL'];
        if (!empty($Qdl)) {
            $dl_csv = implode(',', $Qdl);
            $aWhere['id_dl'] = $dl_csv;
            $aOperador['id_dl'] = 'IN';
            $aWhere['_ordre'] = 'id_dl';
        }

        $cProfesores = $ProfesorRepository->getProfesoresStgr($aWhere, $aOperador);
        $aProfes = [];
        foreach ($cProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $PersonaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            if ($oPersonaDl->getSituacion() !== 'A') {
                continue;
            }
            $dl = $oPersonaDl->getDl();
            $ap_orden = $dl . '*' . $oPersonaDl->getApellido1() . $oPersonaDl->getApellido2() . $oPersonaDl->getNom();
            $ap_nom = $oPersonaDl->getPrefApellidosNombre() . " (" . $oPersonaDl->getCentro_o_dl() . ")";
            $aProfes[$ap_orden][$dl] = $ap_nom;
        }
        ksort($aProfes);
        $aProfesores[$tipo] = $aProfes;
    }
    $aClaustro[] = array('id_departamento' => $id_departamento,
        'departamento' => $departamento,
        'profesores' => $aProfesores
    );
}

$a_campos = [
    'aClaustro' => $aClaustro,
    'rstgr' => $rstgr,
];

$oView = new ViewPhtml('profesores\controller');
$oView->renderizar('lista_por_departamentos.phtml', $a_campos);