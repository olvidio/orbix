<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\helpers\SignedDownloadToken;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 1010 en frontend.
 *
 * @see \src\certificados\domain\Select_certificados_de_una_persona::getSegmentData()
 */
final class SelectCertificadosDeUnaPersonaRender
{
    /**
     * @param array<string, mixed> $seg
     */
    public static function render(array $seg): string
    {
        $paths = isset($seg['paths']) && is_array($seg['paths']) ? $seg['paths'] : [];
        $publicBase = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $delRel = tessera_imprimir_string($paths['certificado_recibido_delete'] ?? '');
        $urlCertificadoRecibidoDelete = $delRel !== '' ? $publicBase . '/' . ltrim($delRel, '/') : '';

        $tablaSeg = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $valoresRaw = actividades_lista_datos($tablaSeg['valores'] ?? []);

        $pdfSignedUrls = [];
        foreach ($valoresRaw as $idx => $row) {
            if (!is_int($idx) || !is_array($row) || !isset($row['sel'])) {
                continue;
            }
            $idItem = tessera_imprimir_int($row['sel']);
            if ($idItem <= 0) {
                continue;
            }
            $pdfSignedUrls[(string) $idItem] = SignedDownloadToken::urlCertificadoRecibido($idItem);
        }

        $signed = SelectCertificadosDeUnaPersonaUrlSigning::sign([
            'url_nuevo_spec' => $seg['url_nuevo_spec'] ?? null,
        ]);

        $hashMain = isset($seg['hash_main']) && is_array($seg['hash_main']) ? $seg['hash_main'] : [];
        $oHashSelect = new HashFront();
        $cf = tessera_imprimir_string($hashMain['campos_form'] ?? '');
        if ($cf !== '') {
            $oHashSelect->setCamposForm($cf);
        }
        $oHashSelect->setCamposNo(tessera_imprimir_string($hashMain['campos_no'] ?? ''));
        $oHashSelect->setArrayCamposHidden(certificados_hash_campos_hidden($hashMain['campos_hidden'] ?? []));

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(tessera_imprimir_string($tabla['id_tabla'] ?? 'select_certificados_de_una_persona'));
        $oTabla->setCabeceras(actividades_lista_cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(actividades_lista_botones($tabla['botones'] ?? []));
        $oTabla->setDatos(actividades_lista_datos($tabla['valores'] ?? []));

        $oView = new ViewNewPhtml('frontend\certificados\view');

        return $oView->renderizar('select_certificados_de_una_persona.phtml', [
            'oTabla' => $oTabla,
            'url_nuevo' => $signed['url_nuevo'],
            'oHashSelect' => $oHashSelect,
            'pdf_signed_urls_json' => json_encode($pdfSignedUrls, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
            'url_certificado_recibido_delete' => $urlCertificadoRecibidoDelete,
        ], false);
    }
}
