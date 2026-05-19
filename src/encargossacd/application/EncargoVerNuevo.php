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
    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $Qfiltro_ctr = (int)($input['filtro_ctr'] ?? 0);
        $Qsf_sv = empty($Qfiltro_ctr) ? EncargoGrupo::CENTRO_SV : $Qfiltro_ctr;

        $Qid_ubi = (int)($input['lst_ctrs'] ?? 0);
        $Qid_zona = (int)($input['id_zona'] ?? 0);
        $Qdesc_enc = (string)($input['desc_enc'] ?? '');
        $Qidioma_enc = (string)($input['idioma_enc'] ?? '');
        $Qdesc_lugar = (string)($input['desc_lugar'] ?? '');
        $Qobserv = (string)($input['observ'] ?? '');

        $Qid_tipo_enc = (string)($input['id_tipo_enc'] ?? '');
        $Qgrupo = (string)($input['grupo'] ?? '');
        $Qnom_tipo = (string)($input['nom_tipo'] ?? '');

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

        if (!empty($Qid_tipo_enc) && !str_contains($Qid_tipo_enc, '.')) {
            $id_tipo_enc = $Qid_tipo_enc;
        } else {
            $condta = $EncargoTipoRepository->id_tipo_encargo($Qgrupo, $Qnom_tipo);
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

        $newId = $EncargoRepository->getNewId();
        $oEncargo = new Encargo();
        $oEncargo->setId_enc($newId);
        $oEncargo->setId_tipo_enc((int)$id_tipo_enc);
        $oEncargo->setGrupoEncargoVo(EncargoGrupo::fromNullableInt($Qsf_sv));
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
