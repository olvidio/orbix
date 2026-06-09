<?php

namespace frontend\shared\layouts;

use frontend\shared\config\OrbixRuntime;

/**
 * Layout pills con workspace switcher, tabs de módulos y breadcrumb de contexto.
 *
 * Reutiliza los datos JSON del layout hamburguesa (`src/menus/menus_burger_layout_data`).
 */
class Pills2Layout implements LayoutInterface
{
    use MenusBurgerLayoutSupport;

    public function generateMenuHtml(array $params): array
    {
        $user_menus = $this->loadBurgerMenuPayload($params);

        $usuario = htmlspecialchars(OrbixRuntime::miUsuario(), ENT_QUOTES, 'UTF-8');
        $region = htmlspecialchars(OrbixRuntime::miRegionDl(), ENT_QUOTES, 'UTF-8');

        $html_shell = '<div class="orbix-layout-pills2" id="orbixPills2Shell">';

        $html_shell .= '<header class="pills2-appbar" role="banner">';
        $html_shell .= '<div class="pills2-appbar__brand"><span class="pills2-appbar__logo">Orbix</span></div>';

        $html_shell .= '<div class="pills2-workspace" id="pills2Workspace">';
        $html_shell .= '<button type="button" class="pills2-workspace-trigger" id="pills2WorkspaceTrigger"'
            . ' aria-expanded="false" aria-controls="pills2WorkspacePanel" aria-haspopup="listbox">';
        $html_shell .= '<span class="pills2-workspace-trigger__dot" aria-hidden="true"></span>';
        $html_shell .= '<span class="pills2-workspace-trigger__label" id="pills2WorkspaceLabel">—</span>';
        $html_shell .= '<span class="pills2-workspace-trigger__caret" aria-hidden="true">▾</span>';
        $html_shell .= '</button>';
        $html_shell .= '<div class="pills2-workspace-panel" id="pills2WorkspacePanel" role="listbox" hidden>';
        $html_shell .= '<ul class="pills2-workspace-list" id="pills2WorkspaceList">';
        if (isset($params['grupMenuData']) && is_array($params['grupMenuData'])) {
            foreach (self::layoutGrupMenuItems($params['grupMenuData']) as $grupMenuItem) {
                $esc = htmlspecialchars($grupMenuItem['grup_menu'], ENT_QUOTES, 'UTF-8');
                $html_shell .= '<li role="option">'
                    . '<button type="button" class="pills2-workspace-option pills2-group-link" data-grupo="' . $esc . '">'
                    . $esc
                    . '</button></li>';
            }
        }
        $html_shell .= '</ul></div></div>';

        $html_shell .= '<div class="pills2-appbar__actions">';
        $html_shell .= '<span class="pills2-appbar__user-meta" title="' . $usuario . ' [' . $region . ']">'
            . $usuario . ' <span class="pills2-appbar__region">[' . $region . ']</span></span>';
        $html_shell .= '<nav class="pills2-appbar__user" aria-label="utilidades">';
        $html_shell .= '<div class="pills2-user-wrap">';
        $html_shell .= '<button type="button" class="pills2-user-trigger" id="pills2UserTrigger"'
            . ' aria-expanded="false" aria-controls="pills2UserPanel" onclick="pills2ToggleUserPanel(event);">'
            . _('Utilidades')
            . '</button>';
        $html_shell .= '<div class="pills2-user-panel" id="pills2UserPanel" hidden>';
        $html_shell .= '<ul class="pills2-user-panel__list">';
        $html_shell .= $user_menus;
        $html_shell .= '<li class="pills2-user-panel__divider" role="separator"></li>';
        $html_shell .= '<li><a href="#" onclick="fnjs_logout();">' . ucfirst(_('salir'))
            . ' <span class="pills2-user-meta">(' . $usuario . '[' . $region . '])</span></a></li>';
        $html_shell .= '</ul></div></div></nav></div>';
        $html_shell .= '</header>';

        $html_shell .= '<div class="pills2-modulebar" role="navigation" aria-label="módulos">';
        $html_shell .= '<ul class="horizontal-menu pills2-modulebar__menu" id="horizontalMenu"></ul>';
        $html_shell .= '</div>';

        $html_shell .= '<div class="pills2-contextbar" id="pills2ContextBar" aria-live="polite">';
        $html_shell .= '<nav class="pills2-breadcrumb" id="pills2Breadcrumb" aria-label="contexto"></nav>';
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
        include_once OrbixRuntime::dirEstilos() . '/layout_pills2.css.php';

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
            if (!window.__orbixLayoutPills2JsLoaded) {
                window.__orbixLayoutPills2JsLoaded = true;
            <?php
            include_once OrbixRuntime::dirScripts() . '/layout_pills2.js.php';
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
