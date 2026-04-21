<?php

use frontend\shared\PostRequest;

/**
 * Horario encargo sacd en ficha. Datos: `/src/encargossacd/horario_sacd_ver_data`
 * (ver {@see \src\encargossacd\application\EncargoSacdHorarioVerData}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qmod = (integer)filter_input(INPUT_POST, 'mod');
$Qfiltro_sacd = (string)filter_input(INPUT_POST, 'filtro_sacd');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');

/** @var array<string, mixed> $d */
$d = PostRequest::getDataFromUrl('/src/encargossacd/horario_sacd_ver_data', [
    'id_nom' => $Qid_nom,
    'id_enc' => $Qid_enc,
    'id_item' => $Qid_item,
    'desc_enc' => $Qdesc_enc,
]);

$ap_nom = (string)($d['ap_nom'] ?? '');
$titulo = (string)($d['titulo'] ?? '');
$id_item = (int)($d['id_item'] ?? 0);
$desc_enc = (string)($d['desc_enc'] ?? '');
$f_ini_iso = (string)($d['f_ini_iso'] ?? '');
$f_fin_iso = (string)($d['f_fin_iso'] ?? '');
$dia_ref = (string)($d['dia_ref'] ?? '');
$dia_num = (string)($d['dia_num'] ?? '');
$mas_menos = (string)($d['mas_menos'] ?? '');
$dia_inc = (string)($d['dia_inc'] ?? '');
$h_ini = (string)($d['h_ini'] ?? '');
$h_fin = (string)($d['h_fin'] ?? '');
$tiene_excepciones = !empty($d['tiene_excepciones']);
$dia = (string)($d['dia'] ?? '');
$opciones_dia_semana = is_array($d['opciones_dia_semana'] ?? null) ? $d['opciones_dia_semana'] : [];
$opciones_dia_ref = is_array($d['opciones_dia_ref'] ?? null) ? $d['opciones_dia_ref'] : [];
$opciones_ordinales = is_array($d['opciones_ordinales'] ?? null) ? $d['opciones_ordinales'] : [];

$url_update = 'frontend/encargossacd/controller/horario_sacd_update.php';

?>
<script>
    $(function () {
        $("#f_ini").datepicker();
    });
    $(function () {
        $("#f_fin").datepicker();
    });

    fnjs_guardar_horario = function (tipo) {
        let err = 0;
        let formulario = $('#modifica');
        let f_ini = $('#f_ini').val();
        let h_ini = $('#h_ini').val();
        let h_fin = $('#h_fin').val();
        let dia = $('#dia').val();

        if (!f_ini) {
            alert("Debe llenar el campo fecha inicio");
            err = 1;
        }
        if (!h_ini) {
            alert("Debe llenar el hora de inicio");
            err = 1;
        }
        if (!h_fin) {
            alert("Debe llenar el hora de finalización");
            err = 1;
        }
        if (!dia) {
            alert("Debe llenar el campo dia");
            err = 1;
        }

        let inc = 0;
        let dia_sem = dia;
        let dia_ref = $('#dia_ref').val();
        let mas_menos = $('#mas_menos').val();

        if (dia_ref) {
            if (mas_menos === "-") {
                if (dia_ref > dia_sem) inc = dia_ref * 1 - dia_sem * 1;
                if (dia_ref < dia_sem) inc = dia_ref * 1 + (7 - dia_sem * 1);
            }
            if (mas_menos === "+") {
                if (dia_ref > dia_sem) inc = (7 - dia_ref * 1) + dia_sem * 1;
                if (dia_ref < dia_sem) inc = dia_sem * 1 - dia_ref * 1;
            }
            $('#dia_inc').val(inc);
        }

        if (err !== 1) {
            switch (tipo) {
                case 4:
                    if (confirm("<?= htmlspecialchars(_("¿Está seguro que desea borrar este horario?"), ENT_QUOTES, 'UTF-8') ?>")) {
                        formulario.mod.value = "eliminar";
                        formulario.attr('action', '<?= $url_update ?>');
                    }
                    break;
                case 5:
                    formulario.mod.value = tipo;
                    formulario.attr('action', 'frontend/encargossacd/controller/horario_sacd_ex_ver.php');
                    break;
                default:
                    formulario.mod.value = tipo;
                    formulario.attr('action', '<?= $url_update ?>');
            }
            fnjs_enviar_formulario(formulario, '#ficha');
        }
    }
