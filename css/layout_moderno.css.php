<style>
:root {
    --modern-accent: <?= $GLOBALS['medio'] ?>;
    --modern-accent-strong: <?= $GLOBALS['oscuro'] ?>;
    /* Superficies alineadas con el contenido (gris claro, menos blanco puro) */
    --modern-canvas: #e4e7ed;
    --modern-surface: #eef1f7;
    --modern-surface-2: <?= $GLOBALS['tono2'] ?>;
    --modern-border: color-mix(in srgb, <?= $GLOBALS['oscuro'] ?> 12%, transparent);
    --modern-text: <?= $GLOBALS['letras'] ?>;
    --modern-muted: color-mix(in srgb, <?= $GLOBALS['letras'] ?> 55%, transparent);
    --modern-bar-bg: color-mix(in srgb, <?= $GLOBALS['muy_oscuro1'] ?> 92%, white);
    --modern-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
    --modern-radius: 10px;
    --modern-pill-radius: 999px;
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    :root {
        --modern-border: rgba(50, 80, 120, 0.15);
        --modern-muted: #5c6570;
        --modern-bar-bg: <?= $GLOBALS['muy_oscuro1'] ?>;
    }
}

.orbix-layout-moderno *,
.orbix-layout-moderno *::before,
.orbix-layout-moderno *::after {
    box-sizing: border-box;
}

body.otro {
    margin: 0;
    min-height: 100vh;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", sans-serif;
    background: var(--modern-canvas);
    color: var(--modern-text);
}

.orbix-layout-moderno {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    gap: 0;
    background: var(--modern-surface);
    border-bottom: 1px solid var(--modern-border);
    box-shadow: var(--modern-shadow);
}

.modern-appbar {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 52px;
    padding: 6px 14px 8px;
    background: linear-gradient(180deg, var(--modern-bar-bg) 0%, color-mix(in srgb, var(--modern-bar-bg) 88%, white) 100%);
    backdrop-filter: blur(10px);
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    .modern-appbar {
        background: linear-gradient(180deg, <?= $GLOBALS['muy_oscuro1'] ?> 0%, <?= $GLOBALS['muy_oscuro2'] ?> 100%);
    }
}

.modern-appbar__brand {
    flex-shrink: 0;
}

.modern-appbar__logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.25rem;
    padding: 0 12px;
    border-radius: var(--modern-radius);
    font-weight: 700;
    letter-spacing: 0.04em;
    font-size: 0.95rem;
    color: #fff;
    background: linear-gradient(135deg, var(--modern-accent) 0%, var(--modern-accent-strong) 100%);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.modern-appbar__groups {
    flex: 1;
    min-width: 0;
}

.modern-pills {
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

.modern-pills::-webkit-scrollbar {
    height: 4px;
}

.modern-pills::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.25);
    border-radius: 4px;
}

.modern-pill {
    flex: 0 0 auto;
    border: 1px solid rgba(255, 255, 255, 0.22);
    background: rgba(255, 255, 255, 0.08);
    color: #f1f5f9;
    font: inherit;
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 7px 14px;
    border-radius: var(--modern-pill-radius);
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.15s ease;
    white-space: nowrap;
}

.modern-pill:hover {
    background: rgba(255, 255, 255, 0.16);
    border-color: rgba(255, 255, 255, 0.35);
}

.modern-pill--active {
    background: #fff !important;
    color: var(--modern-accent-strong) !important;
    border-color: #fff !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
}

.modern-appbar__user {
    flex-shrink: 0;
    position: relative;
}

.modern-user-wrap {
    position: relative;
}

.modern-user-trigger {
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
    border-radius: var(--modern-pill-radius);
    cursor: pointer;
    transition: background 0.2s ease;
}

.modern-user-trigger:hover {
    background: rgba(255, 255, 255, 0.2);
}

.modern-user-trigger__icon {
    font-size: 0.55rem;
    color: #86efac;
}

