<?php

namespace src\encargossacd\application;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;

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
     *     opciones: array<string, string>,
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

        $out = [];
        foreach ($opciones as $k => $v) {
            $out[(string)$k] = (string)$v;
        }

        return [
            'opciones' => $out,
            'selected' => $id_nom,
            'label_prefix' => ucfirst(_("sacd")) . ':&nbsp;&nbsp;&nbsp;',
        ];
    }
}
