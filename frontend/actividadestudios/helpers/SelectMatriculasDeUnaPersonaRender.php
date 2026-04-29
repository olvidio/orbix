<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 1303 en frontend: HashFront, Lista, URLs firmadas.
 *
 * @see \src\actividadestudios\application\Select_matriculas_de_una_persona::getSegmentData()
 */
final class SelectMatriculasDeUnaPersonaRender
{
    /**
     * @param array<string, mixed> $seg
     */
    public static function render(array $seg): string
    {
        $wrapper = isset($seg['wrapper']) && is_array($seg['wrapper']) ? $seg['wrapper'] : [];
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $relForm = (string)($wrapper['url_form_relative'] ?? '');
        $urlForm = $relForm !== '' ? $base . '/' . ltrim($relForm, '/') : '';

        $abs = static function (string $path) use ($base): string {
            return $path !== '' ? $base . '/' . ltrim($path, '/') : '';
        };

        $avisoLines = $seg['aviso_lines'] ?? [];
        if (!is_array($avisoLines)) {
            $avisoLines = [];
        }
        $avisoHtml = implode('', $avisoLines);

        $todosForm = $seg['aviso_todos_form'] ?? null;
        if (is_array($todosForm)) {
            $msg = (string)($todosForm['mensaje'] ?? '');
            $action = (string)($todosForm['dossiers_form_action'] ?? 'frontend/dossiers/controller/dossiers_ver.php');
            $hash = isset($todosForm['hash']) && is_array($todosForm['hash']) ? $todosForm['hash'] : [];
            $oHashA = new HashFront();
            $oHashA->setCamposForm((string)($hash['campos_form'] ?? ''));
            $oHashA->setCamposNo((string)($hash['campos_no'] ?? ''));
            $hiddenA = $hash['campos_hidden'] ?? [];
            $oHashA->setArrayCamposHidden(is_array($hiddenA) ? $hiddenA : []);
            $avisoHtml .= $msg;
            $avisoHtml .= "<form action='" . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . "' method='post'>";
            $avisoHtml .= $oHashA->getCamposHtml();
            $avisoHtml .= "<input type=\"button\" onclick=\"fnjs_enviar_formulario(this.form,'#main')\" value=\"" . htmlspecialchars(_("ver anteriores"), ENT_QUOTES, 'UTF-8') . "\">";
            $avisoHtml .= "</form>";
        }

        $oViewWrap = new ViewNewPhtml('frontend\actividadestudios\view');
        $script = $oViewWrap->renderizar('select_matriculas_de_una_persona.phtml', [
            'aviso' => $avisoHtml,
            'txt_eliminar' => (string)($wrapper['txt_eliminar'] ?? ''),
            'bloque' => (string)($wrapper['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_matricular' => $abs((string)($wrapper['url_matricular_path'] ?? '')),
            'url_matricula_eliminar' => $abs((string)($wrapper['url_matricula_eliminar_path'] ?? '')),
            'url_asistente_observ_est' => $abs((string)($wrapper['url_asistente_observ_est_path'] ?? '')),
            'url_asistente_plan_est_ok' => $abs((string)($wrapper['url_asistente_plan_est_ok_path'] ?? '')),
        ], false);

        $html = $script;
        $cas = $seg['cas'] ?? [];
        if (is_array($cas)) {
            foreach ($cas as $ca) {
                if (is_array($ca)) {
                    $html .= self::renderCa($ca, $wrapper, $base);
                }
            }
        }
        if (($seg['empty_cas_message'] ?? '') !== '') {
            $html .= (string) $seg['empty_cas_message'];
        }

        return $html;
    }

    /**
     * @param array<string, mixed>   $ca
     * @param array<string, mixed>   $wrapper
     */
    private static function renderCa(array $ca, array $wrapper, string $base): string
    {
        $hash = isset($ca['hash']) && is_array($ca['hash']) ? $ca['hash'] : [];
        $oHashCa = new HashFront();
        $oHashCa->setCamposForm((string)($hash['campos_form'] ?? ''));
        $oHashCa->setCamposNo((string)($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashCa->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($ca['tabla']) && is_array($ca['tabla']) ? $ca['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla((string)($tabla['id_tabla'] ?? 'sql_1303'));
        $oTabla->setCabeceras(is_array($tabla['cabeceras'] ?? null) ? $tabla['cabeceras'] : []);
        $oTabla->setBotones(is_array($tabla['botones'] ?? null) ? $tabla['botones'] : []);
        $oTabla->setDatos(is_array($tabla['valores'] ?? null) ? $tabla['valores'] : []);

        $spec = $ca['link_add_spec'] ?? null;
        $linkAdd = is_array($spec) ? DossierTipoFormLinkSpecsSigning::fromSpec($spec) : '';

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');
        $inner = $oView->renderizar('selectUnCa.phtml', [
            'oHashCa' => $oHashCa,
            'oTabla' => $oTabla,
            'link_add' => $linkAdd,
            'nom_activ' => (string)($ca['nom_activ'] ?? ''),
            'form' => (string)($ca['form'] ?? ''),
            'ca_num' => (int)($ca['ca_num'] ?? 0),
            'chk_1' => (string)($ca['chk_1'] ?? ''),
            'chk_2' => (string)($ca['chk_2'] ?? ''),
            'bloque' => (string)($wrapper['bloque'] ?? ''),
            'observ_est' => (string)($ca['observ_est'] ?? ''),
            'permiso' => (int)($ca['permiso'] ?? 0),
        ], false);

        $prefix = (string)($ca['html_prefix'] ?? '');

        return $prefix . $inner;
    }
}
