<?php

namespace src\notas\application;

use src\notas\domain\contracts\ActaRepositoryInterface;

/**
 * Sube (persiste) el contenido binario de un PDF firmado en el campo
 * `pdf` del acta identificada por `acta_num`.
 *
 * El contenido se lee del array `$files` que tiene la misma forma que
 * `$_FILES` (clave `acta_pdf` generada por bootstrap-fileinput en
 * `acta_ver.phtml`).
 */
final class ActaPdfSubir
{
    public static function execute(array $input, array $files): string
    {
        $acta = (string)($input['acta_num'] ?? '');
        if (empty($acta)) {
            return _("No se encuentra el acta");
        }

        $fileKey = 'acta_pdf';
        if (empty($files[$fileKey])) {
            // bootstrap-fileinput puede llamar sin fichero en algunos flujos
            return '';
        }

        $tmpFilePath = $files[$fileKey]['tmp_name'] ?? '';
        $fileName = $files[$fileKey]['name'] ?? '';
        if (empty($tmpFilePath)) {
            return sprintf(_("No se puede subir el archivo %s"), $fileName);
        }

        $fp = fopen($tmpFilePath, 'rb');
        $contenido_doc = fread($fp, (int)filesize($tmpFilePath));
        fclose($fp);

        $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $oActa = $ActaRepository->findById($acta);
        if ($oActa === null) {
            return _("No se encuentra el acta");
        }
        $oActa->setPdf($contenido_doc);
        if ($ActaRepository->Guardar($oActa) === false) {
            return (string)$oActa->getErrorTxt();
        }

        return '';
    }
}
