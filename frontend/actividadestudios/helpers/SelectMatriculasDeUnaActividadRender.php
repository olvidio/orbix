<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/actividadestudios_support.php';

use frontend\shared\config\AppUrlConfig;
use function tessera_imprimir_string;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 3103 en frontend: HashFront, Lista agrupada, URLs.
 *
 * @see \src\actividadestudios\application\Select_matriculas_de_una_actividad::getSegmentData()
 */
final class SelectMatriculasDeUnaActividadRender
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
        $elimPath = tessera_imprimir_string($wrapper['url_matricula_eliminar_path'] ?? '');
        $urlEliminar = $elimPath !== '' ? $base . '/' . ltrim($elimPath, '/') : '';

        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm(tessera_imprimir_string($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo(tessera_imprimir_string($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setGrupos(actividadestudios_lista_grupos($tabla['grupos'] ?? []));
        $oTabla->setCabeceras(actividades_lista_cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(actividades_lista_botones($tabla['botones'] ?? []));
        $oTabla->setDatos(actividades_lista_datos($tabla['valores'] ?? []));

        $sinMsg = tessera_imprimir_string($seg['sin_asignaturas_mensaje'] ?? '');
        $err = tessera_imprimir_string($seg['msg_err'] ?? '');

        $html = '';
        if ($sinMsg !== '') {
            $html .= '<p>' . $sinMsg . '</p>';
        }
        $html .= $err;

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');
        $html .= $oView->renderizar('select_matriculas_de_una_actividad.phtml', [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'txt_eliminar' => tessera_imprimir_string($wrapper['txt_eliminar'] ?? ''),
            'nom_activ' => tessera_imprimir_string($wrapper['nom_activ'] ?? ''),
            'url_form' => $urlForm,
            'url_matricula_eliminar' => $urlEliminar,
        ], false);

        return $html;
    }
}
