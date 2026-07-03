<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoGrupo;

/**
 * Alta de encargo desde el formulario de `encargo_ver` (antes `encargo_ajax.php` que=nuevo).
 */
final class EncargoVerNuevo
{

    public function __construct(
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoTipoRepositoryInterface $encargoTipoRepository
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $Qfiltro_ctr = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'filtro_ctr');
        $Qsf_sv = empty($Qfiltro_ctr) ? EncargoGrupo::CENTRO_SV : $Qfiltro_ctr;

        $Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'lst_ctrs');
        $Qid_zona = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_zona');
        $Qdesc_enc = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'desc_enc');
        $Qidioma_enc = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'idioma_enc');
        $Qdesc_lugar = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'desc_lugar');
        $Qobserv = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ');

        $Qid_tipo_enc = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_enc');
        $Qgrupo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'grupo');
        $Qnom_tipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nom_tipo');

        if (!empty($Qid_tipo_enc) && !str_contains($Qid_tipo_enc, '.')) {
            $id_tipo_enc = $Qid_tipo_enc;
        } else {
            $condta = $this->encargoTipoRepository->id_tipo_encargo($Qgrupo, $Qnom_tipo);
            if (!str_contains((string)$condta, '.')) {
                $id_tipo_enc = $condta;
            } else {
                return ['error' => _('Debe seleccionar un tipo de encargo')];
            }
        }

        if ($id_tipo_enc !== '' && ($id_tipo_enc[0] ?? '') === '7') {
            $Qsf_sv = EncargoGrupo::PERSONAL;
        }

        if ($Qdesc_enc === '') {
            return ['error' => _('Debe llenar el campo descripción')];
        }

        $newId = $this->encargoRepository->getNewId();
        $oEncargo = new Encargo();
        $oEncargo->setId_enc($newId);
        $oEncargo->setId_tipo_enc((int)$id_tipo_enc);
        $grupo = EncargoGrupo::fromNullableInt($Qsf_sv);
        if ($grupo === null) {
            return ['error' => _('grupo de encargo no valido')];
        }
        $oEncargo->setGrupoEncargoVo($grupo);
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
