<style>
:root {
    --pills2-accent: <?= $GLOBALS['medio'] ?>;
    --pills2-accent-strong: <?= $GLOBALS['oscuro'] ?>;
    --pills2-canvas: #e4e7ed;
    --pills2-surface: #eef1f7;
    --pills2-surface-2: <?= $GLOBALS['tono2'] ?>;
    --pills2-border: color-mix(in srgb, <?= $GLOBALS['oscuro'] ?> 12%, transparent);
    --pills2-text: <?= $GLOBALS['letras'] ?>;
    --pills2-muted: color-mix(in srgb, <?= $GLOBALS['letras'] ?> 55%, transparent);
    --pills2-bar-bg: color-mix(in srgb, <?= $GLOBALS['muy_oscuro1'] ?> 92%, white);
    --pills2-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
    --pills2-radius: 10px;
    --pills2-chrome-offset: 120px;
    --pills2-dropdown-min-width: 320px;
    --pills2-dropdown-max-width: min(92vw, 520px);
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    :root {
        --pills2-border: rgba(50, 80, 120, 0.15);
        --pills2-muted: #5c6570;
        --pills2-bar-bg: <?= $GLOBALS['muy_oscuro1'] ?>;
    }
}

.orbix-layout-pills2 *,
.orbix-layout-pills2 *::before,
.orbix-layout-pills2 *::after {
    box-sizing: border-box;
}

body.layout-pills2.otro {
    margin: 0;
    min-height: 100vh;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", sans-serif;
    background: var(--pills2-canvas);
    color: var(--pills2-text);
}

.orbix-layout-pills2 {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    background: var(--pills2-surface);
    border-bottom: 1px solid var(--pills2-border);
    box-shadow: var(--pills2-shadow);
}

/* --- App bar --- */
.pills2-appbar {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 48px;
    padding: 6px 14px;
    background: linear-gradient(180deg, var(--pills2-bar-bg) 0%, color-mix(in srgb, var(--pills2-bar-bg) 88%, white) 100%);
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    .pills2-appbar {
        background: linear-gradient(180deg, <?= $GLOBALS['muy_oscuro1'] ?> 0%, <?= $GLOBALS['muy_oscuro2'] ?> 100%);
    }
}

.pills2-appbar__brand {
    flex-shrink: 0;
}

.pills2-appbar__logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2rem;
    padding: 0 12px;
    border-radius: var(--pills2-radius);
    font-weight: 700;
    letter-spacing: 0.04em;
    font-size: 0.9rem;
    color: #fff;
    background: linear-gradient(135deg, var(--pills2-accent) 0%, var(--pills2-accent-strong) 100%);
}

.pills2-appbar__actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-left: auto;
}

.pills2-appbar__user-meta {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.85);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 10rem;
}

.pills2-appbar__region {
    opacity: 0.75;
}

.pills2-appbar__user {
    flex-shrink: 0;
}

/* --- Workspace switcher --- */
.pills2-workspace {
    position: relative;
    flex-shrink: 0;
}

.pills2-workspace-trigger {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px 6px 10px;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.28);
    background: rgba(255, 255, 255, 0.1);
    color: #f8fafc;
    font: inherit;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease, border-color 0.15s ease;
}

.pills2-workspace-trigger:hover,
.pills2-workspace-trigger[aria-expanded="true"] {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.4);
}

.pills2-workspace-trigger__dot {
    width: 8px;
    height: 8px;
    border-radius: 2px;
    background: var(--pills2-accent);
    flex-shrink: 0;
}

.pills2-workspace-trigger__caret {
    font-size: 0.65rem;
    opacity: 0.8;
}

.pills2-workspace-panel {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    min-width: 220px;
    max-width: min(320px, 92vw);
    background: #fff;
    color: var(--pills2-text);
    border-radius: var(--pills2-radius);
    border: 1px solid var(--pills2-border);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
    z-index: 1100;
}

.pills2-workspace-panel[hidden] {
    display: none !important;
}

.pills2-workspace-list {
    list-style: none;
    margin: 0;
    padding: 4px;
}

.pills2-workspace-option {
    display: block;
    width: 100%;
    text-align: left;
    padding: 9px 12px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: var(--pills2-text);
    font: inherit;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.12s ease;
}

.pills2-workspace-option:hover {
    background: var(--pills2-surface-2);
}

.pills2-workspace-option--active {
    background: color-mix(in srgb, var(--pills2-accent) 12%, transparent);
    color: var(--pills2-accent-strong);
    font-weight: 600;
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    .pills2-workspace-option--active {
        background: <?= $GLOBALS['tono2'] ?>;
        color: <?= $GLOBALS['oscuro'] ?>;
    }
}

/* --- Utilidades --- */
.pills2-user-wrap {
    position: relative;
}

.pills2-user-trigger {
    display: inline-flex;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.28);
    background: rgba(255, 255, 255, 0.1);
    color: #f8fafc;
    font: inherit;
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.15s ease;
}

.pills2-user-trigger:hover {
    background: rgba(255, 255, 255, 0.2);
}

