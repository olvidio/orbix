<?php

use actividades\model\entity\GestorActividad;
use actividadtarifas\model\entity\GestorTipoTarifa;
use casas\model\entity\GestorUbiGasto;
use casas\model\entity\Ingreso;
use ubis\model\entity\CasaDl;
use ubis\model\entity\GestorCasaPeriodo;
use ubis\model\entity\Tarifa;
use web\TiposActividades;
use web\DateTimeLocal;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$QG = (integer)filter_input(INPUT_POST, 'G');
$Qinc_t = (integer)filter_input(INPUT_POST, 'inc_t');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qseccion = (string)filter_input(INPUT_POST, 'seccion');

switch ($Qseccion) {
    case 'sv':
        $isfsv = 1;
        break;
    case 'sf':
        $isfsv = 2;
        break;
}

$any_actual = date("Y");
//$any_actual=2009;
$any_anterior = $any_actual - 1;
$any_prev = $any_actual + 1;

$oCasa = new CasaDl($Qid_ubi);
$nombre_ubi = $oCasa->getNombre_ubi();
$plazas_min = $oCasa->getPlazas_min();

$GesTipoTarifa = new GestorTipoTarifa();
$cTipoTarifas = $GesTipoTarifa->getTipoTarifas(array('sfsv' => $isfsv));
foreach ($cTipoTarifas as $oTipoTarifa) {
    $id_tarifa = $oTipoTarifa->getId_tarifa();
    $a_tarifas_actual[$id_tarifa]['modo'] = $oTipoTarifa->getModo();
    $a_tarifas_actual[$id_tarifa]['letra'] = $oTipoTarifa->getLetra();

    $oTarifa = new Tarifa();
    $oTarifa->setId_tarifa($id_tarifa);
    $oTarifa->setId_ubi($Qid_ubi);
    $oTarifa->setYear($any_actual);

    $a_tarifas_actual[$id_tarifa]['cantidad'] = $oTarifa->getCantidad();
    $oTarifaPrevision = new Tarifa();
    $oTarifaPrevision->setId_tarifa($id_tarifa);
    $oTarifaPrevision->setId_ubi($Qid_ubi);
    $oTarifaPrevision->setYear($any_prev);

    if (is_object($oTarifaPrevision)) {
        $a_tarifas_prev[$id_tarifa]['id_item'] = $oTarifaPrevision->getId_item();
        $a_tarifas_prev[$id_tarifa]['modo'] = $oTipoTarifa->getModo();
        $cantidad = $oTarifaPrevision->getCantidad();
        if (!empty($Qinc_t)) $cantidad = $a_tarifas_actual[$id_tarifa]['cantidad'] * (1 + $Qinc_t / 100);
        $a_tarifas_prev[$id_tarifa]['cantidad'] = $cantidad;
    } else {
        $cantidad = $oTarifa->getCantidad();
        if (!empty($Qinc_t)) {
            $c_prev = $cantidad * (1 + $Qinc_t / 100);
        } else {
            $c_prev = $cantidad;
        }
        $a_tarifas_prev[$id_tarifa]['cantidad'] = $c_prev;
        $a_tarifas_prev[$id_tarifa]['modo'] = $oTipoTarifa->getModo();
    }
}

$oInicio = new DateTimeLocal("$any_prev/1/1");
$oFin = new DateTimeLocal("$any_prev/12/31");
$GesCasaPeriodo = new GestorCasaPeriodo();
// dies ocupació sv (1).
$p_dv = $GesCasaPeriodo->getCasaPeriodosDias(1, $Qid_ubi, $oInicio, $oFin);
// dies ocupació sf (2).
$p_df = $GesCasaPeriodo->getCasaPeriodosDias(2, $Qid_ubi, $oInicio, $oFin);

// Ingresos

