<?php

use actividades\model\entity\Actividad;
use actividadestudios\model\entity\GestorMatriculaDl;
use asignaturas\model\entity\Asignatura;
use personas\model\entity\Persona;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\is_true;

/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

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
$oPosicion->recordar();

$aviso = '';
$form = '';
$traslados = '';
if (!empty($traslados)) {
    // personas trasladadas con matriculas pendientes
    // Periodo??

} else {
    $gesMatriculasDl = new GestorMatriculaDl();
    $cMatriculasPendientes = $gesMatriculasDl->getMatriculasPendientes();
}

$titulo = _("lista de matrículas pendientes de poner nota");
$a_botones = array(
    array('txt' => _("ver asignaturas ca"), 'click' => "fnjs_ver_ca(this.form)"),
    array('txt' => _("borrar matrícula"), 'click' => "fnjs_borrar(this.form)")
);

$a_cabeceras = array(_("actividad"), _("asignatura"), _("alumno"), _("p"));

$i = 0;
$a_valores = array();
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
$msg_err = '';
foreach ($cMatriculasPendientes as $oMatricula) {
    $i++;
    $id_nom = $oMatricula->getId_nom();
    $id_activ = $oMatricula->getId_activ();
    $id_asignatura = $oMatricula->getId_asignatura();
    $preceptor = $oMatricula->getPreceptor();
    $preceptor = is_true($preceptor)? 'x' : '';

    //echo "id_activ: $id_activ<br>";
    //echo "id_asignatura: $id_asignatura<br>";

    $oActividad = new Actividad($id_activ);
    $nom_activ = $oActividad->getNom_activ();
    $oPersona = Persona::newPersona($id_nom);
    if (!is_object($oPersona)) {
        $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
        continue;
    }
    $apellidos_nombre = $oPersona->getPrefApellidosNombre();
    $oAsignatura = new Asignatura($id_asignatura);
    $nombre_corto = $oAsignatura->getNombre_corto();

    $a_valores[$i]['sel'] = "$id_activ#$id_asignatura#$id_nom";
    $a_valores[$i][1] = $nom_activ;
    $a_valores[$i][2] = $nombre_corto;
    $a_valores[$i][3] = $apellidos_nombre;
    $a_valores[$i][4] = $preceptor;
}


$oHash = new Hash();
$oHash->setCamposNo('sel!mod!pau!scroll_id');
$a_camposHidden = array(
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
);
$oHash->setArraycamposHidden($a_camposHidden);

if (!empty($msg_err)) {
    echo $msg_err;
}
echo $oPosicion->mostrar_left_slide(1);

?>
<script>
    fnjs_ver_ca = function (formulario, n) {
        rta = fnjs_solo_uno(formulario);
        if (rta == 1) {
            var mod = "#mod";
            //$(mod).val("editar");
            $("#pau").val("a");
            $(formulario).attr('action', "apps/dossiers/controller/dossiers_ver.php");
            fnjs_enviar_formulario(formulario, '#main');
        }
    }

    fnjs_borrar = function (formulario) {
        var mensaje;
        mensaje = "<?= _("¿Está seguro que desea borrar todas las matrículas seleccionadas?");?>";
        if (confirm(mensaje)) {
            var mod = "#mod";
            $(mod).val("eliminar");
            $(formulario).attr('action', "apps/actividadestudios/controller/update_3103.php");
            fnjs_enviar_formulario(formulario, '#main');
        }
    }
</script>
<h2 class=titulo><?= $titulo ?></h2>
<h3><?= $aviso; ?></h3>
<form id="seleccionados" name="seleccionados" action="" method="post">
    <?= $oHash->getCamposHtml(); ?>
    <input type="hidden" id="pau" name="pau" value="p">
    <input type="hidden" id="mod" name="mod" value="">
    <?php
    $oTabla = new Lista();
    $oTabla->setId_tabla('mtr_pdte');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setBotones($a_botones);
    $oTabla->setDatos($a_valores);
    echo $oTabla->mostrar_tabla();
    ?>
</form>