.modern-user-panel {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    min-width: 220px;
    max-width: min(320px, 92vw);
    background: #fff;
    color: var(--modern-text);
    border-radius: var(--modern-radius);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
    border: 1px solid var(--modern-border);
    z-index: 1100;
}

.modern-user-panel[hidden] {
    display: none !important;
}

.modern-user-panel__list {
    list-style: none;
    margin: 0;
    padding: 6px 0;
}

.modern-user-panel__list a {
    display: block;
    padding: 9px 14px;
    color: var(--modern-text);
    text-decoration: none;
    font-size: 0.875rem;
    transition: background 0.15s ease;
}

.modern-user-panel__list a:hover {
    background: var(--modern-surface-2);
}

.modern-user-panel__divider {
    height: 1px;
    margin: 6px 10px;
    background: var(--modern-border);
    list-style: none;
}

.modern-user-meta {
    font-size: 0.78rem;
    color: var(--modern-muted);
}

.modern-modulebar {
    background: var(--modern-surface);
    border-top: 1px solid rgba(255, 255, 255, 0.35);
    padding: 0 10px;
}

.modern-modulebar__menu.horizontal-menu {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 2px;
    list-style: none;
    margin: 0;
    padding: 6px 0 8px;
    min-height: 44px;
}

.modern-modulebar__menu.horizontal-menu > li {
    position: relative;
    display: flex;
    align-items: center;
}

.modern-modulebar__menu.horizontal-menu > li > a {
    display: inline-flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--modern-text);
    border: 1px solid transparent;
    transition: background 0.15s ease, border-color 0.15s ease;
}

.modern-modulebar__menu.horizontal-menu > li > a:hover {
    background: var(--modern-surface-2);
    border-color: var(--modern-border);
}

.modern-modulebar__menu.horizontal-menu > li.active > a {
    background: color-mix(in srgb, var(--modern-accent) 14%, transparent);
    border-color: color-mix(in srgb, var(--modern-accent) 35%, transparent);
    color: var(--modern-accent-strong);
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    .modern-modulebar__menu.horizontal-menu > li.active > a {
        background: <?= $GLOBALS['tono2'] ?>;
        border-color: <?= $GLOBALS['medio'] ?>;
    }
}

.modern-modulebar__menu .dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 4px;
    background: #fff;
    min-width: 200px;
    border-radius: var(--modern-radius);
    box-shadow: var(--modern-shadow);
    border: 1px solid var(--modern-border);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    z-index: 1050;
    display: none;
}

.modern-modulebar__menu.horizontal-menu > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

.modern-modulebar__menu .dropdown ul {
    list-style: none;
    margin: 0;
    padding: 6px 0;
}

.modern-modulebar__menu .dropdown a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    color: var(--modern-text);
    text-decoration: none;
    font-size: 0.84rem;
    border-left: 3px solid transparent;
}

.modern-modulebar__menu .dropdown a:hover {
    background: var(--modern-surface-2);
    border-left-color: var(--modern-accent);
}

.modern-modulebar__menu .dropdown .dropdown {
    top: 0;
    left: calc(100% - 4px);
    margin-top: 0;
    margin-left: 0;
}

.modern-modulebar__menu .dropdown > ul > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

.modern-modulebar__menu .has-submenu::after {
    content: '\25BC';
    font-size: 0.55rem;
    margin-left: 6px;
    opacity: 0.75;
}

.modern-modulebar__menu .dropdown .has-submenu::after {
    content: '\25B6';
    font-size: 0.55rem;
}

#contenido_sin_menus {
    position: relative;
    margin-left: 0 !important;
    margin-top: 104px;
    padding-top: 6px;
    background: var(--modern-canvas);
}

@media (max-width: 640px) {
    .modern-appbar {
        flex-wrap: wrap;
    }

    .modern-appbar__groups {
        order: 3;
        flex-basis: 100%;
        padding-bottom: 2px;
    }

    #contenido_sin_menus {
        margin-top: 128px;
    }
}
</style>
