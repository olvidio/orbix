<style>
    /* ===========================================
       VARIABLES CSS - SISTEMA DE COLORES
       =========================================== */
:root {
    /* Colores principales del tema */
    --primary-color: #3498db;
    --secondary-color: #f39c12;
    --accent-color: #e74c3c;

    /* Sidebar - Menú lateral */
    --sidebar-bg-start: #2c3e50;
    --sidebar-bg-end: #34495e;
    --sidebar-header-bg: rgba(0, 0, 0, 0.2);
    --sidebar-text-color: #ecf0f1;
    --sidebar-border-color: rgba(255, 255, 255, 0.1);
    --sidebar-hover-bg: rgba(255, 255, 255, 0.1);
    --sidebar-active-bg: rgba(52, 152, 219, 0.2);
    --sidebar-shadow: rgba(0, 0, 0, 0.1);

    /* Menú horizontal superior */
    --top-menu-bg-start: #667eea;
    --top-menu-bg-end: #764ba2;
    --top-menu-text-color: white;
    --top-menu-hover-bg: rgba(255, 255, 255, 0.1);
    --top-menu-active-bg: rgba(255, 255, 255, 0.2);
    --top-menu-border-hover: #f39c12;
    --top-menu-shadow: rgba(0, 0, 0, 0.1);

    /* Dropdowns - Submenús */
    --dropdown-bg: white;
    --dropdown-text-color: #5c7fa5;
    --dropdown-hover-bg: #c6d2d8;
    --dropdown-border-hover: #3498db;
    --dropdown-shadow: rgba(0, 0, 0, 0.15);
    --submenu-arrow-color: #000000;
    --submenu-arrow-color-white: #ffffff;

    /* Contenido principal */
    --content-bg: beige;
    --content-section-bg: white;
    --content-title-color: #2c3e50;
    --content-text-color: #666;
    --content-shadow: rgba(0, 0, 0, 0.1);

    /* Botón móvil */
    --mobile-btn-bg: #3498db;
    --mobile-btn-color: white;
    --mobile-btn-shadow: rgba(52, 152, 219, 0.3);

    /* Overlay */
    --overlay-bg: rgba(0, 0, 0, 0.5);

    /* Estados responsive */
    --mobile-border-color: rgba(255, 255, 255, 0.1);
    --mobile-dropdown-bg: rgba(255, 255, 255, 0.1);
}

/* ===========================================
   ESTILOS BASE
   =========================================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body.otro {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--content-bg);
    overflow-x: hidden;

    margin: 0;
    /* font-family: Arial, sans-serif; */
    display: flex; /* Usamos flexbox para que el contenedor principal ocupe toda la altura */
    min-height: 100vh; /* Asegura que el body ocupe al menos el 100% del viewport height */

}

/* Contenedor principal */
.main-container {
    display: flex;
    min-height: 100vh;
    min-width: 100vw;
}

/* ===========================================
   SIDEBAR IZQUIERDO
   =========================================== */
.sidebar {
    width: 200px;
    background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
    box-shadow: 2px 0 10px var(--sidebar-shadow);
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
    transition: transform 0.3s ease;
    top: 42px;
}

.sidebar-header {
    display: block;
    padding: 9px;
    height: 42px;
    background-color: var(--mobile-btn-bg);
    border-bottom: 1px solid var(--sidebar-border-color);
    margin-bottom: 2px;
}

.sidebar-header h2 {
    font-size: 18px;
    text-align: center;
}

.group-menu {
    padding: 10px 0;
    height: auto;
    display: block;
}

.group-menu ul {
    list-style: none;
    display: block;
    height: auto;
}

.group-menu li {
    margin: 5px 0;
    display: block;
}

.group-menu a {
    display: block;
    padding: 5px 20px;
    color: var(--sidebar-text-color);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    position: relative;
    height: auto;
    font-size: 14px;
}

.group-menu a:hover {
    background-color: var(--sidebar-hover-bg);
    border-left-color: var(--primary-color);
    transform: translateX(5px);
}

.group-menu a.active {
    background-color: var(--sidebar-active-bg);
    border-left-color: var(--primary-color);
    color: var(--primary-color);
}

