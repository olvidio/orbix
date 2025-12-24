<?php

/**
 *
 * @package    delegacion
 * @subpackage actividades
 * @author    Daniel Serrabou
 * @since        15/3/09.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Periodo;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_ctr_num = (integer)filter_input(INPUT_POST, 'id_ctr_num');
$Qa_id_ctr = (array)filter_input(INPUT_POST, 'id_ctr', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

// valores por defeccto
if (empty($Qperiodo)) {
    $Qperiodo = 'actual';
}

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);


$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();
if (!empty($Qperiodo) && $Qperiodo === 'desdeHoy') {
    $condicion_periodo = "f_fin BETWEEN '$inicioIso' AND '$finIso'";
} else {
    $condicion_periodo = "f_ini BETWEEN '$inicioIso' AND '$finIso'";
}


$CentroRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
if (empty($Qid_ctr_num)) {
    // Todos los ctr de sg
    $aWhere = ['tipo_ctr' => '^s[^s]', '_ordre' => 'nombre_ubi'];
    $aOperador = ['tipo_ctr' => '~'];
    $cCentros = $CentroRepository->getCentros($aWhere, $aOperador);
} else {
    // una lista de ctrs.
    $Qa_id_ctr = array_filter($Qa_id_ctr); // para quitar los elementos vacios.
    $aWhere['id_ubi'] = implode(',', $Qa_id_ctr);
    $aOperador['id_ubi'] = 'IN';
    // puede ser que este todo vacio.
    if (empty($Qa_id_ctr)) {
        // Todos los ctr de sg
        $aWhere = ['tipo_ctr' => '^s[^s]', '_ordre' => 'nombre_ubi'];
        $aOperador = ['tipo_ctr' => '~'];
        $cCentros = $CentroRepository->getCentros($aWhere, $aOperador);
    } else {
        $cCentros = $CentroRepository->getCentros($aWhere, $aOperador);
    }
}

$c = 0;
$a_centros = [];
$a_actividades = [];
$CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
foreach ($cCentros as $oCentro) {
    $c++;
    $id_ubi = $oCentro->getId_ubi();
    $a_centros[$c] = $oCentro->getNombre_ubi();
    // actividades encargadas al centro en el periodo
    $cActividades = $CentroEncargadoRepository->getActividadesDeCentros($id_ubi, $condicion_periodo);
    // para cada actividad, los otros centros encargados
    $a = 0;
    foreach ($cActividades as $oActividad) {
        $a++;
        $id_activ = $oActividad->getId_activ();
        //$a_actividades[$c][$a]['f_ini']=$oActividad->getF_ini();
        //$a_actividades[$c][$a]['f_fin']=$oActividad->getF_fin();
        $a_actividades[$c][$a]['nom_activ'] = $oActividad->getNom_activ();
        $cEncargados = $CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ);
        $i = 0;
        $txt_ctr = "";
        foreach ($cEncargados as $oUbi) {
            $i++;
            $id_ctr = $oUbi->getId_ubi();
            $ctr = $oUbi->getNombre_ubi();
            //$num_orden=$oUbi->getNum_orden();
            // no pongo el propio centro
            if ($id_ctr != $id_ubi) {
                if ($i == 1) {
                    $clase = "class='responsable'";
                } else {
                    $clase = "";
                }
                $txt_ctr .= "<span $clase> $ctr;</span>";
            }
            $a_actividades[$c][$a]["mas_ctr"] = $txt_ctr;
        }
    }
}

// ----------------------------- html -----------------------------------
?>
<style>
    .responsable {
        text-decoration: underline;
    }
</style>
<?php
$num_ctr = count($a_centros);
for ($c = 1; $c <= $num_ctr; $c++) {
    $centro = $a_centros[$c];
    echo "<h3>$centro</h3>";
    echo "<table>";
    if (!empty($a_actividades[$c]) && is_array($a_actividades[$c])) {
        foreach ($a_actividades[$c] as $actividad) {
            /*
               Ahora (1/5/2012) sin fechas...
            <tr><td><?= $actividad['f_ini'] ?></td><td><?= $actividad['f_fin'] ?></td><td><?= $actividad['nom_activ'] ?></td><td><?= $actividad['mas_ctr'] ?></td></tr>
            */
            ?>
            <tr>
                <td><?= $actividad['nom_activ'] ?></td>
                <td><?= $actividad['mas_ctr'] ?></td>
            </tr>
            <?php
        }
    }
    echo "</table>";
}
?>
