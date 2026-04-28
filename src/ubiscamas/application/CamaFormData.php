<?php

namespace src\ubiscamas\application;

use Ramsey\Uuid\Uuid;
use src\shared\config\ConfigGlobal;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\CamaId;
use frontend\shared\security\HashFront;

/**
 * Datos para `frontend/ubiscamas/controller/cama_form.php`.
 */
final class CamaFormData
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        $Qmod = (string)($input['mod'] ?? '');
        $Qid_cama = (string)($input['id_cama'] ?? '');
        $Qid_ubi = (int)($input['id_ubi'] ?? 0);
        $Qid_habitacion = isset($input['id_habitacion']) ? (string)$input['id_habitacion'] : '';

        $uuid_cama = CamaId::fromNullableString($Qid_cama);
        $descripcion = '';
        $larga = false;
        $vip = false;

        if ($uuid_cama === null) {
            $Qid_cama = Uuid::uuid4()->toString();
        } else {
            $CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
            $oCama = $CamaRepository->findById($uuid_cama->value());
            $Qid_habitacion = $oCama->getIdHabitacionVo()->value();
            $descripcion = $oCama->getDescripcion() ?? '';
            $larga = $oCama->isLarga() ?? false;
            $vip = $oCama->isVip() ?? false;
        }

        $oHash = new HashFront();
        $oHash->setCamposForm('descripcion!larga!vip');
        $oHash->setCamposChk('larga!vip');
        $oHash->setArraycamposHidden([
            'id_cama' => $Qid_cama,
            'id_habitacion' => $Qid_habitacion,
            'id_ubi' => $Qid_ubi,
            'mod' => $Qmod,
        ]);

        $web = rtrim(ConfigGlobal::getWeb(), '/');

        return [
            'hash_form_html' => $oHash->getCamposHtml(),
            'id_cama' => $Qid_cama,
            'id_habitacion' => $Qid_habitacion,
            'id_ubi' => $Qid_ubi,
            'descripcion' => $descripcion,
            'larga' => $larga,
            'vip' => $vip,
            'cama_update_url' => $web . '/src/ubiscamas/cama_update',
        ];
    }
}
