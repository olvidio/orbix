<?php

namespace frontend\shared\layouts;

use frontend\shared\config\OrbixRuntime;

/**
 * Layout compacto de dos bandas (grupos en pills + módulos), sin barra lateral.
 *
 * Reutiliza los datos JSON del layout hamburguesa (`src/menus/menus_burger_layout_data`).
 */
class PillsLayout implements LayoutInterface
{
    use MenusBurgerLayoutSupport;

    public function generateMenuHtml(array $params): array
    {
        $user_menus = $this->loadBurgerMenuPayload($params);

        $html_shell = '<div class="orbix-layout-pills" id="orbixPillsShell">';

        $html_shell .= '<header class="pills-appbar" role="banner">';
        $html_shell .= '<div class="pills-appbar__brand"><span class="pills-appbar__logo">Orbix</span></div>';

        $html_shell .= '<nav class="pills-appbar__groups" aria-label="grupos">';
        $html_shell .= '<ul class="pills-groups" id="pillsGroupMenu">';
        if (isset($params['grupMenuData']) && is_array($params['grupMenuData'])) {
            foreach (self::layoutGrupMenuItems($params['grupMenuData']) as $grupMenuItem) {
                $esc = htmlspecialchars($grupMenuItem['grup_menu'], ENT_QUOTES, 'UTF-8');
                $html_shell .= '<li><button type="button" class="pills-pill pills-group-link" data-grupo="' . $esc . '">' . $esc . '</button></li>';
            }
        }
        $html_shell .= '</ul></nav>';

        $html_shell .= '<nav class="pills-appbar__user" aria-label="utilidades">';
        $html_shell .= '<div class="pills-user-wrap">';
        $html_shell .= '<button type="button" class="pills-user-trigger" id="pillsUserTrigger" aria-expanded="false" aria-controls="pillsUserPanel" onclick="pillsToggleUserPanel(event);">'
            . '<span class="pills-user-trigger__icon">&#9679;</span> ' . _('Utilidades')
            . '</button>';
        $html_shell .= '<div class="pills-user-panel" id="pillsUserPanel" hidden>';
        $html_shell .= '<ul class="pills-user-panel__list">';
        $html_shell .= $user_menus;
        $html_shell .= '<li class="pills-user-panel__divider" role="separator"></li>';
        $html_shell .= '<li><a href="#" onclick="fnjs_logout();">' . ucfirst(_('salir'))
            . ' <span class="pills-user-meta">(' . htmlspecialchars(OrbixRuntime::miUsuario(), ENT_QUOTES, 'UTF-8')
            . '[' . htmlspecialchars(OrbixRuntime::miRegionDl(), ENT_QUOTES, 'UTF-8') . '])</span></a></li>';
        $html_shell .= '</ul></div></div></nav>';
        $html_shell .= '</header>';

        $html_shell .= '<div class="pills-modulebar" role="navigation" aria-label="módulos">';
        $html_shell .= '<ul class="horizontal-menu pills-modulebar__menu" id="horizontalMenu"></ul>';
        $html_shell .= '</div>';

        $html_shell .= '</div>';

        return [
            'li_submenus' => '',
            'html_shell' => $html_shell,
            'user_menus' => $user_menus,
        ];
    }

    public function includeCss(array $params): string
    {
        ob_start();
        include_once OrbixRuntime::dirEstilos() . '/layout_pills.css.php';

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
            if (!window.__orbixLayoutPillsJsLoaded) {
                window.__orbixLayoutPillsJsLoaded = true;
            <?php
            include_once OrbixRuntime::dirScripts() . '/layout_pills.js.php';
            ?>
            }
        </script>
        <?php

        return (string) ob_get_clean();
    }

    public function renderHtml(array $htmlComponents, array $params): string
    {
        $html_shell = self::layoutScalarString($htmlComponents['html_shell'] ?? '');

        ob_start();
        echo $html_shell;

        return (string) ob_get_clean();
    }
}
