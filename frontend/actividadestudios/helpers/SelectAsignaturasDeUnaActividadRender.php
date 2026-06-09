<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/actividadestudios_support.php';

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\config\AppUrlConfig;
use function tessera_imprimir_string;
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
        $oHashSelect->setCamposForm(tessera_imprimir_string($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo(tessera_imprimir_string($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(tessera_imprimir_string($tabla['id_tabla'] ?? 'select3005'));
        $oTabla->setCabeceras(actividades_lista_cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(actividades_lista_botones($tabla['botones'] ?? []));
        $oTabla->setDatos(actividades_lista_datos($tabla['valores'] ?? []));

        $linkSpec = actividadestudios_link_spec($seg['link_insert_spec'] ?? null);
        $linkInsert = $linkSpec !== null ? DossierTipoFormLinkSpecsSigning::fromSpec($linkSpec) : '';

        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $urlFormRel = tessera_imprimir_string($seg['url_form_relative'] ?? '');
        $urlForm = $urlFormRel !== '' ? $base . '/' . ltrim($urlFormRel, '/') : '';
        $elimPath = tessera_imprimir_string($seg['url_actividad_asignatura_eliminar_path'] ?? '');
        $urlEliminar = $elimPath !== '' ? $base . '/' . ltrim($elimPath, '/') : '';

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');

        return $oView->renderizar('select_asignaturas_de_una_actividad.phtml', [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'link_insert' => $linkInsert,
            'txt_eliminar' => tessera_imprimir_string($seg['txt_eliminar'] ?? ''),
            'txt_no_permiso' => tessera_imprimir_string($seg['txt_no_permiso'] ?? ''),
            'bloque' => tessera_imprimir_string($seg['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_actividad_asignatura_eliminar' => $urlEliminar,
        ], false);
    }
}
