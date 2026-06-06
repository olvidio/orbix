<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\ubis\application\services\DelegacionDropdown;

use function src\shared\domain\helpers\input_string;

/**
 * Devuelve el payload (id, opciones, selected, blanco) del desplegable de
 * delegaciones organizadoras para el sfsv indicado en `entrada`. El frontend
 * construye el `<select>`.
 */
class ActividadTipoGetDlOrg
{
    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: array<int|string,string>, selected: string, blanco: bool}
     */
    public function execute(array $input = []): array
    {
        $sfsv = input_string($input, 'entrada');
        $dl_default = ConfigGlobal::mi_delef($sfsv);
        $sfsvInt = is_numeric($sfsv) ? (int) $sfsv : 0;

        return [
            'id' => 'dl_org',
            'opciones' => DelegacionDropdown::delegacionesURegiones($sfsvInt, true),
            'selected' => $dl_default,
            'blanco' => true,
        ];
    }
}
