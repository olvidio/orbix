<?php

namespace frontend\dossiers\helpers;

use frontend\shared\security\HashF;
use frontend\shared\web\Lista;

/**
 * Compone el bloque HTML (form + hash + tabla dossier) para dossiers_ver a partir de los datos
 * planos devueltos por `DossiersVerPantallaData`. Toda la firma de URLs con `HashF` ocurre
 * aquí (frontend); el backend sólo entrega `*_link_spec` y `script_ctx`.
 */
class DossiersVerFichaDatosTabla
{
    /**
     * @param array<string, mixed> $seg
     */
    public static function render(array $seg): string
    {
        $parsed = DossiersSegmentSupport::datosTablaFromSegment($seg);
        $titulo = $parsed['titulo'];
        $actionTablaUrl = $parsed['action_tabla_url'];

        $script = self::buildScript($parsed['script_ctx'], $actionTablaUrl);

        $oHashSelect = new HashF();
        $oHashSelect->setCamposForm($parsed['hash_campos_form']);
        $oHashSelect->setCamposNo($parsed['hash_campos_no']);
        $oHashSelect->setArrayCamposHidden($parsed['hash_campos_hidden']);

        $html = '<script>' . $script . '</script>';
        $html .= '<h3 class=subtitulo>' . $titulo . '</h3>'
            . "<form id='seleccionados' name='seleccionados' action='' method='post'>";
        $html .= $oHashSelect->getCamposHtml();
        $html .= "<input type='hidden' id='mod' name='mod' value=''>";

        $valores = $parsed['tabla_valores'];
        if ($valores !== []) {
            $oTabla = new Lista();
            $oTabla->setId_tabla($parsed['tabla_id']);
            $oTabla->setCabeceras($parsed['tabla_cabeceras']);
            $oTabla->setBotones($parsed['tabla_botones']);
            $oTabla->setDatos($valores);
            $html .= $oTabla->mostrar_tabla();
        }

        if ($parsed['permiso'] === 3) {
            $insTrasladoUrl = $parsed['ins_traslado_url'];
            $html .= "<br><table class=botones><tr class=botones>\n"
                . "\t\t\t\t\t<td class=botones><input name=\"btn_new\" type=\"button\" value=\"";
            $html .= _("nuevo");
            if ($insTrasladoUrl !== '') {
                $html .= '" onclick="fnjs_update_div(\'#main\',\'' . $insTrasladoUrl . '\');"></td></tr></table>';
            } else {
                $html .= "\" onclick=\"fnjs_nuevo('#seleccionados');\"></td></tr></table>";
            }
        }

        return $html;
    }

    /**
     * Script de la tabla con la URL de acción ya firmada.
     *
     * @param array{bloque: string, action_form: string, action_update: string, eliminar_txt: string} $ctx
     */
    private static function buildScript(array $ctx, string $actionTablaUrl): string
    {
        return \frontend\shared\helpers\DatosTablaScript::script(
            $ctx['eliminar_txt'],
            $ctx['bloque'],
            $ctx['action_form'],
            $ctx['action_update'],
            $actionTablaUrl,
        );
    }
}
