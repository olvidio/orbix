<style>
:root {
    --pills-accent: <?= $GLOBALS['medio'] ?>;
    --pills-accent-strong: <?= $GLOBALS['oscuro'] ?>;
    /* Superficies alineadas con el contenido (gris claro, menos blanco puro) */
    --pills-canvas: #e4e7ed;
    --pills-surface: #eef1f7;
    --pills-surface-2: <?= $GLOBALS['tono2'] ?>;
    --pills-border: color-mix(in srgb, <?= $GLOBALS['oscuro'] ?> 12%, transparent);
    --pills-text: <?= $GLOBALS['letras'] ?>;
    --pills-muted: color-mix(in srgb, <?= $GLOBALS['letras'] ?> 55%, transparent);
    --pills-bar-bg: color-mix(in srgb, <?= $GLOBALS['muy_oscuro1'] ?> 92%, white);
    --pills-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
    --pills-radius: 10px;
    --pills-pill-radius: 999px;
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    :root {
        --pills-border: rgba(50, 80, 120, 0.15);
        --pills-muted: #5c6570;
        --pills-bar-bg: <?= $GLOBALS['muy_oscuro1'] ?>;
    }
}

.orbix-layout-pills *,
.orbix-layout-pills *::before,
.orbix-layout-pills *::after {
    box-sizing: border-box;
}

body.otro {
    margin: 0;
    min-height: 100vh;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", sans-serif;
    background: var(--pills-canvas);
    color: var(--pills-text);
}

.orbix-layout-pills {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    gap: 0;
    background: var(--pills-surface);
    border-bottom: 1px solid var(--pills-border);
    box-shadow: var(--pills-shadow);
}

.pills-appbar {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 52px;
    padding: 6px 14px 8px;
    background: linear-gradient(180deg, var(--pills-bar-bg) 0%, color-mix(in srgb, var(--pills-bar-bg) 88%, white) 100%);
    backdrop-filter: blur(10px);
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    .pills-appbar {
        background: linear-gradient(180deg, <?= $GLOBALS['muy_oscuro1'] ?> 0%, <?= $GLOBALS['muy_oscuro2'] ?> 100%);
    }
}

.pills-appbar__brand {
    flex-shrink: 0;
}

.pills-appbar__logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.25rem;
    padding: 0 12px;
    border-radius: var(--pills-radius);
    font-weight: 700;
    letter-spacing: 0.04em;
    font-size: 0.95rem;
    color: #fff;
    background: linear-gradient(135deg, var(--pills-accent) 0%, var(--pills-accent-strong) 100%);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.pills-appbar__groups {
    flex: 1;
    min-width: 0;
}

.pills-groups {
    display: flex;
    flex-wrap: nowrap;
    gap: 6px;
    margin: 0;
    padding: 0;
    list-style: none;
    overflow-x: auto;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}

.pills-groups::-webkit-scrollbar {
    height: 4px;
}

.pills-groups::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.25);
    border-radius: 4px;
}

.pills-pill {
    flex: 0 0 auto;
    border: 1px solid rgba(255, 255, 255, 0.22);
    background: rgba(255, 255, 255, 0.08);
    color: #f1f5f9;
    font: inherit;
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 7px 14px;
    border-radius: var(--pills-pill-radius);
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.15s ease;
    white-space: nowrap;
}

.pills-pill:hover {
    background: rgba(255, 255, 255, 0.16);
    border-color: rgba(255, 255, 255, 0.35);
}

.pills-pill--active {
    background: #fff !important;
    color: var(--pills-accent-strong) !important;
    border-color: #fff !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
}

.pills-appbar__user {
    flex-shrink: 0;
    position: relative;
}

.pills-user-wrap {
    position: relative;
}

.pills-user-trigger {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid rgba(255, 255, 255, 0.28);
    background: rgba(255, 255, 255, 0.1);
    color: #f8fafc;
    font: inherit;
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 7px 12px;
    border-radius: var(--pills-pill-radius);
    cursor: pointer;
    transition: background 0.2s ease;
}

.pills-user-trigger:hover {
    background: rgba(255, 255, 255, 0.2);
}

.pills-user-trigger__icon {
    font-size: 0.55rem;
    color: #86efac;
}

