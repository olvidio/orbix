<?php

namespace frontend\dossiers\helpers;

use frontend\shared\security\HashFront;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\web\Lista;
use src\shared\domain\DatosTablaRepo;

/**
 * Compone el bloque HTML (form + hash + tabla dossier) para dossiers_ver a partir de los datos
 * planos devueltos por `DossiersVerPantallaData`. Toda la firma de URLs con `HashFront` ocurre
 * aquí (frontend); el backend sólo entrega `*_link_spec` y `script_ctx`.
 */
class DossiersVerFichaDatosTabla
{
    public static function render(array $seg): string
    {
        $titulo = (string) ($seg['titulo'] ?? '');

        $actionTablaUrl = '';
        if (!empty($seg['action_tabla_link_spec']) && is_array($seg['action_tabla_link_spec'])) {
            $actionTablaUrl = HashFrontSignedLink::fromSpec($seg['action_tabla_link_spec']);
        }

        $script = self::buildScript(
            is_array($seg['script_ctx'] ?? null) ? $seg['script_ctx'] : [],
            $actionTablaUrl
        );

        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm((string) ($hash['campos_form'] ?? 'mod'));
        $oHashSelect->setCamposNo((string) ($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $html = '<script>' . $script . '</script>';
        $html .= '<h3 class=subtitulo>' . $titulo . '</h3>'
            . "<form id='seleccionados' name='seleccionados' action='' method='post'>";
        $html .= $oHashSelect->getCamposHtml();
        $html .= "<input type='hidden' id='mod' name='mod' value=''>";

        $valores = $seg['tabla']['valores'] ?? null;
        if (!is_array($valores)) {
            $valores = [];
        }
        $cabeceras = $seg['tabla']['cabeceras'] ?? [];
        $botones = $seg['tabla']['botones'] ?? [];
        if (!is_array($cabeceras)) {
            $cabeceras = [];
        }
        if (!is_array($botones)) {
            $botones = [];
        }

        if (!empty($valores)) {
            $oTabla = new Lista();
            $oTabla->setId_tabla((string) ($seg['tabla']['id_tabla'] ?? 'datos_sql'));
            $oTabla->setCabeceras($cabeceras);
            $oTabla->setBotones($botones);
            $oTabla->setDatos($valores);
            $html .= $oTabla->mostrar_tabla();
        }

        $permiso = (int) ($seg['permiso'] ?? 0);
        if ($permiso === 3) {
            $insTrasladoUrl = '';
            if (!empty($seg['ins_traslado_link_spec']) && is_array($seg['ins_traslado_link_spec'])) {
                $insTrasladoUrl = HashFrontSignedLink::fromSpec($seg['ins_traslado_link_spec']);
            }
            $html .= "<br><table class=botones><tr class=botones>\n"
                . "\t\t\t\t\t<td class=botones><input name=\"btn_new\" type=\"button\" value=\"";
            $html .= _('nuevo');
            if ($insTrasladoUrl !== '') {
                $html .= '" onclick="fnjs_update_div(\'#main\',\'' . $insTrasladoUrl . '\');"></td></tr></table>';
            } else {
                $html .= "\" onclick=\"fnjs_nuevo('#seleccionados');\"></td></tr></table>";
            }
        }

        return $html;
    }

    /**
     * Reusa `DatosTablaRepo::getScript()` inyectando la URL de acción ya firmada.
     * La clase sólo necesita `bloque`, `action_form`, `action_update`, `action_tabla`
     * y `eliminar_txt` para producir el script: no se requieren datos de dominio.
     *
     * @param array<string, mixed> $ctx
     */
    private static function buildScript(array $ctx, string $actionTablaUrl): string
    {
        $repo = new DatosTablaRepo();
        $repo->setBloque((string) ($ctx['bloque'] ?? ''));
        $repo->setAction_form((string) ($ctx['action_form'] ?? ''));
        $repo->setAction_update((string) ($ctx['action_update'] ?? ''));
        $repo->setAction_tabla($actionTablaUrl);
        $repo->setEliminar_txt((string) ($ctx['eliminar_txt'] ?? ''));

        return $repo->getScript();
    }
}
