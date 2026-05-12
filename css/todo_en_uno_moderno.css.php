<?php
/**
 * Sustituye a {@see todo_en_uno.css.php} en la shell principal cuando el layout es `modern`:
 * carga primero los estilos legacy y aplica una capa de overrides acotada a `body.layout-modern`.
 *
 * Otras páginas que incluyen solo `todo_en_uno.css.php` no cambian.
 */
include __DIR__ . '/todo_en_uno.css.php';
?>
<style>
/* --- Orbix: capa «contenido moderno» (index.php, body.layout-modern) --- */
body.layout-modern {
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", sans-serif;
    background-color: #e4e7ed;
}

body.layout-modern #contenido_sin_menus {
    background-color: #e4e7ed;
}

body.layout-modern #main {
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06), 0 4px 24px rgba(15, 23, 42, 0.04);
    border: 1px solid <?= $lineas ?>;
    background-color: #f0f2f7;
}

body.layout-modern .titulo,
body.layout-modern .subtitulo,
body.layout-modern .etiqueta,
body.layout-modern .contenido,
body.layout-modern .fecha,
body.layout-modern .fecha_hora,
body.layout-modern div.lista,
body.layout-modern div.lista .etiqueta,
body.layout-modern div.lista .datos {
    font-family: inherit;
}

body.layout-modern .titulo {
    font-size: 1.25rem;
    letter-spacing: -0.02em;
    color: <?= $medio ?>;
    font-weight: 600;
}

body.layout-modern .subtitulo {
    font-size: 0.875rem;
    color: <?= $oscuro ?>;
    opacity: 0.75;
    font-weight: 600;
}

body.layout-modern .link,
body.layout-modern A {
    color: <?= $medio ?>;
}

body.layout-modern .link:hover,
body.layout-modern td.link:hover,
body.layout-modern span.link:hover,
body.layout-modern A:hover {
    color: <?= $letras_hover ?>;
}

body.layout-modern input[type="text"],
body.layout-modern input[type="password"],
body.layout-modern input[type="number"],
body.layout-modern input[type="search"],
body.layout-modern input[type="email"],
body.layout-modern select,
body.layout-modern textarea {
    font-family: inherit;
    font-size: 0.875rem;
    border-radius: 8px;
    border: 1px solid <?= $tono5 ?>;
    padding: 6px 10px;
    background: #f7f8fb;
}

body.layout-modern input.btn_ok,
body.layout-modern input[type="submit"],
body.layout-modern button:not(.modern-pill):not(.modern-user-trigger) {
    font-family: inherit;
    border-radius: 8px;
    padding: 6px 14px;
    border: 1px solid <?= $medio ?>;
    background: linear-gradient(180deg, <?= $tono2 ?> 0%, <?= $tono3 ?> 100%);
    color: <?= $oscuro ?>;
    font-weight: 600;
    cursor: pointer;
}

body.layout-modern input.btn_ok:hover,
body.layout-modern input[type="submit"]:hover {
    filter: brightness(1.03);
}

body.layout-modern table.lista,
body.layout-modern table {
    border-radius: 8px;
    overflow: hidden;
    border-color: <?= $lineas ?>;
    background: #e2e5eb !important;
}

body.layout-modern table.ca_posibles {
    background: #e2e5eb !important;
}

body.layout-modern th,
body.layout-modern th.ca_posibles,
body.layout-modern th.centrado {
    background: linear-gradient(180deg, <?= $tono4 ?> 0%, <?= $tono5 ?> 100%) !important;
    color: <?= $oscuro ?> !important;
    border-color: <?= $lineas ?> !important;
    font-weight: 600;
}

body.layout-modern tr.impar,
body.layout-modern tr.imp {
    background-color: #e8eaef !important;
}

body.layout-modern tr.par {
    background-color: #f1f3f7 !important;
}

body.layout-modern tr:hover {
    background-color: <?= $tono2 ?> !important;
}

body.layout-modern table.botones {
    border-radius: 8px;
}

body.layout-modern #cargando {
    left: 50%;
    top: 50%;
    transform: translate(-50%, -40%);
    border-radius: 12px;
    border: 1px solid <?= $lineas ?>;
    font-family: inherit;
    background-color: #f0f2f7 !important;
}

body.layout-modern .slick-cell {
    font-family: inherit;
    font-size: 0.8125rem;
}
</style>
