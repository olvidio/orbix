<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\helpers\PayloadCoercion;
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
        $urlForm = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\PayloadCoercion::string($wrapper['url_form_relative'] ?? '')
        );

        $abs = static fn (string $path): string => AppUrlConfig::browserUrlFromAppRelative($path);

        $avisoHtml = implode('', ActividadestudiosRenderSupport::avisoLines($seg['aviso_lines'] ?? []));

        $todosForm = $seg['aviso_todos_form'] ?? null;
        if (is_array($todosForm)) {
            $msg = \frontend\shared\helpers\PayloadCoercion::string($todosForm['mensaje'] ?? '');
            $action = \frontend\shared\helpers\PayloadCoercion::string($todosForm['dossiers_form_action'] ?? 'frontend/dossiers/controller/dossiers_ver.php');
            $hash = isset($todosForm['hash']) && is_array($todosForm['hash']) ? $todosForm['hash'] : [];
            $oHashA = new HashFront();
            $oHashA->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_form'] ?? ''));
            $oHashA->setCamposNo(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_no'] ?? ''));
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
            'txt_eliminar' => \frontend\shared\helpers\PayloadCoercion::string($wrapper['txt_eliminar'] ?? ''),
            'bloque' => \frontend\shared\helpers\PayloadCoercion::string($wrapper['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_matricular' => $abs(\frontend\shared\helpers\PayloadCoercion::string($wrapper['url_matricular_path'] ?? '')),
            'url_matricula_eliminar' => $abs(\frontend\shared\helpers\PayloadCoercion::string($wrapper['url_matricula_eliminar_path'] ?? '')),
            'url_asistente_observ_est' => $abs(\frontend\shared\helpers\PayloadCoercion::string($wrapper['url_asistente_observ_est_path'] ?? '')),
            'url_asistente_plan_est_ok' => $abs(\frontend\shared\helpers\PayloadCoercion::string($wrapper['url_asistente_plan_est_ok_path'] ?? '')),
        ], false);

        $html = $script;
        $cas = $seg['cas'] ?? [];
        if (is_array($cas)) {
            foreach ($cas as $ca) {
                $caRow = ActividadestudiosRenderSupport::stringKeyRow($ca);
                if ($caRow !== []) {
                    $html .= self::renderCa($caRow, $wrapper, $base);
                }
            }
        }
        $emptyMessage = \frontend\shared\helpers\PayloadCoercion::string($seg['empty_cas_message'] ?? '');
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
        $oHashCa->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_form'] ?? ''));
        $oHashCa->setCamposNo(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashCa->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($ca['tabla']) && is_array($ca['tabla']) ? $ca['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(\frontend\shared\helpers\PayloadCoercion::string($tabla['id_tabla'] ?? 'sql_1303'));
        $oTabla->setCabeceras(ActividadesListaSupport::cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(ActividadesListaSupport::botones($tabla['botones'] ?? []));
        $oTabla->setDatos(ActividadesListaSupport::datos($tabla['valores'] ?? []));

        $linkSpec = ActividadestudiosRenderSupport::linkSpec($ca['link_add_spec'] ?? null);
        $linkAdd = $linkSpec !== null ? DossierTipoFormLinkSpecsSigning::fromSpec($linkSpec) : '';

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');
        $inner = $oView->renderizar('selectUnCa.phtml', [
            'oHashCa' => $oHashCa,
            'oTabla' => $oTabla,
            'link_add' => $linkAdd,
            'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string($ca['nom_activ'] ?? ''),
            'form' => \frontend\shared\helpers\PayloadCoercion::string($ca['form'] ?? ''),
            'ca_num' => \frontend\shared\helpers\PayloadCoercion::int($ca['ca_num'] ?? 0),
            'chk_1' => \frontend\shared\helpers\PayloadCoercion::string($ca['chk_1'] ?? ''),
            'chk_2' => \frontend\shared\helpers\PayloadCoercion::string($ca['chk_2'] ?? ''),
            'bloque' => \frontend\shared\helpers\PayloadCoercion::string($wrapper['bloque'] ?? ''),
            'observ_est' => \frontend\shared\helpers\PayloadCoercion::string($ca['observ_est'] ?? ''),
            'permiso' => \frontend\shared\helpers\PayloadCoercion::int($ca['permiso'] ?? 0),
        ], false);

        return \frontend\shared\helpers\PayloadCoercion::string($ca['html_prefix'] ?? '') . $inner;
    }
}