</script>
<form id="modifica" name="modifica" action="">
    <input type="hidden" name="filtro_sacd" value="<?= htmlspecialchars((string)$Qfiltro_sacd, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id_nom" value="<?= (int)$Qid_nom ?>">
    <input type="hidden" name="id_enc" value="<?= (int)$Qid_enc ?>">
    <input type="hidden" name="id_item" value="<?= (int)$id_item ?>">
    <input type="hidden" name="desc_enc" value="<?= htmlspecialchars((string)$desc_enc, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="mod" value="<?= (int)$Qmod ?>">
    <table>
        <tr>
            <th class="titulo_inv"><?= htmlspecialchars(ucfirst((string)$ap_nom), ENT_QUOTES, 'UTF-8') ?></th>
        </tr>
        <tr>
            <th class="titulo_inv"><?= htmlspecialchars(ucfirst((string)$titulo), ENT_QUOTES, 'UTF-8') ?></th>
        </tr>
    </table>
    <br>
    <table>
        <tr>

            <td class=etiqueta><?php echo ucfirst(_("activo desde")); ?>:</td>
            <td><input class="fecha" size="11" id="f_ini" name="f_ini" value="<?= htmlspecialchars((string)$f_ini_iso, ENT_QUOTES, 'UTF-8') ?>">
            <td class=etiqueta><?php echo ucfirst(_("hasta")); ?>:</td>
            <td><input class="fecha" size="11" id="f_fin" name="f_fin" value="<?= htmlspecialchars((string)$f_fin_iso, ENT_QUOTES, 'UTF-8') ?>">
        </tr>
        <tr>
            <td class=etiqueta><?php echo ucfirst(_("dia")); ?>:</td>
            <td><select class=contenido id="dia" name="dia">
                    <option></option>
                    <?php
                    foreach ($opciones_dia_semana as $key => $d_semana) {
                        $selected = ($dia === (string)$key) ? "selected" : "";
                        echo "<option value=\"" . htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') . "\" $selected>" . ucfirst((string)$d_semana) . "</option>";
                    }
                    ?>
                </select>
            </td>
            <td>
                <select class=contenido id="mas_menos" name="mas_menos">
                    <?php
                    $sel_mas = "";
                    $sel_menos = "";
                    if ($mas_menos === "-") {
                        $sel_menos = "selected";
                        $sel_mas = "";
                    }
                    if ($mas_menos === "+") {
                        $sel_mas = "selected";
                        $sel_menos = "";
                    }
                    echo "<option value=0></option>";
                    echo "<option value=\"-\" $sel_menos >" . _("antes del") . "</option>";
                    echo "<option value=\"+\" $sel_mas >" . _("después del") . "</option>";
                    ?>
                </select>
            </td>
            <td>
                <select class=contenido id="dia_num" name="dia_num">
                    <option></option>
                    <?php
                    foreach ($opciones_ordinales as $key => $d_ord) {
                        $selected = ($dia_num === (string)$key) ? "selected" : "";
                        echo "<option value=\"" . htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') . "\" $selected>" . htmlspecialchars((string)$d_ord, ENT_QUOTES, 'UTF-8') . "</option>";
                    }
                    ?>
                </select>
            </td>
            <td><select class=contenido id="dia_ref" name="dia_ref">
                    <option></option>
                    <?php
                    foreach ($opciones_dia_ref as $key => $d_ref) {
                        $selected = ($dia_ref === (string)$key) ? "selected" : "";
                        echo "<option value=\"" . htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') . "\" $selected>" . ucfirst((string)$d_ref) . "</option>";
                    }
                    ?>
                </select>
            </td>
        <tr>
            <td class=etiqueta><?php echo ucfirst(_("hora inicio")); ?>:</td>
            <td><input class=contenido size="11" id="h_ini" name="h_ini" value="<?= htmlspecialchars((string)$h_ini, ENT_QUOTES, 'UTF-8') ?>">
            <td class=etiqueta><?php echo ucfirst(_("hora fin")); ?>:</td>
            <td><input class=contenido size="11" id="h_fin" name="h_fin" value="<?= htmlspecialchars((string)$h_fin, ENT_QUOTES, 'UTF-8') ?>">
        </tr>
        <tr>
            <td><input type=hidden id="dia_inc" name="dia_inc" value="<?= htmlspecialchars((string)$dia_inc, ENT_QUOTES, 'UTF-8') ?>">
            </td>
        </tr>


    </table>
    <?php
    if ($id_item) {
        echo "<input TYPE=\"button\" VALUE=\"" . ucfirst(_("guardar horario")) . "\" onclick=\"javascript:fnjs_guardar_horario(2)\"> ";
        echo "<input TYPE=\"button\" VALUE=\"" . ucfirst(_("añadir horario")) . "\" onclick=\"javascript:fnjs_guardar_horario(3)\"> ";
        echo "<input TYPE=\"button\" VALUE=\"" . ucfirst(_("eliminar horario")) . "\" onclick=\"javascript:fnjs_guardar_horario(4)\"> ";
    } else {
        echo "<input TYPE=\"button\" VALUE=\"" . ucfirst(_("crear horario")) . "\" onclick=\"javascript:fnjs_guardar_horario(1)\"> ";
    }
    if ($id_item) {
        if ($tiene_excepciones) {
            echo "</form>";
            include("horario_sacd_ex_select.php");
        } else {
            echo "<input TYPE=\"button\" VALUE=\"" . ucfirst(_("generar excepciones")) . "\" onclick=\"javascript:fnjs_guardar_horario(5)\"> ";
        }
    }
    ?>
</form>
