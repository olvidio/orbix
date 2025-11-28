<?php

use core\ConfigGlobal;
use notas\model\AsignaturasPendientes;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;
use web\Lista;
use function core\is_true;

/**
 * Esta página muestra una tabla con las personas que cumplen con la condicion.
 *
 * Es llamado desde personas_que.php
 *
 * @package    delegacion
 * @subpackage    fichas
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qnumero = (int)filter_input(INPUT_POST, 'numero');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');

$Qlista = (string)filter_input(INPUT_POST, 'lista');
$Qlista = empty($Qlista) ? FALSE : TRUE;

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (empty($Qpersonas_n) && empty($Qpersonas_agd)) {
    exit (_("Debe marcar un grupo de personas (n o agd)"));
}
//miro las condiciones.
if ($Qb_c == 'b') {
    $curso = "bienio";
    $curso_txt = "bienio";
} else {
    // En caso no tener valores, pongo los dos.
    if (empty($Qc1) && empty($Qc2)) {
        $Qc1 = TRUE;
        $Qc2 = TRUE;
    }
    if ($Qc1 && $Qc2) {
        $curso = "cuadrienio";
        $curso_txt = "cuadrienio";
    } elseif (!empty($Qc2)) {
        $curso = "c2";
        $curso_txt = "cuadrienio años II-IV";
    } elseif (!empty($Qc1)) {
        $curso = "c1";
        $curso_txt = "cuadrienio año I";
    }
}
if (!empty($Qpersonas_n)) {
    $personas = "p_numerarios";
    $gente = "numerarios";
    $obj_pau = 'PersonaN';
}
if (!empty($Qpersonas_agd)) {
    $personas = "p_agregados";
    $gente = "agregados";
    $obj_pau = 'PersonaAgd';
}
if (!empty($Qpersonas_n) && !empty($Qpersonas_agd)) {
    $personas = "personas_dl";
    $gente = "numerarios y agregados";
    $obj_pau = 'PersonaDl';
}

$Pendientes = new AsignaturasPendientes($personas);
$Pendientes->setLista($Qlista);
$aId_nom = $Pendientes->personasQueLesFalta($Qnumero, $curso);

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
        'numero' => $Qnumero,
        'b_c' => $Qb_c,
        'c1' => $Qc1,
        'c2' => $Qc2,
        'lista' => $Qlista,
        'personas_n' => $Qpersonas_n,
        'personas_agd' => $Qpersonas_agd);
$oPosicion->setParametros($aGoBack, 1);

$a_botones = array(array('txt' => _("modificar stgr"), 'click' => "fnjs_modificar(\"#seleccionados\")"),
        array('txt' => _("ver tessera"), 'click' => "fnjs_tesera(\"#seleccionados\")")
);

$a_cabeceras = array(ucfirst(_("tipo")),
        array('name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'),
        ucfirst(_("centro")),
        ucfirst(_("stgr")),
        ucfirst(_("asignaturas")),
        array('name' => _("telf."), 'width' => 80),
        array('name' => _("mails"), 'width' => 100),
);

$titulo = sprintf(_("lista de %s a los que faltan %d o menos asignaturas para finalizar el %s"), $gente, $Qnumero, $curso_txt);

$i = 0;
$a_valores = [];
$obj = 'personas\\model\\entity\\' . $obj_pau;
foreach ($aId_nom as $id_nom => $aAsignaturas) {
    $i++;
    $oPersona = new $obj($id_nom);
    $id_tabla = $oPersona->getId_tabla();
    $stgr = $oPersona->getStgr();
    $nom = $oPersona->getPrefApellidosNombre();
    // El ctr
    // En el caso cr-stgr, interesa la dl
    if (ConfigGlobal::mi_ambito() === 'rstgr') {
        $nombre_ubi = $oPersona->getDl();
    } else {
        $id_ctr = $oPersona->getId_ctr();
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentroDl = $CentroDlRepository->findById($id_ctr);
        $nombre_ubi = $oCentroDl->getNombre_ubi();
    }

    // Añado los telf:
    $telfs = '';
    $telfs_fijo = $oPersona->telecos_persona($id_nom, "telf", " / ", "*", FALSE);
    $telfs_movil = $oPersona->telecos_persona($id_nom, "móvil", " / ", "*", FALSE);
    if (!empty($telfs_fijo) && !empty($telfs_movil)) {
        $telfs = $telfs_fijo . " / " . $telfs_movil;
    } else {
        $telfs .= $telfs_fijo ?? '';
        $telfs .= $telfs_movil ?? '';
    }
    $mails = $oPersona->telecos_persona($id_nom, "e-mail", " / ", "*", FALSE);

    $condicion_2 = "Where id_nom='" . $id_nom . "'";
    $condicion_2 = urlencode($condicion_2);
    $pagina = Hash::link(ConfigGlobal::getWeb() . '/apps/personas/controller/home_persona.php?' . http_build_query(array('id_nom' => $id_nom, 'obj_pau' => $obj_pau)));

    if (is_true($Qlista)) { //Hacer un listado de las asignaturas que le faltan
        $as = '';
        foreach ($aAsignaturas as $asig) {
            $as .= empty($as) ? '' : " / ";
            $as .= $asig;
        }
    } else {
        $as = $aAsignaturas;
    }
    $a_valores[$i]['sel'] = "$id_nom#$id_tabla";
    $a_valores[$i][1] = $id_tabla;
    $a_valores[$i][2] = array('ira' => $pagina, 'valor' => $nom);
    $a_valores[$i][3] = $nombre_ubi;
    $a_valores[$i][4] = $stgr;
    $a_valores[$i][5] = $as;
    $a_valores[$i][6] = "$telfs";
    $a_valores[$i][7] = "$mails";

}
if (!empty($a_valores)) {
    if (isset($Qid_sel) && !empty($Qid_sel)) {
        $a_valores['select'] = $Qid_sel;
    }
    if (isset($Qscroll_id) && !empty($Qscroll_id)) {
        $a_valores['scroll_id'] = $Qscroll_id;
    }
}


$oHash = new Hash();
$oHash->setCamposForm('sel!scroll_id');
$a_camposHidden = array(
        'pau' => 'p',
        'obj_pau' => $obj_pau,
);
$oHash->setArraycamposHidden($a_camposHidden);

/* ---------------------------------- html --------------------------------------- */
?>
<script>
    fnjs_tesera = function (formulario) {
        rta = fnjs_solo_uno(formulario);
        if (rta == 1) {
            $(formulario).attr('action', "apps/notas/controller/tessera_ver.php");
            fnjs_enviar_formulario(formulario);
        }
    }

    fnjs_modificar = function (formulario) {
        rta = fnjs_solo_uno(formulario);
        if (rta == 1) {
            $(formulario).attr('action', "apps/personas/controller/stgr_cambio.php");
            fnjs_enviar_formulario(formulario);
        }
    }

</script>
<?= $oPosicion->mostrar_left_slide(1) ?>
<h2 class=titulo><?= $titulo ?></h2>
<form id='seleccionados' name='seleccionados' action='' method='post'>
    <?= $oHash->getCamposHtml(); ?>
    <?php
    $oTabla = new Lista();
    $oTabla->setId_tabla('asig_faltan_select');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setBotones($a_botones);
    $oTabla->setDatos($a_valores);
    echo $oTabla->mostrar_tabla();
    ?>
</form>