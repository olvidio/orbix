<?php

namespace src\encargossacd\application;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;

/**
 * Opciones para el desplegable de SACDs filtrados por tabla
 * (`sacd_ficha_ajax?que=get_select`).
 */
final class SacdSelectData
{

    public function __construct(
        private PersonaDlRepositoryInterface $personaDlRepository
    ) {
    }

    /**
     * @return array{
     *     opciones: list<array{0: string, 1: string}>,
     *     selected: int,
     *     label_prefix: string
     * }
     */
    public function execute(string $filtro_sacd, int $id_nom): array
    {
        $filtro_sacd = trim($filtro_sacd);
        $sdonde = '';
        if ($filtro_sacd !== '') {
            $sdonde = sprintf("AND id_tabla='%s' ", addslashes($filtro_sacd));
        }

        $opciones = $this->personaDlRepository->getArraySacd($sdonde);

        return [
            'opciones' => OpcionesDesplegable::enOrden($opciones),
            'selected' => $id_nom,
            'label_prefix' => ucfirst(_("sacd")) . ':&nbsp;&nbsp;&nbsp;',
        ];
    }
}
