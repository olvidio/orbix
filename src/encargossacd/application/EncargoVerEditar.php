<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

/**
 * Actualización de encargo desde `encargo_ver` (antes `encargo_ajax.php` que=editar).
 */
final class EncargoVerEditar
{
    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $Qfiltro_ctr = (int)($input['filtro_ctr'] ?? 0);
        $Qsf_sv = empty($Qfiltro_ctr) ? 1 : $Qfiltro_ctr;
        $Qid_enc = (int)($input['id_enc'] ?? 0);

        $Qid_ubi = (int)($input['lst_ctrs'] ?? 0);
        $Qid_zona = (int)($input['id_zona'] ?? 0);
        $Qdesc_enc = (string)($input['desc_enc'] ?? '');
        $Qidioma_enc = (string)($input['idioma_enc'] ?? '');
        $Qdesc_lugar = (string)($input['desc_lugar'] ?? '');
        $Qobserv = (string)($input['observ'] ?? '');
        $Qid_tipo_enc = (string)($input['id_tipo_enc'] ?? '');

        if ($Qdesc_enc === '') {
            return ['error' => _('Debe llenar el campo descripción')];
        }

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $oEncargo = $EncargoRepository->findById($Qid_enc);
        if ($oEncargo === null) {
            return ['error' => sprintf(_('No se encuentra el encargo %d'), $Qid_enc)];
        }

        $oEncargo->setId_tipo_enc((int)$Qid_tipo_enc);
        $oEncargo->setGrupo_encargo($Qsf_sv);
        $oEncargo->setId_ubi($Qid_ubi);
        $oEncargo->setId_zona($Qid_zona);
        $oEncargo->setDesc_enc($Qdesc_enc);
        $oEncargo->setIdioma_enc($Qidioma_enc);
        $oEncargo->setDesc_lugar($Qdesc_lugar);
        $oEncargo->setObserv($Qobserv);
        if ($EncargoRepository->Guardar($oEncargo) === false) {
            return ['error' => _('hay un error, no se ha guardado') . "\n" . $EncargoRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