// Gastos ------
$oInicio_anterior = new DateTimeLocal("$any_anterior/1/1");
$oFin_anterior = new DateTimeLocal("$any_anterior/12/31");
$GesGastos = new GestorUbiGasto();
// tipo: 1=sv, 2=sf, 3=gastos
$a_resumen[1]['aportacion'] = $GesGastos->getSumaGastos($Qid_ubi, 1, $oInicio_anterior, $oFin_anterior);
$a_resumen[2]['aportacion'] = $GesGastos->getSumaGastos($Qid_ubi, 2, $oInicio_anterior, $oFin_anterior);
$a_resumen[0]['gasto'] = $GesGastos->getSumaGastos($Qid_ubi, 3, $oInicio_anterior, $oFin_anterior);

if (empty($a_resumen[0]['gasto'])) {

    $aQuery = ['tipo_lista' => 'datosEcGastos',
        'id_ubi' => $Qid_ubi,
        'periodo' => 'ninguno',
        'year' => $any_anterior,
    ];
    // el hppt_build_query no pasa los valores null
    if (is_array($aQuery)) {
        array_walk($aQuery, 'core\poner_empty_on_null');
    }
    $pagina = web\Hash::link('apps/casas/controller/casa_que.php?' . http_build_query($aQuery));

    $link = "<span class=link onclick=\"fnjs_update_div('#main','$pagina');\">$any_anterior</span>";

    echo(sprintf(_("Falta introducir la información económica (total) del año anterior: %s"), $link));
    echo "<br><br>";
    exit;
}

$r_it = $a_resumen[0]['gasto'];
$r_idef = 0;
$r_ip = 0;
//$r_idl = 0;
$r_tda = 0;
$r_tac = 0;
$r_tap = 0;
$r_ta = 0;

switch ($Qseccion) {
    case 'sv':
        $r_idl = $a_resumen[1]['aportacion'];
        $p_ta_min = $p_dv * $plazas_min;
        $p_dseccion = $p_dv;
        $p_ta_min_txt = _("Mínimo de asistencias (p_dv.M)");
        $total_txt = _("según asignación dias sv");

        $p_ip = empty($p_dv) ? 0 : round((1 + $QG / 100) * $r_it * $p_dv / ($p_dv + $p_df), 2);
        $p_ip_txt = _("Previsión de ingresos sv a 2 años vista (1+G).r_it(p_dv/(p_dv+p_df))");
        break;
    case 'sf':
        $r_idl = $a_resumen[2]['aportacion'];
        $p_ta_min = $p_df * $plazas_min;
        $p_dseccion = $p_df;
        $p_ta_min_txt = _("Mínimo de asistencias (p_df.M)");
        $total_txt = _("según asignación dias sf");

        $p_ip = empty($p_df) ? 0 : round((1 + $QG / 100) * $r_it * $p_df / ($p_dv + $p_df), 2);
        $p_ip_txt = _("Previsión de ingresos sf a 2 años vista (1+G).r_it(p_df/(p_dv+p_df))");
        break;
}

//Activitats año previsto
$aWhere['id_ubi'] = $Qid_ubi;
$aWhere['f_ini'] = $oFin->getIso();
$aOperador['f_ini'] = '<=';
$aWhere['f_fin'] = $oInicio->getIso();
$aOperador['f_fin'] = '>=';

$aWhere['id_tipo_activ'] = "^$isfsv";
$aOperador['id_tipo_activ'] = '~';
$aWhere['_ordre'] = 'f_ini';
$GesActividades = new GestorActividad();
$cActividades = $GesActividades->getActividades($aWhere, $aOperador);

