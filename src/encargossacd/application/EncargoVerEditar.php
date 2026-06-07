<?php

namespace src\encargossacd\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

/**
 * Actualización de encargo desde `encargo_ver` (antes `encargo_ajax.php` que=editar).
 */
final class EncargoVerEditar
{

    public function __construct(
        private EncargoRepositoryInterface $encargoRepository
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $Qfiltro_ctr = input_int($input, 'filtro_ctr');
        $Qsf_sv = empty($Qfiltro_ctr) ? 1 : $Qfiltro_ctr;
        $Qid_enc = input_int($input, 'id_enc');

        $Qid_ubi = input_int($input, 'lst_ctrs');
        $Qid_zona = input_int($input, 'id_zona');
        $Qdesc_enc = input_string($input, 'desc_enc');
        $Qidioma_enc = input_string($input, 'idioma_enc');
        $Qdesc_lugar = input_string($input, 'desc_lugar');
        $Qobserv = input_string($input, 'observ');
        $Qid_tipo_enc = input_string($input, 'id_tipo_enc');

        if ($Qdesc_enc === '') {
            return ['error' => _('Debe llenar el campo descripción')];
        }

        $oEncargo = $this->encargoRepository->findById($Qid_enc);
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
        if ($this->encargoRepository->Guardar($oEncargo) === false) {
            return ['error' => _('hay un error, no se ha guardado') . "\n" . $this->encargoRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
