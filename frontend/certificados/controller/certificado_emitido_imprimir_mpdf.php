<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\certificados\helpers\CertificadosMpdfRender;
use frontend\certificados\helpers\CertificadosPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

$idItemRaw = filter_input(INPUT_GET, 'id_item', FILTER_VALIDATE_INT);
$Qid_item = is_int($idItemRaw) ? $idItemRaw : 0;

require_once __DIR__ . '/certificado_emitido_aviso_html.php';

$payload = CertificadosPayload::postData(PostRequest::getDataFromUrl(
    '/src/certificados/certificado_emitido_imprimir_mpdf_datos',
    ['id_item' => $Qid_item],
    false,
));
if (!empty($payload['error'])) {
    certificado_emitido_echo_aviso_y_salir(
        PostRequest::stripInternalCallProvenance(PayloadCoercion::string($payload['error']))
    );
}

$mpdf = CertificadosMpdfRender::fromPayload($payload);
$id_nom = $mpdf['id_nom'];
$nom = $mpdf['nom'];
$certificado = $mpdf['certificado'];
$lugar_fecha = $mpdf['lugar_fecha'];
$vstgr = $mpdf['vstgr'];
$dir_stgr = $mpdf['dir_stgr'];
$replace = $mpdf['replace'];
$txt_superavit = $mpdf['txt_superavit'];
$cAsignaturas = $mpdf['cAsignaturas'];
$aAprobadas = $mpdf['aAprobadas'];

$mpdfLabels = [
    'curso_filosofia' => $mpdf['curso_filosofia'],
    'curso_teologia' => $mpdf['curso_teologia'],
    'any_I' => $mpdf['any_I'],
    'any_II' => $mpdf['any_II'],
    'any_III' => $mpdf['any_III'],
    'any_IV' => $mpdf['any_IV'],
    'ECTS' => $mpdf['ECTS'],
    'iudicium' => $mpdf['iudicium'],
    'pie_ects' => $mpdf['pie_ects'],
];

$rowEmpty = CertificadosMpdfRender::emptyAprobadaRow();
?>
<head>
    <?php include_once(OrbixRuntime::dirEstilos() . '/certificado_mpdf.css.php'); ?>
</head>
<body>
<div class="A4">
    <table>
        <col style="width: 7%">
        <col style="width: 45%">
        <col style="width: 5%">
        <col style="width: 36%">
        <col style="width: 7%">
        <tr>
            <td class="space"></td>
        </tr>
        <tr>
            <td class="titulo1" colspan="5"><?= $mpdf['titulo_1'] ?></td>
        </tr>
        <tr>
            <td class="titulo2" colspan="5"><?= $mpdf['titulo_2'] ?></td>
        </tr>
        <tr>
            <td class="subtitulo1" colspan="5"><?= $mpdf['titulo_3'] ?></td>
        </tr>
        <tr>
            <td class="subtitulo2" colspan="5"><?= $mpdf['infra'] ?></td>
        </tr>
        <?php
        ksort($aAprobadas);
        $num_asig = count($cAsignaturas);
        $a = 0;
        $j = 0;
        reset($aAprobadas);
        $row = CertificadosMpdfRender::currentAprobadaRow($aAprobadas, $rowEmpty);
        while ($a < count($cAsignaturas)) {
            $oAsignatura = $cAsignaturas[$a++];
            while (($row['id_nivel_asig'] < $oAsignatura['id_nivel']) && ($j < $num_asig)) {
                if (key($aAprobadas) === null) {
                    $row = $rowEmpty;
                    break;
                }
                if (next($aAprobadas) === false) {
                    break;
                }
                $row = CertificadosMpdfRender::currentAprobadaRow($aAprobadas, $rowEmpty);
                $j++;
            }
            while (($oAsignatura['id_nivel'] < $row['id_nivel_asig']) && ($row['id_nivel'] < 2434)) {
                $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                $etcs = number_format(($oAsignatura['creditos'] * 2), 0);
                CertificadosMpdfRender::titulo($oAsignatura['id_nivel'], $mpdfLabels);
                ?>
                <tr style="vertical-align: text-bottom">
                    <td></td>
                    <td><?= $nombre_asignatura ?>&nbsp;</td>
                    <td class="dato"><?= $etcs ?>&nbsp;</td>
                    <td class="dato">-----------</td>
                    <td></td>
                </tr>
                <?php
                $oAsignatura = $cAsignaturas[$a++];
            }

            if ($oAsignatura['id_nivel'] === $row['id_nivel_asig']) {
                CertificadosMpdfRender::titulo($oAsignatura['id_nivel'], $mpdfLabels);
                if ($row['id_asignatura'] > 3000 && $row['id_asignatura'] < 9000) {
                    $nombre_asignatura = strtr($row['nombre_asignatura'], $replace);
                    $algo = $oAsignatura['nombre_asignatura'] . '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $nombre_asignatura;
                    ?>
                    <tr class="opcional" style="vertical-align: text-bottom">
                        <td></td>
                        <td><?= $algo ?>&nbsp;</td>
                        <td class="dato"><?= $row['creditos'] ?>&nbsp;</td>
                        <td class="dato"><?= $row['nota_txt'] ?>&nbsp;</td>
                        <td></td>
                    </tr>
                    <?php
                } else {
                    $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                    ?>
                    <tr>
                        <td></td>
                        <td><?= $nombre_asignatura ?>&nbsp;</td>
                        <td class="dato"><?= $row['creditos'] ?>&nbsp;</td>
                        <td class="dato"><?= $row['nota_txt'] ?>&nbsp;</td>
                        <td></td>
                    </tr>
                    <?php
                }
                $num_asig++;
            } elseif ($row['id_nivel'] === 0 || ($j === $num_asig)) {
                $nombre_asignatura = strtr($oAsignatura['nombre_asignatura'], $replace);
                $etcs = number_format(($oAsignatura['creditos'] * 2), 0);
                CertificadosMpdfRender::titulo($oAsignatura['id_asignatura'], $mpdfLabels);
                ?>
                <tr>
                    <td></td>
                    <td><?= $nombre_asignatura ?>&nbsp;</td>
                    <td class="dato"><?= $etcs ?>&nbsp;</td>
                    <td class="dato">----------</td>
                    <td></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
    <table>
        <tr>
            <td class="subtitulo2" colspan="5">
                <?= $txt_superavit ?>
            </td>
        </tr>
    </table>
    <div class="pie">
        <div class="fecha"><?= $lugar_fecha ?></div>
        <table class="g_sello">
            <tr>
                <td class="sello"><?= $mpdf['sello'] ?></td>
                <td class="firma"><?= $mpdf['fidem'] ?></td>
            </tr>
            <tr>
                <td class="espacio_firma"></td>
            </tr>
        </table>
    </div>
</div>

<div class="g_libro">
    <table>
        <tr>
            <td class="libro"><?= $mpdf['reg_num'] ?> (<?= $certificado ?>)</td>
            <td class="libro"></td>
            <td class="libro"></td>
            <td class="secretario"><?= $vstgr ?></td>
        </tr>
    </table>
</div>

<div class="ects"><?= $mpdf['pie_ects'] ?>
</div>
<?php
$footer = '<table class="piepagina"><tr><td class="f7">F10</td><td class="dir">' . $dir_stgr . '</td></tr></table>';
?>
</body>
