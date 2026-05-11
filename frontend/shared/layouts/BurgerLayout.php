<?php

namespace frontend\shared\layouts;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;

/**
 * Layout hamburguesa (grupmenús en columna).
 *
 * Datos de menú vía HTTP (`src/menus`).
 */
class BurgerLayout implements LayoutInterface
{
    /** @var array<string, mixed> */
    private array $menuConfigArray = [];

    /**
     * @var array<int|string, string>
     */
    private array $listaGrupMenu = [];

    public function generateMenuHtml(array $params): array
    {
        $this->listaGrupMenu = $params['listaGrupMenu'] ?? [];

        $payload = PostRequest::getDataFromUrl('/src/menus/menus_burger_layout_data', [
            'lista_grup_menu_json' => json_encode($this->listaGrupMenu, JSON_UNESCAPED_UNICODE),
        ]);
        $this->menuConfigArray = [];
        $user_menus = '';
        if (is_array($payload)) {
            if (isset($payload['menu_config']) && is_array($payload['menu_config'])) {
                $this->menuConfigArray = $payload['menu_config'];
            }
            if (isset($payload['user_menus_html']) && is_string($payload['user_menus_html'])) {
                $user_menus = $payload['user_menus_html'];
            }
        }

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

        if (isset($params['grupMenuData'])) {
            ksort($params['grupMenuData']);

            foreach ($params['grupMenuData'] as $grupMenuItem) {
                $id_gm = $grupMenuItem['id_gm'];
                $grup_menu = $grupMenuItem['grup_menu'];
                $html_aside .= "<li><a href='#' onclick=\"setActiveGroup(this, '$grup_menu');\" >$grup_menu</a></li>";
            }
        }

        $html_exit = '<li><a href="#" onclick="fnjs_logout();" >' . ucfirst(_('salir'))
            . ' (' . OrbixRuntime::miUsuario() . '[' . OrbixRuntime::miRegionDl() . '])'
            . '</a></li>';
        $html_aside .= $html_exit;

        $html_aside .= '</ul>';
        $html_aside .= '</nav>';
        $html_aside .= '</aside>';

        $li_submenus = '';
        $htmlComponents['li_submenus'] = $li_submenus;
        $htmlComponents['html_aside'] = $html_aside;
        $htmlComponents['user_menus'] = $user_menus;

        return $htmlComponents;
    }

    public function includeCss(array $params): string
    {
        ob_start();

        include_once (OrbixRuntime::dirEstilos() . '/layout_hamburguesa.css.php');

        return ob_get_clean();
    }

    public function includeJs(array $params): string
    {
        $defaultGrupMenu = (empty($params['id_grupmenu'])) ? '' : ($this->listaGrupMenu[$params['id_grupmenu']] ?? '');
        $menuJson = json_encode($this->menuConfigArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($menuJson === false) {
            $menuJson = '{}';
        }
        ob_start();
        ?>
        <!-- Configuración de menús por grupo -->
        <script>
            const defaultGrupMenu = '<?= htmlspecialchars((string)$defaultGrupMenu, ENT_QUOTES, 'UTF-8') ?>';
        </script>
        <!--  para layout hamburguesa -->
        <script>
            <?php
            include_once (OrbixRuntime::dirScripts() . '/layout_hamburguesa.js.php');
            ?>
        </script>
        <!-- Configuración de menús por grupo -->
        <script>
            const menuConfig = <?= $menuJson ?>;
        </script>
        <?php

        return ob_get_clean();
    }

    public function renderHtml(array $htmlComponents, array $params): string
    {
        $li_submenus = $htmlComponents['li_submenus'] ?? '';
        $html_aside = $htmlComponents['html_aside'] ?? '';
        $user_menus = $htmlComponents['user_menus'] ?? '';

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
                        <a href="#" class="user-menu">👤 Utilidades</a>
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

        return ob_get_clean();
    }
}
