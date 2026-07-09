<?php

namespace frontend\shared\layouts;

use frontend\shared\config\OrbixRuntime;

/**
 * Layout hamburguesa (grupmenús en columna).
 *
 * Datos de menú vía HTTP (`src/menus`).
 */
class BurgerLayout implements LayoutInterface
{
    use MenusBurgerLayoutSupport;

    public function generateMenuHtml(array $params): array
    {
        $user_menus = $this->loadBurgerMenuPayload($params);

        $html_aside = "
          <!-- Botón toggle para móvil -->
            <button class=\"mobile-toggle\" id=\"mobileToggle\"  onclick=\"toggleSidebar()\">
            ☰
                <div class=\"sidebar-header\">
                    <h2 id =\"sidebar-header-h2\">Oficinas</h2>
                </div>
            </button>

            <!-- Overlay para móvil -->
            <div class=\"overlayMenu\" id=\"overlayMenu\" onclick=\"toggleSidebar()\"></div>

            <div class=\"main-container\" id=\"mainContent\">
                <aside class=\"sidebar\" id=\"sidebar\">
                <nav class=\"group-menu\" id=\"groupMenu\">
                    <ul>";

        if (isset($params['grupMenuData']) && is_array($params['grupMenuData'])) {
            foreach (self::layoutGrupMenuItems($params['grupMenuData']) as $grupMenuItem) {
                $grup_menu = $grupMenuItem['grup_menu'];
                $esc = htmlspecialchars($grup_menu, ENT_QUOTES, 'UTF-8');
                $html_aside .= "<li><a href='#' onclick=\"setActiveGroup(this, '" . $esc . "');\" >" . $esc . '</a></li>';
            }
        }

        $html_exit = '<li><a href="#" onclick="fnjs_logout();" >' . ucfirst(_('salir'))
            . ' (' . OrbixRuntime::miUsuario() . '[' . OrbixRuntime::miRegionDl() . '])'
            . '</a></li>';
        $html_aside .= $html_exit;

        $html_aside .= '</ul>';
        $html_aside .= '</nav>';
        $html_aside .= '</aside>';

        return [
            'li_submenus' => '',
            'html_aside' => $html_aside,
            'user_menus' => $user_menus,
        ];
    }

    public function includeCss(array $params): string
    {
        ob_start();

        include_once (OrbixRuntime::dirEstilos() . '/layout_hamburguesa.css.php');

        return (string) ob_get_clean();
    }

    public function includeJs(array $params): string
    {
        $defaultGrupMenu = $this->defaultGrupMenuFromParams($params);
        $menuJson = $this->menuConfigJson();
        ob_start();
        ?>
        <script>
            window.orbixLayout = {
                defaultGrupMenu: <?= json_encode($defaultGrupMenu, JSON_UNESCAPED_UNICODE) ?>,
                menuConfig: <?= $menuJson ?>
            };
            if (!window.__orbixLayoutBurgerJsLoaded) {
                window.__orbixLayoutBurgerJsLoaded = true;
            <?php
            include_once OrbixRuntime::dirScripts() . '/layout_hamburguesa.js.php';
            ?>
            }
        </script>
        <?php

        return (string) ob_get_clean();
    }

    public function renderHtml(array $htmlComponents, array $params): string
    {
        $li_submenus = self::layoutScalarString($htmlComponents['li_submenus'] ?? '');
        $html_aside = self::layoutScalarString($htmlComponents['html_aside'] ?? '');
        $user_menus = self::layoutScalarString($htmlComponents['user_menus'] ?? '');

        ob_start();

        echo $html_aside;
        ?>
        <!-- Contenido principal -->
        <div class="main-content">
        <!-- Menú horizontal superior -->
        <header class="top-menu">
            <nav>
                <ul class="horizontal-menu" id="horizontalMenu">
                    <?= $li_submenus ?>
                </ul>
            </nav>
            <!-- Elemento "Utilidades" alineado a la derecha -->
            <nav class="menu-utilidades-derecha">
                <ul class="user-menu">
                    <li>
                        <a href="#" class="user-menu">👤 <?= _("Utilidades") ?></a>
                        <div class="user-dropdown" id="navUser">
                            <ul>
                                <?= $user_menus ?>
                                <hr>
                                <li><a href="#" onclick="fnjs_logout();"><?= ucfirst(_('salir')) ?>
                                        <?= '(' . htmlspecialchars(OrbixRuntime::miUsuario(), ENT_QUOTES, 'UTF-8') . '[' . htmlspecialchars(OrbixRuntime::miRegionDl(), ENT_QUOTES, 'UTF-8') . '])' ?>
                                    </a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </nav>
        </header>
        <?php

        return (string) ob_get_clean();
    }
}