/* ===========================================
   ÁREA DE CONTENIDO PRINCIPAL
   =========================================== */
.main-content {
    flex: 1;
    /*margin-left: 200px;*/
    display: flex;
    flex-direction: column;
    margin-top: 0;
    width: 100vw;
    background-color: var(--sidebar-bg-start);
}

/* ===========================================
   MENÚ HORIZONTAL SUPERIOR
   =========================================== */
.top-menu {
    background: linear-gradient(135deg, var(--top-menu-bg-start) 0%, var(--top-menu-bg-end) 100%);
    box-shadow: 0 2px 10px var(--top-menu-shadow);
    position: fixed;
    top: 0;
    left: 200px;
    right: 0;
    z-index: 999;
    height: 42px;

    flex-shrink: 0; /* Evita que se encoja */
    display: flex; /* Para centrar contenido si es necesario */
    align-items: center; /* Alinea verticalmente */
}

.horizontal-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0 0 0 10px;
    height: 42px;
    gap: 0;
}

.horizontal-menu > li {
    position: relative;
    display: flex;
    align-items: center;
}

.horizontal-menu > li > a {
    display: flex;
    align-items: center;
    padding: 18px 15px;
    color: var(--top-menu-text-color);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    height: 42px;
}

.horizontal-menu > li > a:hover {
    background-color: var(--top-menu-hover-bg);
    border-bottom-color: var(--top-menu-border-hover);
    transform: translateY(-2px);
}

.horizontal-menu > li.active > a {
    background-color: var(--top-menu-active-bg);
    border-bottom-color: var(--top-menu-border-hover);
}


/* ===========================================
   MAIN
   =========================================== */

/* Div que contendrá el fijo a la izquierda y el resto */
#contenido_sin_menus {
    position: relative;
    top: 42px;
    flex-grow: 1; /* Ocupa todo el espacio restante dentro del div-principal */
    display: flex; /* Para organizar el div fijo y el resto */
    padding: 5px; /* Espacio alrededor del contenido */
    overflow: hidden; /* Oculta cualquier desbordamiento inicial */
    background-color: var(--content-bg);
    margin-left: 200px;
}

#contenido_sin_menus.sidebar-open {
    margin-left: 0;
}


/* ===========================================
   SUBMENÚS DESPLEGABLES
   =========================================== */
.dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    background: var(--dropdown-bg);
    min-width: 180px;
    box-shadow: 0 4px 15px var(--dropdown-shadow);
    border-radius: 6px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
    display: none;
}

.horizontal-menu > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

.dropdown ul {
    list-style: none;
    padding: 8px 0;
}

.dropdown li {
    position: relative;
}

.dropdown a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 12px;
    color: var(--dropdown-text-color);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    font-weight: 500;
    border-bottom-color: #c6d2d8;
    border-bottom-style: solid;
    border-bottom-width: 1px;
}

.dropdown a:hover {
    background-color: var(--dropdown-hover-bg);
    border-left-color: var(--dropdown-border-hover);
    color: var(--dropdown-text-color);
}

/* Submenú nivel 2 */
.dropdown .dropdown {
    top: 0;
    left: 100%;
    margin-left: -8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    display: none;
}

.dropdown > ul > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

/* Submenú nivel 3 */
.dropdown .dropdown .dropdown {
    top: 0;
    left: 100%;
    margin-left: -8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    display: none;
}

.dropdown .dropdown > ul > li:hover > .dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

/* ===========================================
   INDICADORES DE SUBMENÚ
   =========================================== */
.has-submenu::after {
    content: '▶';
    font-size: 12px;
    color: var(--submenu-arrow-color);
    transition: transform 0.3s ease;
    margin-left: 12px;
    font-weight: bold;
}

.horizontal-menu .has-submenu::after {
    content: '▼';
    color: var(--submenu-arrow-color-white);
    font-size: 12px;
    margin-left: 10px;
    font-weight: bold;
}

.dropdown .has-submenu::after {
    content: '▶';
    color: var(--submenu-arrow-color) !important;
    font-size: 12px;
    margin-left: 12px;
    font-weight: bold;
}

/* ===========================================
   ÁREA DE CONTENIDO
   =========================================== */
