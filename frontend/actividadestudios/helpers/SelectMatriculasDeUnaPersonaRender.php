<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/actividadestudios_support.php';

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\config\AppUrlConfig;
use function tessera_imprimir_int;
use function tessera_imprimir_string;
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
        $relForm = tessera_imprimir_string($wrapper['url_form_relative'] ?? '');
        $urlForm = $relForm !== '' ? $base . '/' . ltrim($relForm, '/') : '';

        $abs = static function (string $path) use ($base): string {
            return $path !== '' ? $base . '/' . ltrim($path, '/') : '';
        };

        $avisoHtml = implode('', actividadestudios_aviso_lines($seg['aviso_lines'] ?? []));

        $todosForm = $seg['aviso_todos_form'] ?? null;
        if (is_array($todosForm)) {
            $msg = tessera_imprimir_string($todosForm['mensaje'] ?? '');
            $action = tessera_imprimir_string($todosForm['dossiers_form_action'] ?? 'frontend/dossiers/controller/dossiers_ver.php');
            $hash = isset($todosForm['hash']) && is_array($todosForm['hash']) ? $todosForm['hash'] : [];
            $oHashA = new HashFront();
            $oHashA->setCamposForm(tessera_imprimir_string($hash['campos_form'] ?? ''));
            $oHashA->setCamposNo(tessera_imprimir_string($hash['campos_no'] ?? ''));
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
            'txt_eliminar' => tessera_imprimir_string($wrapper['txt_eliminar'] ?? ''),
            'bloque' => tessera_imprimir_string($wrapper['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_matricular' => $abs(tessera_imprimir_string($wrapper['url_matricular_path'] ?? '')),
            'url_matricula_eliminar' => $abs(tessera_imprimir_string($wrapper['url_matricula_eliminar_path'] ?? '')),
            'url_asistente_observ_est' => $abs(tessera_imprimir_string($wrapper['url_asistente_observ_est_path'] ?? '')),
            'url_asistente_plan_est_ok' => $abs(tessera_imprimir_string($wrapper['url_asistente_plan_est_ok_path'] ?? '')),
        ], false);

        $html = $script;
        $cas = $seg['cas'] ?? [];
        if (is_array($cas)) {
            foreach ($cas as $ca) {
                $caRow = actividadestudios_string_key_row($ca);
                if ($caRow !== []) {
                    $html .= self::renderCa($caRow, $wrapper, $base);
                }
            }
        }
        $emptyMessage = tessera_imprimir_string($seg['empty_cas_message'] ?? '');
        if ($emptyMessage !== '') {
            $html .= $emptyMessage;
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
        $oHashCa->setCamposForm(tessera_imprimir_string($hash['campos_form'] ?? ''));
        $oHashCa->setCamposNo(tessera_imprimir_string($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashCa->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($ca['tabla']) && is_array($ca['tabla']) ? $ca['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(tessera_imprimir_string($tabla['id_tabla'] ?? 'sql_1303'));
        $oTabla->setCabeceras(actividades_lista_cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(actividades_lista_botones($tabla['botones'] ?? []));
        $oTabla->setDatos(actividades_lista_datos($tabla['valores'] ?? []));

        $linkSpec = actividadestudios_link_spec($ca['link_add_spec'] ?? null);
        $linkAdd = $linkSpec !== null ? DossierTipoFormLinkSpecsSigning::fromSpec($linkSpec) : '';

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');
        $inner = $oView->renderizar('selectUnCa.phtml', [
            'oHashCa' => $oHashCa,
            'oTabla' => $oTabla,
            'link_add' => $linkAdd,
            'nom_activ' => tessera_imprimir_string($ca['nom_activ'] ?? ''),
            'form' => tessera_imprimir_string($ca['form'] ?? ''),
            'ca_num' => tessera_imprimir_int($ca['ca_num'] ?? 0),
            'chk_1' => tessera_imprimir_string($ca['chk_1'] ?? ''),
            'chk_2' => tessera_imprimir_string($ca['chk_2'] ?? ''),
            'bloque' => tessera_imprimir_string($wrapper['bloque'] ?? ''),
            'observ_est' => tessera_imprimir_string($ca['observ_est'] ?? ''),
            'permiso' => tessera_imprimir_int($ca['permiso'] ?? 0),
        ], false);

        return tessera_imprimir_string($ca['html_prefix'] ?? '') . $inner;
    }
}
