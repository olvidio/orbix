<?php
/**
 * Esta página sirve para comprobar las notas de la tabla e_notas.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        22/11/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ViewPhtml;
use core\ViewTwig;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qfiltro = (integer)filter_input(INPUT_POST, 'filtro', FILTER_DEFAULT);

if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $rstgr = TRUE;
} else {
    $rstgr = FALSE;
}

$a_comprobar_n = ['id_tabla' => 'n'];
$a_comprobar_a = ['id_tabla' => 'a'];
$a_n_listado = ['lista' => 1];
$a_n_numeros = ['lista' => 0];
$a_agd_listado = ['lista' => 1];
$a_agd_numeros = ['lista' => 0];
$a_profesores_numeros = ['lista' => 0];
$a_profesores_listado = ['lista' => 1];

if (ConfigGlobal::mi_ambito() === 'rstgr' && $Qfiltro == 1) {

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

    if (!empty($Qdl)) {
        $a_comprobar_n = ['id_tabla' => 'n', 'dl' => $Qdl];
        $a_comprobar_a = ['id_tabla' => 'a', 'dl' => $Qdl];
        $a_n_listado = ['lista' => 1, 'dl' => $Qdl];
        $a_n_numeros = ['lista' => 0, 'dl' => $Qdl];
        $a_agd_listado = ['lista' => 1, 'dl' => $Qdl];
        $a_agd_numeros = ['lista' => 0, 'dl' => $Qdl];
        $a_profesores_numeros = ['lista' => 0, 'dl' => $Qdl];
        $a_profesores_listado = ['lista' => 1, 'dl' => $Qdl];
    }

}

$go['comprobar_n'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/comprobar_notas.php?' . http_build_query($a_comprobar_n));
$go['comprobar_a'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/comprobar_notas.php?' . http_build_query($a_comprobar_a));
$go['n_listado'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/informe_stgr_n.php?' . http_build_query($a_n_listado));
$go['n_numeros'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/informe_stgr_n.php?' . http_build_query($a_n_numeros));
$go['agd_listado'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/informe_stgr_agd.php?' . http_build_query($a_agd_listado));
$go['agd_numeros'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/informe_stgr_agd.php?' . http_build_query($a_agd_numeros));
$go['profesores_numeros'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/informe_stgr_profesores.php?' . http_build_query($a_profesores_numeros));
$go['profesores_listado'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/informe_stgr_profesores.php?' . http_build_query($a_profesores_listado));
$go['asig_faltan'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/asig_faltan_que.php');

$go['filtro'] = Hash::link(ConfigGlobal::getWeb() . '/apps/notas/controller/resumen_anual.php?' . http_build_query(array('filtro' => 1)));

if (ConfigGlobal::mi_ambito() === 'rstgr' && $Qfiltro == 1) {
    $url = 'apps/notas/controller/resumen_anual.php';
    $a_campos = [
        'oHash' => $oHash,
        'url' => $url,
        'boton_txt' => _("Aplicar filtro"),
        'oCuadros' => $oCuadros,
    ];

    $oView = new ViewTwig('ubis/controller');
    $oView->renderizar('dl_rstgr_que.html.twig', $a_campos);
}

if (ConfigGlobal::mi_ambito() !== 'rstgr' ||
    (ConfigGlobal::mi_ambito() === 'rstgr' && !($Qfiltro == 1 && empty($Qdl)))
) {
    $a_campos = [
        'go' => $go,
        'rstgr' => $rstgr,
    ];

    $oView = new ViewPhtml('notas\model');
    $oView->renderizar('resumen_anual.phtml', $a_campos);
}