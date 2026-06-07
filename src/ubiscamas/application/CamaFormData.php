<?php

namespace src\ubiscamas\application;

use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\CamaId;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Datos para `frontend/ubiscamas/controller/cama_form.php`.
 * La composición de `HashFront` ocurre en {@see \frontend\ubiscamas\helpers\UbiscamasFormHashCompose::camaForm}.
 */
final class CamaFormData
{
    public function __construct(
        private CamaDlRepositoryInterface $camaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $Qmod = input_string($input, 'mod');
        $Qid_cama = input_string($input, 'id_cama');
        $Qid_ubi = input_int($input, 'id_ubi');
        $Qid_habitacion = input_string($input, 'id_habitacion');

        $uuid_cama = CamaId::fromNullableString($Qid_cama);
        $descripcion = '';
        $larga = false;
        $vip = false;

        if ($uuid_cama === null) {
            $Qid_cama = Uuid::uuid4()->toString();
        } else {
            $oCama = $this->camaRepository->findById($uuid_cama->value());
            if ($oCama !== null) {
                $Qid_habitacion = $oCama->getIdHabitacionVo()->value();
                $descripcion = $oCama->getDescripcion();
                $larga = $oCama->isLarga() ?? false;
                $vip = $oCama->isVip() ?? false;
            }
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