.pills2-user-panel {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    min-width: 220px;
    max-width: min(320px, 92vw);
    background: #fff;
    color: var(--pills2-text);
    border-radius: var(--pills2-radius);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
    border: 1px solid var(--pills2-border);
    z-index: 1100;
}

.pills2-user-panel[hidden] {
    display: none !important;
}

.pills2-user-panel__list {
    list-style: none;
    margin: 0;
    padding: 6px 0;
}

.pills2-user-panel__list a {
    display: block;
    padding: 9px 14px;
    color: var(--pills2-text);
    text-decoration: none;
    font-size: 0.875rem;
    transition: background 0.15s ease;
}

.pills2-user-panel__list a:hover {
    background: var(--pills2-surface-2);
}

.pills2-user-panel__divider {
    height: 1px;
    margin: 6px 10px;
    background: var(--pills2-border);
    list-style: none;
}

.pills2-user-meta {
    font-size: 0.78rem;
    color: var(--pills2-muted);
}

/* --- Módulos (tabs) --- */
.pills2-modulebar {
    background: var(--pills2-surface);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding: 0 8px;
}

.pills2-modulebar__menu.horizontal-menu {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 0;
    list-style: none;
    margin: 0;
    padding: 0;
    min-height: 40px;
    border-bottom: 1px solid var(--pills2-border);
}

.pills2-modulebar__menu.horizontal-menu > li {
    position: relative;
    display: flex;
    align-items: stretch;
}

.pills2-modulebar__menu.horizontal-menu > li > a {
    display: inline-flex;
    align-items: center;
    padding: 10px 14px 8px;
    margin-bottom: -1px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--pills2-muted);
    border-bottom: 2px solid transparent;
    transition: color 0.15s ease, border-color 0.15s ease;
}

.pills2-modulebar__menu.horizontal-menu > li > a:hover {
    color: var(--pills2-text);
}

.pills2-modulebar__menu.horizontal-menu > li.active > a {
    color: var(--pills2-accent-strong);
    border-bottom-color: var(--pills2-accent);
    font-weight: 600;
}

.pills2-modulebar__menu .dropdown {
    position: absolute;
    top: calc(100% - 1px);
    left: 0;
    background: #fff;
    width: max-content;
    min-width: var(--pills2-dropdown-min-width);
    max-width: var(--pills2-dropdown-max-width);
    border-radius: var(--pills2-radius);
    box-shadow: var(--pills2-shadow);
    border: 1px solid var(--pills2-border);
    opacity: 0;
    visibility: hidden;
    z-index: 1050;
    display: none;
}

.pills2-modulebar__menu.horizontal-menu > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    display: block;
}

.pills2-modulebar__menu .dropdown ul {
    list-style: none;
    margin: 0;
    padding: 6px 0;
}

.pills2-modulebar__menu .dropdown li {
    position: relative;
}

.pills2-modulebar__menu .dropdown a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 14px;
    color: var(--pills2-text);
    text-decoration: none;
    font-size: 0.84rem;
    border-left: 3px solid transparent;
    white-space: nowrap;
}

.pills2-modulebar__menu .dropdown a:hover {
    background: var(--pills2-surface-2);
    border-left-color: var(--pills2-accent);
}

.pills2-modulebar__menu .dropdown .dropdown {
    top: 0;
    left: calc(100% - 4px);
    margin-top: 0;
    margin-left: 0;
}

.pills2-modulebar__menu .dropdown > ul > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    display: block;
}

.pills2-modulebar__menu .has-submenu::after {
    content: '\25BC';
    font-size: 0.55rem;
    margin-left: 6px;
    opacity: 0.75;
}

.pills2-modulebar__menu .dropdown .has-submenu::after {
    content: '\25B6';
    font-size: 0.55rem;
}

/* --- Breadcrumb --- */
.pills2-contextbar {
    padding: 7px 14px;
    background: color-mix(in srgb, var(--pills2-surface) 70%, var(--pills2-canvas));
    border-top: 1px solid var(--pills2-border);
    font-size: 0.8125rem;
}

@supports not (color: color-mix(in srgb, black 50%, white)) {
    .pills2-contextbar {
        background: <?= $GLOBALS['tono2'] ?>;
    }
}

.pills2-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin: 0;
    padding: 0;
    list-style: none;
    color: var(--pills2-muted);
}

.pills2-breadcrumb__segment {
    color: var(--pills2-muted);
}

.pills2-breadcrumb__segment--current {
    color: var(--pills2-accent-strong);
    font-weight: 600;
}

.pills2-breadcrumb__sep {
    color: var(--pills2-muted);
    opacity: 0.6;
    user-select: none;
}

body.layout-pills2 #contenido_sin_menus {
    position: relative;
    margin-left: 0 !important;
    margin-top: var(--pills2-chrome-offset);
    padding-top: 6px;
    background: var(--pills2-canvas);
}

@media (max-width: 768px) {
    :root {
        --pills2-chrome-offset: 104px;
    }

    .pills2-appbar__user-meta {
        display: none;
    }

    .pills2-contextbar {
        display: none;
    }

    .pills2-modulebar__menu.horizontal-menu {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}

@media (max-width: 480px) {
    :root {
        --pills2-chrome-offset: 96px;
    }
}
</style>
