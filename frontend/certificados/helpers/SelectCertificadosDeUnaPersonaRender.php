<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

use frontend\shared\config\AppUrlConfig;
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
        $apiBase = rtrim(AppUrlConfig::getApiBaseUrl(), '/');
        $delRel = (string)($paths['certificado_recibido_delete'] ?? '');
        $pdfRel = (string)($paths['certificado_recibido_pdf_download'] ?? '');
        $urlCertificadoRecibidoDelete = $delRel !== '' ? $publicBase . '/' . ltrim($delRel, '/') : '';
        $urlCertificadoRecibidoPdfDownload = $pdfRel !== '' ? $apiBase . '/' . ltrim($pdfRel, '/') : '';

        $hashPdf = isset($seg['hash_pdf']) && is_array($seg['hash_pdf']) ? $seg['hash_pdf'] : [];
        $oHashDown = new HashFront();
        $oHashDown->setUrl($urlCertificadoRecibidoPdfDownload);
        $oHashDown->setCamposForm((string)($hashPdf['campos_form'] ?? 'key'));
        $h_download = $oHashDown->linkSinVal();

        $urlNuevoSpec = isset($seg['url_nuevo_spec']) && is_array($seg['url_nuevo_spec']) ? $seg['url_nuevo_spec'] : [];
        $signed = SelectCertificadosDeUnaPersonaUrlSigning::sign([
            'url_nuevo_spec' => $urlNuevoSpec,
        ]);

        $hashMain = isset($seg['hash_main']) && is_array($seg['hash_main']) ? $seg['hash_main'] : [];
        $oHashSelect = new HashFront();
        $cf = (string)($hashMain['campos_form'] ?? '');
        if ($cf !== '') {
            $oHashSelect->setCamposForm($cf);
        }
        $oHashSelect->setCamposNo((string)($hashMain['campos_no'] ?? ''));
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla((string)($tabla['id_tabla'] ?? 'select_certificados_de_una_persona'));
        $oTabla->setCabeceras(is_array($tabla['cabeceras'] ?? null) ? $tabla['cabeceras'] : []);
        $oTabla->setBotones(is_array($tabla['botones'] ?? null) ? $tabla['botones'] : []);
        $oTabla->setDatos(is_array($tabla['valores'] ?? null) ? $tabla['valores'] : []);

        $oView = new ViewNewPhtml('frontend\certificados\view');

        return $oView->renderizar('select_certificados_de_una_persona.phtml', [
            'oTabla' => $oTabla,
            'url_nuevo' => (string)($signed['url_nuevo'] ?? ''),
            'oHashSelect' => $oHashSelect,
            'h_download' => $h_download,
            'url_certificado_recibido_delete' => $urlCertificadoRecibidoDelete,
            'url_certificado_recibido_pdf_download' => $urlCertificadoRecibidoPdfDownload,
        ], false);
    }
}
