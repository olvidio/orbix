<?php

/**
 * Variables PHP del planning y tokens CSS según el estilo de sesión (azul / verde / naranja).
 * Incluir siempre ANTES de calendario.css.php.
 */
require_once __DIR__ . '/colores_estilo_desde_sesion.php';
[$estilo_color, $tipo_menu] = css_colores_estilo_desde_sesion();
require_once __DIR__ . '/colores.php';

$planning_nom_text = ($letras === 'black') ? '#333333' : $letras;
$table_border = ' frame=below rules=groups CELLSPACING=0';

switch ($estilo_color) {
    case 'naranja':
        // Tonos claros (tono1/tono2); domingo más oscuro (oscuro + tono5)
        $colorColumnaUno = $tono1;
        $colorColumnaDos = $tono2;
        $colorColumnaDomingo = $tono5;
        $planning_nom_bg = $tono1;
        $planning_nom_border = $tono2;
        $planning_cap_text = $oscuro;
        $planning_mes_bg = $tono1;
        $planning_mes_text = $oscuro;
        $planning_diumenge_bg = $oscuro;
        $planning_diumenge_text = '#FFFFFF';
        $planning_diumenge_cell_bg = $tono5;
        break;
    case 'verde':
        $colorColumnaUno = $tono1;
        $colorColumnaDos = $tono2;
        $colorColumnaDomingo = $tono4;
        $planning_nom_bg = $tono1;
        $planning_nom_border = $tono3;
        $planning_cap_text = $oscuro;
        $planning_mes_bg = $tono2;
        $planning_mes_text = $oscuro;
        $planning_diumenge_bg = $fondo_oscuro;
        $planning_diumenge_text = $fondo_claro;
        $planning_diumenge_cell_bg = $tono4;
        break;
    case 'azul':
    default:
        $colorColumnaUno = $tono1;
        $colorColumnaDos = $tono2;
        $colorColumnaDomingo = $tono4;
        $planning_nom_bg = $tono1;
        $planning_nom_border = $tono3;
        $planning_cap_text = $oscuro;
        $planning_mes_bg = $tono2;
        $planning_mes_text = $oscuro;
        $planning_diumenge_bg = $fondo_oscuro;
        $planning_diumenge_text = $fondo_claro;
        $planning_diumenge_cell_bg = $tono4;
        break;
}
?>
<style>
:root {
    --planning-nom-bg: <?= $planning_nom_bg ?>;
    --planning-nom-text: <?= $planning_nom_text ?>;
    --planning-nom-border: <?= $planning_nom_border ?>;
    --planning-cap-text: <?= $planning_cap_text ?>;
    --planning-mes-bg: <?= $planning_mes_bg ?>;
    --planning-mes-text: <?= $planning_mes_text ?>;
    --planning-diumenge-bg: <?= $planning_diumenge_bg ?>;
    --planning-diumenge-text: <?= $planning_diumenge_text ?>;
    --planning-diumenge-cell-bg: <?= $planning_diumenge_cell_bg ?>;
    --planning-th-bg: #ffffff;
    --planning-th-text: <?= $planning_nom_text ?>;
    --planning-border: <?= $lineas ?>;
    --planning-border-light: <?= $lineas ?>;
    /* lineas (fino, misma persona) < tono4 (entre personas) */
    --planning-border-fila-interna: <?= $lineas ?>;
    --planning-persona-separator: <?= $tono4 ?>;
}
</style>