$i = 0;
$p_tda = 0;
$p_tap = 0;
$p_ta = 0;
$p_tia = 0;
$r_tia = 0;
$a_actividades = [];
foreach ($cActividades as $oActividad) {
    $i++;
    $id_activ = $oActividad->getId_activ();
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $f_ini_local = $oActividad->getF_ini()->getFromLocal();
    $f_fin_local = $oActividad->getF_fin()->getFromLocal();

    $num_dias_act = $oActividad->getDuracion();
    $num_dias = $oActividad->getDuracionEnPeriodo($oInicio, $oFin);
    $num_dias_real = $oActividad->getDuracionReal();
    $factor_dias = ($num_dias / $num_dias_real);

    $factor = ($num_dias_act - $num_dias_real) / $num_dias_real;
    $num_dias = round($num_dias * (1 + $factor), 1);

    //echo "$nom_activ hh: $horas dd: $dias mod: $e_dias xx: $dec suma:$suma_dias total:$num_dias<br>";
    $oTipoActiv = new TiposActividades($id_tipo_activ);
    $nom = $oTipoActiv->getNom() . " ($f_ini_local - $f_fin_local)";
    if (!empty($id_tarifa)) {
        $oIngreso = new Ingreso($id_activ);
        $num_asistentes = $oIngreso->getNum_asistentes();
        if (empty($num_asistentes)) $num_asistentes = $plazas_min;
        $asistencias = $num_dias * $num_asistentes;
        // si la tarifa es modo 1 no tiene en cuenta los dias de la actividad
        if ($a_tarifas_prev[$id_tarifa]['modo'] == 1) {
            $ingresos = round($num_asistentes * $a_tarifas_prev[$id_tarifa]['cantidad'], 2);
            $ingresos_actual = round($num_asistentes * $a_tarifas_actual[$id_tarifa]['cantidad'], 2);
        } else {
            $ingresos = round($asistencias * $a_tarifas_prev[$id_tarifa]['cantidad'], 2);
            $ingresos_actual = round($asistencias * $a_tarifas_actual[$id_tarifa]['cantidad'], 2);
        }
        $letra_tarifa = $a_tarifas_actual[$id_tarifa]['letra'];
    } else {
        $ingresos = _("tar. no definida");
        $letra_tarifa = '';
        $asistencias = '?';
        $ingresos_actual = 0;
    }
    //$p_ia=number_format($ingresos, 2, ',', '.');
    $a_actividades[] = array('nom' => $nom, 'dias' => $num_dias, 'asistentes' => $num_asistentes, 'asistencias' => $asistencias,
        'tarifa' => $letra_tarifa, 'ingresos' => $ingresos);
    //totales
    $p_tac = $i;
    $p_tda += $num_dias;
    $p_tap += $num_asistentes;
    $p_ta += $asistencias;
    $p_tia += $ingresos;
    $r_tia += $ingresos_actual;
}
//Si no hay actividades:
if ($i < 1) {
    $p_tac = 1;
}
// tarifa media ponderada
$p_tarifa = empty($p_ta) ? 0 : round($p_tia / $p_ta, 2);
$p_ti_min = round($p_ta_min * $p_tarifa, 2);

switch ($Qseccion) {
    case 'sv':
        $dias_libres = $p_dv - $p_tda;
        break;
    case 'sf':
        $dias_libres = $p_df - $p_tda;
        break;
}

$dif_asistencias = round($p_ta_min - $p_ta, 2);
$dif_ingresos = round($p_ti_min - $p_tia, 2);
if (empty($p_tarifa)) {
    $inc_p = _("no disponible");
    $inc_d = 0;
    $inc_pt = 0;
} else {
    $inc_p = round(($p_ip - $p_tia) / $p_tarifa);
    $inc_d = round($inc_p / $plazas_min);
    $inc_pt = round(($p_ip / $r_tia - 1) * 100);
}
// -------------------------------------------- html --------------------------------------
?>
<style>
    td {
        text-align: right;
    }
