<style>
:root {
    --legacy-chrome-offset: 72px;
    --legacy-chrome-z: 100010;
}

.orbix-layout-legacy {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: var(--legacy-chrome-z);
    background: <?= $fondo_claro ?>;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}

body.layout-legacy #contenido_sin_menus {
    position: relative;
    margin-top: var(--legacy-chrome-offset);
    height: calc(100vh - var(--legacy-chrome-offset));
    overflow: hidden;
}

/* El shell fijo asume el apilamiento; evitar un techo artificial de 1000 en los hijos. */
body.layout-legacy .orbix-layout-legacy #menu,
body.layout-legacy .orbix-layout-legacy #submenu {
    position: relative;
    z-index: auto;
}
</style>
