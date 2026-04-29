<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 1011 en frontend.
 *
 * @see \src\notas\application\Select_notas_de_una_persona::getSegmentData()
 */
final class SelectNotasDeUnaPersonaRender
{
    /**
     * @param array<string, mixed> $seg
     */
    public static function render(array $seg): string
    {
        $paths = isset($seg['paths']) && is_array($seg['paths']) ? $seg['paths'] : [];
        $publicBase = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $elimRel = (string)($paths['persona_nota_eliminar'] ?? '');
        $urlPersonaNotaEliminar = $elimRel !== '' ? $publicBase . '/' . ltrim($elimRel, '/') : '';

        $hashMain = isset($seg['hash_main']) && is_array($seg['hash_main']) ? $seg['hash_main'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposNo((string)($hashMain['campos_no'] ?? ''));
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla((string)($tabla['id_tabla'] ?? 'select_notas_de_una_persona'));
        $oTabla->setCabeceras(is_array($tabla['cabeceras'] ?? null) ? $tabla['cabeceras'] : []);
        $oTabla->setBotones(is_array($tabla['botones'] ?? null) ? $tabla['botones'] : []);
        $oTabla->setDatos(is_array($tabla['valores'] ?? null) ? $tabla['valores'] : []);

        $spec = $seg['link_insert_spec'] ?? null;
        $signed = SelectNotasDeUnaPersonaUrlSigning::sign([
            'link_insert_spec' => is_array($spec) ? $spec : null,
        ]);

        $aviso = (string)($seg['aviso'] ?? '');

        $oView = new ViewNewPhtml('frontend\\notas\\view');

        return $oView->renderizar('select_notas_de_una_persona.phtml', [
            'oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'link_insert' => (string)($signed['link_insert'] ?? ''),
            'txt_eliminar' => (string)($seg['txt_eliminar'] ?? ''),
            'bloque' => (string)($seg['bloque'] ?? ''),
            'aviso' => $aviso,
            'msg' => $aviso,
            'url_persona_nota_eliminar' => $urlPersonaNotaEliminar,
        ], false);
    }
}