.pills-user-panel {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    min-width: 220px;
    max-width: min(320px, 92vw);
    background: #fff;
    color: var(--pills-text);
    border-radius: var(--pills-radius);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
    border: 1px solid var(--pills-border);
    z-index: 1100;
}

.pills-user-panel[hidden] {
    display: none !important;
}

.pills-user-panel__list {
    list-style: none;
    margin: 0;
    padding: 6px 0;
}

.pills-user-panel__list a {
    display: block;
    padding: 9px 14px;
    color: var(--pills-text);
    text-decoration: none;
    font-size: 0.875rem;
    transition: background 0.15s ease;
}

.pills-user-panel__list a:hover {
    background: var(--pills-surface-2);
}

.pills-user-panel__divider {
    height: 1px;
    margin: 6px 10px;
    background: var(--pills-border);
    list-style: none;
}

.pills-user-meta {
    font-size: 0.78rem;
    color: var(--pills-muted);
}

.pills-modulebar {
    background: var(--pills-surface);
    border-top: 1px solid rgba(255, 255, 255, 0.35);
    padding: 0 10px;
}

.pills-modulebar__menu.horizontal-menu {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 2px;
    list-style: none;
    margin: 0;
    padding: 6px 0 8px;
    min-height: 44px;
}

.pills-modulebar__menu.horizontal-menu > li {
    position: relative;
    display: flex;
    align-items: center;
}

.pills-modulebar__menu.horizontal-menu > li > a {
    display: inline-flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--pills-text);
    border: 1px solid transparent;
    transition: background 0.15s ease, border-color 0.15s ease;
}

.pills-modulebar__menu.horizontal-menu > li > a:hover {
    background: var(--pills-surface-2);
    border-color: var(--pills-border);
}

.pills-modulebar__menu.horizontal-menu > li.active > a {
    background: color-mix(in srgb, var(--pills-accent) 14%, transparent);
    border-color: color-mix(in srgb, var(--pills-accent) 35%, transparent);
    color: var(--pills-accent-strong);
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    .pills-modulebar__menu.horizontal-menu > li.active > a {
        background: <?= $GLOBALS['tono2'] ?>;
        border-color: <?= $GLOBALS['medio'] ?>;
    }
}

.pills-modulebar__menu .dropdown {
    position: absolute;
    top: calc(100% - 1px);
    left: 0;
    margin-top: 0;
    background: #fff;
    min-width: 200px;
    border-radius: var(--pills-radius);
    box-shadow: var(--pills-shadow);
    border: 1px solid var(--pills-border);
    opacity: 0;
    visibility: hidden;
    transform: translateY(0);
    transition: opacity 0.12s ease, visibility 0.12s;
    z-index: 1050;
    display: none;
}

.pills-modulebar__menu.horizontal-menu > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

.pills-modulebar__menu .dropdown ul {
    list-style: none;
    margin: 0;
    padding: 6px 0;
}

.pills-modulebar__menu .dropdown li {
    position: relative;
}

.pills-modulebar__menu .dropdown a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    color: var(--pills-text);
    text-decoration: none;
    font-size: 0.84rem;
    border-left: 3px solid transparent;
}

.pills-modulebar__menu .dropdown a:hover {
    background: var(--pills-surface-2);
    border-left-color: var(--pills-accent);
}

.pills-modulebar__menu .dropdown .dropdown {
    top: 0;
    left: calc(100% - 4px);
    margin-top: 0;
    margin-left: 0;
}

.pills-modulebar__menu .dropdown > ul > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

.pills-modulebar__menu .has-submenu::after {
    content: '\25BC';
    font-size: 0.55rem;
    margin-left: 6px;
    opacity: 0.75;
}

.pills-modulebar__menu .dropdown .has-submenu::after {
    content: '\25B6';
    font-size: 0.55rem;
}

#contenido_sin_menus {
    position: relative;
    margin-left: 0 !important;
    margin-top: 104px;
    padding-top: 6px;
    background: var(--pills-canvas);
}

@media (max-width: 640px) {
    .pills-appbar {
        flex-wrap: wrap;
    }

    .pills-appbar__groups {
        order: 3;
        flex-basis: 100%;
        padding-bottom: 2px;
    }

    #contenido_sin_menus {
        margin-top: 128px;
    }
}
</style>