.content-area {
    flex: 1;
    padding: 30px;
    background-color: var(--content-bg);
    margin-top: 42px;
}

.content-section {
    background: var(--content-section-bg);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px var(--content-shadow);
    margin-bottom: 20px;
}

.content-section h2 {
    color: var(--content-title-color);
    margin-bottom: 15px;
    font-size: 24px;
}

.content-section p {
    color: var(--content-text-color);
    line-height: 1.6;
    font-size: 16px;
}

/* ===========================================
   BOTÓN TOGGLE MÓVIL - SIEMPRE VISIBLE
   =========================================== */
.mobile-toggle {
    display: flex;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1001;
    background: var(--mobile-btn-bg);
    color: var(--mobile-btn-color);
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 4px 12px var(--mobile-btn-shadow);
    height: 40px;
    width: 200px;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.mobile-toggle:hover {
    background: var(--primary-color);
    transform: scale(1.05);
}

.mobile-toggle.sidebar-open {
    /*left: 265px;*/
}

.sidebar.hide {
    display: none;
}


/* ===========================================
   OVERLAY MÓVIL
   =========================================== */
.overlayMenu {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--overlay-bg);
    z-index: 999;
}

/*
.overlay.active {
    display: block;
}
*/
/* ===========================================
   RESPONSIVE DESIGN
   =========================================== */
/*
@media (max-width: 1024px) {
    .mobile-toggle {
        display: flex;
    }

    .sidebar {
        transform: translateX(-100%);
        height: 100vh;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
    }

    .top-menu {
        left: 0;
        padding-left: 75px;
    }

    .content-area {
        margin-top: 42px;
    }
}
*/

@media (max-width: 768px) {
     /* En móvil, el sidebar se superpone y no empuja el contenido */
    .main-content.sidebar-open {
        margin-left: 0;
    }

    .top-menu.sidebar-open {
        left: 0;
    }

    .mobile-toggle.sidebar-open {
        left: 15px;
    }

    .overlay.active {
        display: block;
    }

    .horizontal-menu {
        flex-direction: column;
        height: auto;
        padding: 0;
    }

    .horizontal-menu > li {
        display: block;
    }

    .horizontal-menu > li > a {
        padding: 12px 20px;
        border-bottom: 1px solid var(--mobile-border-color);
        height: auto;
        display: block;
        border-bottom-width: 1px;
        border-bottom-style: solid;
    }

    .dropdown {
        position: static;
        opacity: 1;
        visibility: visible;
        transform: none;
        box-shadow: none;
        background: var(--mobile-dropdown-bg);
        border-radius: 0;
        display: none;
    }

    .horizontal-menu > li:hover > .dropdown {
        display: block;
    }

    .content-area {
        padding: 20px 15px;
        margin-top: 42px;
    }

    .top-menu {
        height: auto;
    }
}


/* ===========================================
   MENÚ DE USUARIO (UTILIDADES)
   =========================================== */

.menu-utilidades-derecha {
    margin-left: auto; /* Esto empuja el elemento del menú hacia la derecha */
    margin-right: 10px; // Añade un pequeño margen a la derecha
}


.user-menu {
    position: relative;
    height: 42px;
    display: flex;
    align-items: center;
    color: var(--top-menu-text-color);
    list-style: none;
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--dropdown-bg);
    min-width: 180px;
    box-shadow: 0 4px 15px var(--dropdown-shadow);
    border-radius: 6px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
    display: block;
    margin-top: 8px;
}

.user-dropdown .user-dropdown {
    top: 0;
    left: -180px;
    width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    display: none;
}

.user-dropdown .user-dropdown .user-dropdown {
    top: 0;
    left: -180px;
    width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    display: none;
}

.user-menu > li:hover > .user-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

.user-dropdown ul {
    list-style: none;
    padding: 8px 0;
    margin: 0;
}

.user-dropdown li {
    position: relative;
}

.user-dropdown a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    color: var(--dropdown-text-color);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    font-weight: 500;
    font-size: 14px;
}

.user-dropdown a:hover {
    background-color: var(--dropdown-hover-bg);
    border-left-color: var(--dropdown-border-hover);
    color: var(--dropdown-text-color);
}

.user-dropdown > ul > li:hover > .user-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    display: block;
}

</style>