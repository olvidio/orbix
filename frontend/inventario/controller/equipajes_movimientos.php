<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
FrontBootstrap::boot();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$url_backend = '/src/inventario/equipajes_movimientos';
$a_campos_backend = ['sel' => $a_sel];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$mov = inventario_movimientos_from_payload(inventario_post_payload($data));

$aCambios = $mov['aCambios'];
$aLugaresPorEgm = $mov['aLugaresPorEgm'];
$aNomEquipajes = $mov['aNomEquipajes'];

$html_tot = '';
foreach ($aCambios as $id_equipaje => $aGrupos) {
    $html = '';
    foreach ($aGrupos as $id_item_egm => $aINOUT) {
        $html_in = '';
        $html_out = '';
        if ($aINOUT['in'] !== []) {
            foreach ($aINOUT['in'] as $id_doc) {
                $html_in .= '<tr><td></td><td></td><td></td><td>' . tessera_imprimir_string($id_doc) . '</td></tr>';
            }
        }
        if ($aINOUT['out'] !== []) {
            foreach ($aINOUT['out'] as $id_doc) {
                $html_out .= '<tr><td></td><td></td><td></td><td>' . tessera_imprimir_string($id_doc) . '</td></tr>';
            }
        }
        $html_g = '';
        if ($html_in !== '' || $html_out !== '') {
            $nom_lugar = $aLugaresPorEgm[$id_item_egm] ?? '';
            $html_g = '<tr><td></td><td colspan=3>' . $nom_lugar . '</td></tr>';
            if ($html_in !== '') {
                $html_g .= '<tr><td></td><td><td colspan=2>' . _('añadir') . '</td></tr>';
                $html_g .= $html_in;
            }
            if ($html_out !== '') {
                $html_g .= '<tr><td></td><td><td colspan=2>' . _('quitar') . '</td></tr>';
                $html_g .= $html_out;
            }
        }
        $html .= $html_g;
    }
    if ($html !== '') {
        $nom_equipaje = $aNomEquipajes[$id_equipaje] ?? '';
        $html_tot .= '<table><tr><td colspan=4>' . $nom_equipaje . '</td></tr>';
        $html_tot .= $html . '</table>';
    }
}
echo $html_tot;
