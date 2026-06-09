<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use function frontend\shared\helpers\payload_string;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\BotonesCurso;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 1301 en frontend.
 *
 * @see \src\asistentes\application\Select_actividades_de_una_persona::getSegmentData()
 */
final class SelectActividadesDeUnaPersonaRender
{
    /**
     * @param array<string, mixed> $seg
     */
    public static function render(array $seg): string
    {
        $wrapper = isset($seg['wrapper']) && is_array($seg['wrapper']) ? $seg['wrapper'] : [];
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $relForm = payload_string($wrapper, 'url_form_relative');
        $urlForm = $relForm !== '' ? $base . '/' . ltrim($relForm, '/') : '';
        $elimPath = payload_string($wrapper, 'url_eliminar_path');
        $urlEliminar = $elimPath !== '' ? $base . '/' . ltrim($elimPath, '/') : '';

        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm(payload_string($hash, 'campos_form'));
        $oHashSelect->setCamposNo(payload_string($hash, 'campos_no'));
        $hidden = asistentes_hash_campos_hidden($hash['campos_hidden'] ?? []);
        $oHashSelect->setArrayCamposHidden($hidden);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(payload_string($tabla, 'id_tabla', 'select_actividades_de_una_persona'));
        $oTabla->setCabeceras(actividades_lista_cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(actividades_lista_botones($tabla['botones'] ?? []));
        $oTabla->setDatos(actividades_lista_datos($tabla['valores'] ?? []));

        $modoCurso = tessera_imprimir_int($seg['modo_curso'] ?? 1);
        $oBotonesCurso = new BotonesCurso($modoCurso);

        $aLinks_dl = asistentes_sign_link_map($seg['links_dl_specs'] ?? []);
        $aLinks_otros = asistentes_sign_link_map($seg['links_otros_specs'] ?? []);

        $msgErr = payload_string($seg, 'msg_err');

        $oView = new ViewNewPhtml('frontend\asistentes\view');

        return $msgErr . $oView->renderizar('select_actividades_de_una_persona.phtml', [
            'oTabla' => $oTabla,
            'oBotonesCurso' => $oBotonesCurso,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $aLinks_dl,
            'aLinks_otros' => $aLinks_otros,
            'txt_eliminar' => payload_string($wrapper, 'txt_eliminar'),
            'bloque' => payload_string($wrapper, 'bloque'),
            'url_form' => $urlForm,
            'url_eliminar' => $urlEliminar,
        ], false);
    }
}
