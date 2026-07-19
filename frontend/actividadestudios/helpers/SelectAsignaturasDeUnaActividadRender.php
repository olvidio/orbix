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
 * Bloque dossier 3005 en frontend: HashFront, Lista, URLs firmadas.
 *
 * @see \src\actividadestudios\application\Select_asignaturas_de_una_actividad::getSegmentData()
 */
final class SelectAsignaturasDeUnaActividadRender
{
    /**
     * @param array<string, mixed> $seg payload de ficha_segmentos (campos tipo/id se pueden ignorar)
     */
    public static function render(array $seg): string
    {
        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo(\frontend\shared\helpers\PayloadCoercion::string($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(\frontend\shared\helpers\PayloadCoercion::string($tabla['id_tabla'] ?? 'select3005'));
        $oTabla->setCabeceras(ActividadesListaSupport::cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(ActividadesListaSupport::botones($tabla['botones'] ?? []));
        $oTabla->setDatos(ActividadesListaSupport::datos($tabla['valores'] ?? []));

        $linkSpec = ActividadestudiosRenderSupport::linkSpec($seg['link_insert_spec'] ?? null);
        $linkInsert = $linkSpec !== null ? DossierTipoFormLinkSpecsSigning::fromSpec($linkSpec) : '';

        $urlForm = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\PayloadCoercion::string($seg['url_form_relative'] ?? '')
        );
        $urlEliminar = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\PayloadCoercion::string($seg['url_actividad_asignatura_eliminar_path'] ?? '')
        );

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');

        return $oView->renderizar('select_asignaturas_de_una_actividad.phtml', [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'link_insert' => $linkInsert,
            'txt_eliminar' => \frontend\shared\helpers\PayloadCoercion::string($seg['txt_eliminar'] ?? ''),
            'txt_no_permiso' => \frontend\shared\helpers\PayloadCoercion::string($seg['txt_no_permiso'] ?? ''),
            'bloque' => \frontend\shared\helpers\PayloadCoercion::string($seg['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_actividad_asignatura_eliminar' => $urlEliminar,
        ], false);
    }
}
