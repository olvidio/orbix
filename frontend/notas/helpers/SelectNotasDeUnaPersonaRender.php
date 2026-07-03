<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\helpers\PayloadCoercion;

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
        $elimRel = \frontend\shared\helpers\PayloadCoercion::string($paths['persona_nota_eliminar'] ?? '');
        $urlPersonaNotaEliminar = $elimRel !== '' ? $publicBase . '/' . ltrim($elimRel, '/') : '';

        $hashMain = isset($seg['hash_main']) && is_array($seg['hash_main']) ? $seg['hash_main'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposNo(\frontend\shared\helpers\PayloadCoercion::string($hashMain['campos_no'] ?? ''));
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $cabecerasRaw = $tabla['cabeceras'] ?? [];
        /** @var list<array<string, mixed>|string> $cabeceras */
        $cabeceras = [];
        if (is_array($cabecerasRaw)) {
            foreach ($cabecerasRaw as $item) {
                if (is_string($item)) {
                    $cabeceras[] = $item;
                } elseif (is_array($item)) {
                    $cabeceras[] = $item;
                }
            }
        }
        $botonesRaw = $tabla['botones'] ?? [];
        /** @var list<array<string, mixed>> $botones */
        $botones = [];
        if (is_array($botonesRaw)) {
            foreach ($botonesRaw as $item) {
                if (is_array($item)) {
                    $botones[] = $item;
                }
            }
        }
        $valoresRaw = $tabla['valores'] ?? [];

        $oTabla = new Lista();
        $oTabla->setId_tabla(\frontend\shared\helpers\PayloadCoercion::string($tabla['id_tabla'] ?? 'select_notas_de_una_persona'));
        $oTabla->setCabeceras($cabeceras);
        $oTabla->setBotones($botones);
        $oTabla->setDatos(is_array($valoresRaw) ? $valoresRaw : []);

        $spec = $seg['link_insert_spec'] ?? null;
        $linkInsertSpec = null;
        if (is_array($spec)) {
            $path = \frontend\shared\helpers\PayloadCoercion::string($spec['path'] ?? '');
            $queryRaw = $spec['query'] ?? [];
            $query = [];
            if (is_array($queryRaw)) {
                foreach ($queryRaw as $key => $value) {
                    if (is_string($key)) {
                        $query[$key] = $value;
                    }
                }
            }
            $linkInsertSpec = ['path' => $path, 'query' => $query];
        }
        $signed = SelectNotasDeUnaPersonaUrlSigning::sign([
            'link_insert_spec' => $linkInsertSpec,
        ]);

        $aviso = \frontend\shared\helpers\PayloadCoercion::string($seg['aviso'] ?? '');

        $oView = new ViewNewPhtml('frontend\\notas\\view');

        return $oView->renderizar('select_notas_de_una_persona.phtml', [
            'oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'link_insert' => $signed['link_insert'],
            'txt_eliminar' => \frontend\shared\helpers\PayloadCoercion::string($seg['txt_eliminar'] ?? ''),
            'bloque' => \frontend\shared\helpers\PayloadCoercion::string($seg['bloque'] ?? ''),
            'aviso' => $aviso,
            'msg' => $aviso,
            'url_persona_nota_eliminar' => $urlPersonaNotaEliminar,
            'id_sel_value' => \frontend\shared\helpers\PayloadCoercion::string($seg['id_sel_value'] ?? ''),
        ], false);
    }
}
