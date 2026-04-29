<?php

namespace src\ubiscamas\application;

use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\CamaId;

/**
 * Datos para `frontend/ubiscamas/controller/cama_form.php`.
 * La composición de `HashFront` ocurre en {@see \frontend\ubiscamas\helpers\UbiscamasFormHashCompose::camaForm}.
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

        return [
            'hash_form' => [
                'campos_form' => 'descripcion!larga!vip',
                'campos_chk' => 'larga!vip',
                'campos_hidden' => [
                    'id_cama' => $Qid_cama,
                    'id_habitacion' => $Qid_habitacion,
                    'id_ubi' => $Qid_ubi,
                    'mod' => $Qmod,
                ],
            ],
            'id_cama' => $Qid_cama,
            'id_habitacion' => $Qid_habitacion,
            'id_ubi' => $Qid_ubi,
            'descripcion' => $descripcion,
            'larga' => $larga,
            'vip' => $vip,
        ];
    }
}
