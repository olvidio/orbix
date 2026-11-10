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
            // El filtro es un código de tabla (n, a, sssc, cp_sss…) y getArraySacd() lo interpola en el
            // SQL: solo se admiten minúsculas, dígitos y «_». addslashes() NO protege en PostgreSQL con
            // standard_conforming_strings=on (la barra invertida no escapa la comilla).
            if (preg_match('/^[a-z0-9_]{1,10}$/', $filtro_sacd) !== 1) {
                return [
                    'opciones' => [],
                    'selected' => $id_nom,
                    'label_prefix' => ucfirst(_("sacd")) . ':&nbsp;&nbsp;&nbsp;',
                ];
            }
            $sdonde = sprintf("AND id_tabla='%s' ", $filtro_sacd);
        }

        $opciones = $this->personaDlRepository->getArraySacd($sdonde);

        return [
            'opciones' => OpcionesDesplegable::enOrden($opciones),
            'selected' => $id_nom,
            'label_prefix' => ucfirst(_("sacd")) . ':&nbsp;&nbsp;&nbsp;',
        ];
    }
}
