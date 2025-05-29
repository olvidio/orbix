<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/infrastructure/controllers/equipajes_movimientos.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['sel' => $a_sel]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$aCambios = $data['aCambios'];
$aLugaresPorEgm = $data['aLugaresPorEgm'];
$aNomEquipajes = $data['aNomEquipajes'];

$html_tot = '';
foreach ($aCambios as $id_equipaje => $aGrupos) {
    $html = '';
    foreach ($aGrupos as $id_item_egm => $aINOUT) {
        $html_in = '';
        $html_out = '';
        if (!empty($aINOUT['in'])) {
            foreach ($aINOUT['in'] as $id_doc) {
                $html_in .= '<tr><td></td><td></td><td></td><td>' . $id_doc . '</td></tr>';
            }
        }
        if (!empty($aINOUT['out'])) {
            foreach ($aINOUT['out'] as $id_doc) {
                $html_out .= '<tr><td></td><td></td><td></td><td>' . $id_doc . '</td></tr>';
            }
        }
        if ($html_in != '' or $html_out != '') {
            $nom_lugar = $aLugaresPorEgm[$id_item_egm];
            $html_g = '<tr><td></td><td colspan=3>' . $nom_lugar . '</td></tr>';
            if ($html_in != '') {
                $html_g .= '<tr><td></td><td><td colspan=2>' . _("añadir") . '</td></tr>';
                $html_g .= $html_in;
            }
            if ($html_out != '') {
                $html_g .= '<tr><td></td><td><td colspan=2>' . _("quitar") . '</td></tr>';
                $html_g .= $html_out;
            }
        }
        $html .= $html_g;
    }
    if ($html != '') {
        $nom_equipaje = $aNomEquipajes[$id_equipaje];
        $html_tot .= "<table><tr><td colspan=4>" . $nom_equipaje . '</td></tr>';
        $html_tot .= $html . '</table>';
    }
}
echo $html_tot;