</style>
<?php
if (!empty($Qseccion)) {
?>
<h2 class=titulo><?php echo ucfirst(sprintf(_("Estudio económico y de ocupación de %s para %s"), $nombre_ubi, $Qseccion)); ?></h2>
<form id="frm_tarifas" action="">
    <input type="hidden" name="id_ubi" value="<?= $Qid_ubi ?>">
    <input type="hidden" name="year" value="<?= $any_prev ?>">
    <input type="hidden" name="que" value="update_inc">
    <table>
        <tr>
            <td><?= _("tarifa"); ?></td>
            <?php
            $tar_txt = '';
            $cantidad_txt = '';
            $incremento_txt = '';
            foreach ($a_tarifas_actual as $id_tarifa => $aTarifa) {
                $tar_txt .= "<td>" . $aTarifa['letra'] . "</td>";
                $cantidad_txt .= "<td>" . $aTarifa['cantidad'] . "</td>";
                $inc = empty($aTarifa['cantidad']) ? _("no disponible") : round((($a_tarifas_prev[$tar]['cantidad'] / $aTarifa['cantidad']) - 1) * 100);
                $incremento_txt .= "<td>$inc%</td>";
            }
            $prevision_txt = '';
            foreach ($a_tarifas_prev as $id_tarifa => $aTarifa) {
                $valor = $aTarifa['cantidad'];
                $id_item = $aTarifa['id_item'];
                $index = "$id_tarifa#$id_item";
                $prevision_txt .= "<td><input type=text name=inc_cantidad[$index] size=4 value=$valor></td>";
            }
            ?>
            <?= $tar_txt ?>
        </tr>
        <tr>
            <td><?= _("actual"); ?></td><?= $cantidad_txt ?></tr>
        <tr>
            <td><?= _("previsión"); ?></td><?= $prevision_txt ?></tr>
        <tr>
            <td><?= _("incremento"); ?></td><?= $incremento_txt ?></tr>
        <tr>
            <td><input type="button" onclick="fnjs_guardar('frm_tarifas');" value="grabar tarifas"></td>
        </tr>
    </table>
</form>
<p><?= sprintf(_("Para hacer una estimación de los gastos de %s, se toman los de %s que son los únicos gastos reales a 31 de diciembre."), $any_prev, $any_anterior) ?></p>
<p><?= _("Se parte de que los gastos totales = ingresos totales. Hipótesis que es cierta si en los ingresos se consignan los procedentes de los asistentes, las multas pagadas por los ctr y las cantidades aportadas directamente por las dl para equilibrar los gastos.") ?></p>
<p><br/></p>
<p><span class=contenido><?= sprintf(_("Año anterior %s"), $any_anterior) ?></p>
<ul>
    <li><span class=contenido><?= $r_it ?> €</span> [r_it] <?= _("Ingresos totales año anterior (sv+sf)") ?></li>
    <li><span class=contenido><?= $r_idef ?> €</span>
        [r_idef] <?= _("Ingresos totales año anterior procedentes de deficits (multa)") ?></li>
    <li><span class=contenido><?= $r_idl ?> €</span> [r_idl] <?= _("Ingresos totales año anterior procedentes de dl") ?>
    </li>
    <li><span class=contenido><?= $r_ip ?> €</span>
        [r_ip] <?= _("Ingresos totales año anterior procedentes de asistencias") ?></li>
    <li><span class=contenido><?= $r_ta ?></span> [r_Rta] <?= _("número total de asistencias") ?></li>
</ul>
<p></p>
<p><span class=contenido><?= sprintf(_("Año previsión %s"), $any_prev) ?></p>
<ul>
    <li><span class=contenido><?= $p_dv ?></span> [p_dv] <?= _("Días de ocupación sv") ?></li>
    <li><span class=contenido><?= $p_df ?></span> [p_df] <?= _("Días de ocupación sf") ?></li>
    <li><span class=contenido><?= $plazas_min ?></span> [M] <?= _("número mínimo de asistentes") ?></li>
    <li><span class=contenido><?= $QG ?>%</span> [G] <?= _("Incremento de gastos 2 años") ?></li>
    <li><span class=contenido><?= $p_ip ?></span> [p_ip] <?= $p_ip_txt ?></li>
    <li><span class=contenido><?= $p_ta_min ?></span> [p_ta_min] <?= $p_ta_min_txt ?></li>
    <li><span class=contenido><?= $p_tarifa ?> €</span>
        [p_tarifa] <?= _("tarifa media ponderada: prev.ing.actividades/prev.asistencias: [p_tia]/[p_ta]") ?></li>
</ul>
<table border=1>
    <tr>
        <td colspan=8 class=titulo><?= _("Actividades próximo año") ?></td>
    <tr>
    <tr>
        <td colspan=2><?= _("Actividad") ?></td>
        <td><?= _("nº de días") ?></td>
        <td><?= _("previsión de asistentes") ?></td>
        <td><?= _("nº total de asistencias") ?></td>
        <td><?= _("tarifa") ?></td>
        <td><?= _("ingresos previstos de asistentes") ?></td>
    </tr>
    <?php
    foreach ($a_actividades as $actividad) {
        ?>
        <tr>
            <td colspan=2><?= $actividad['nom'] ?></td>
            <td><?= $actividad['dias'] ?></td>
            <td><?= $actividad['asistentes'] ?></td>
            <td><?= $actividad['asistencias'] ?></td>
            <td><?= $actividad['tarifa'] ?></td>
            <td><?= $actividad['ingresos'] ?></td>
        </tr>
        <?php
    }
    ?>
    <tr class=total>
        <td><?= _("total") ?>  <?= $any_prev ?></td>
        <td><?= $p_tac ?></td>
        <td><?= $p_tda ?></td>
        <td><?= $p_tap ?></td>
        <td><?= $p_ta ?></td>
        <td></td>
        <td><?= $p_tia ?></td>
    <tr>
        <td><?= _("total") ?>  <?= $any_anterior ?></td>
        <td><?= $r_tac ?></td>
        <td><?= $r_tda ?></td>
        <td><?= $r_tap ?></td>
        <td><?= $r_ta ?></td>
        <td></td>
        <td><?= $p_ip ?></td>
    </tr>
    <td><?= _("diferencia") ?></td>
    <td><?= $p_tac - $r_tac ?></td>
    <td><?= $p_tda - $r_tda ?></td>
    <td><?= $p_tap - $r_tap ?></td>
    <td><?= $p_ta - $r_ta ?></td>
    <td></td>
    <td><?= $p_tia - $p_ip ?></td>
    </tr>
</table>
<p><?= sprintf(_("ingresos previstos por actividades: %s"), $p_tia) ?>
    <?= sprintf(_("gastos reales del %s proyectados al %s: %s"), $any_anterior, $any_prev, $p_ip) ?></p>

<h2><?= _("pistas para equilibrar la gestión") ?></h2>
<p><?= sprintf(_("incrementar las asistencias en %s"), $inc_p) ?>
    <?= sprintf(_("que supone %s dias de actividad"), $inc_d) ?></p>
<p><?= sprintf(_("incrementar las tarifas en %s %%"), $inc_pt) ?>
<table border=1>
    <tr>
        <td colspan=8 class=titulo><?= _("Comparación con los mínimos de la casa") ?></td>
    <tr>
    <tr>
        <td></td>
        <td><?= _("nº de actividades") ?></td>
        <td><?= _("nº de días") ?></td>
        <td><?= _("previsión de asistentes") ?></td>
        <td><?= _("nº total de asistencias") ?></td>
        <td><?= _("ingresos previstos de asistentes") ?></td>
    </tr>
    <tr class=total>
        <td><?= _("total según actividades") ?> <?= $any_prev ?></td>
        <td><?= $p_tac ?></td>
        <td><?= $p_tda ?></td>
        <td><?= $p_tap ?></td>
        <td><?= $p_ta ?></td>
        <td><?= $p_tia ?></td>
    <tr>
    <tr class=total>
        <td><?= $total_txt ?> <?= $any_prev ?></td>
        <td></td>
        <td><?= $p_dseccion ?></td>
        <td></td>
        <td><?= $p_ta_min ?></td>
        <td><?= $p_ti_min ?></td>
    <tr>
    <tr>
        <td><?= _("diferencia") ?></td>
        <td></td>
        <td><?= $dias_libres ?></td>
        <td></td>
        <td><?= $dif_asistencias ?></td>
        <td><?= $dif_ingresos ?></td>
    </tr>
    <?php
    }
    ?>
