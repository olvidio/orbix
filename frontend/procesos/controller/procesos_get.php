<?php
/**
 * Renderer frontend del arbol del proceso.
 * Llama a /src/procesos/procesos_get (JSON con aPadres) y dibuja el
 * arbol de fases.
 */

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/procesos/procesos_get', PostRequest::requestPayloadForHash());
$aPadres = $data['aPadres'] ?? [];

if (empty($aPadres)) {
    return;
}

/**
 * Dibuja recursivamente los hijos de una fase.
 */
function procesos_get_dibujar_hijos(array $aPadres, int $id_fase): string
{
    if (empty($aPadres[$id_fase])) {
        return '';
    }
    $html = '';
    foreach ($aPadres[$id_fase] as $padre) {
        $id_fase_i = (int)$padre['id'];
        $nom = $padre['nom'];
        if (array_key_exists($id_fase_i, $aPadres)) {
            $html .= '<div class="branch">';
            $html .= '<div class="entry"><span>' . $nom . '</span>';
            $html .= '<div class="branch">';
            $html .= procesos_get_dibujar_hijos($aPadres, $id_fase_i);
            $html .= '</div>';
            $html .= '</div>';
        } else {
            $html .= '<div class="entry"><span>' . $nom . '</span></div>';
        }
    }
    return $html;
}

ksort($aPadres);
echo '<div id="tree">';
if (!empty($aPadres[0])) {
    foreach ($aPadres[0] as $padre) {
        $id_fase_i = (int)$padre['id'];
        $nom = $padre['nom'];
        if (array_key_exists($id_fase_i, $aPadres)) {
            echo '<div class="branch">';
            echo '<div class="entry"><span>' . $nom . '</span>';
            echo '<div class="branch">';
            echo procesos_get_dibujar_hijos($aPadres, $id_fase_i);
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="entry"><span>' . $nom . '</span></div>';
        }
    }
}
echo '</div>';
