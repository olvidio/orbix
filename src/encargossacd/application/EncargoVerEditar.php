<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

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
        $Qfiltro_ctr = FuncTablasSupport::inputInt($input, 'filtro_ctr');
        $Qsf_sv = empty($Qfiltro_ctr) ? 1 : $Qfiltro_ctr;
        $Qid_enc = FuncTablasSupport::inputInt($input, 'id_enc');

        $Qid_ubi = FuncTablasSupport::inputInt($input, 'lst_ctrs');
        $Qid_zona = FuncTablasSupport::inputInt($input, 'id_zona');
        $Qdesc_enc = FuncTablasSupport::inputString($input, 'desc_enc');
        $Qidioma_enc = FuncTablasSupport::inputString($input, 'idioma_enc');
        $Qdesc_lugar = FuncTablasSupport::inputString($input, 'desc_lugar');
        $Qobserv = FuncTablasSupport::inputString($input, 'observ');
        $Qid_tipo_enc = FuncTablasSupport::inputString($input, 'id_tipo_enc');

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
