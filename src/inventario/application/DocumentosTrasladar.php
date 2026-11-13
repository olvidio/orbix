<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;

/**
 * Trasladar documentos a otro centro (y, si se indica, a un lugar de ese centro): `traslado_doc_guardar`.
 *
 * Sucesor de la lógica del controlador `traslado_doc_guardar.php` (fase 5, paso 5.2). Corrige: sin centro
 * nuevo se intentaba dejar el documento sin centro, cosa que la entidad no admite (excepción «id cannot
 * be null»); ahora se devuelve un error y no se toca nada.
 */
final class DocumentosTrasladar
{
    public function __construct(
        private DocumentoRepositoryInterface $documentoRepository,
    ) {
    }

    /**
     * @param list<int> $idsDoc
     * @return string texto de error ('' si todo fue bien)
     */
    public function execute(array $idsDoc, int $idUbiNuevo, int $idLugarNuevo): string
    {
        if ($idUbiNuevo <= 0) {
            return sprintf(_("Faltan parámetros: %s"), 'id_ubi_new');
        }
        $error = '';
        foreach ($idsDoc as $idDoc) {
            $oDocumento = $this->documentoRepository->findById($idDoc);
            if ($oDocumento === null) {
                continue;
            }
            $oDocumento->setIdUbiVo($idUbiNuevo);
            if ($idLugarNuevo > 0) {
                $oDocumento->setId_lugar($idLugarNuevo);
            }
            if ($this->documentoRepository->Guardar($oDocumento) === false) {
                $error .= _("hay un error, no se ha guardado") . "\n" . $this->documentoRepository->getErrorTxt();
            }
        }

        return $error;
    }
}
