<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\ubis\application\services\DelegacionDropdown;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Devuelve el payload (id, opciones, selected, blanco) del desplegable de
 * delegaciones organizadoras para el sfsv indicado en `entrada`. El frontend
 * construye el `<select>`.
 */
class ActividadTipoGetDlOrg
{
    public function __construct(
        private DelegacionDropdown $delegacionDropdown,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool}
     */
    public function execute(array $input = []): array
    {
        $sfsv = FuncTablasSupport::inputString($input, 'entrada');
        $dl_default = ConfigGlobal::mi_delef($sfsv);
        $sfsvInt = is_numeric($sfsv) ? (int) $sfsv : 0;

        return [
            'id' => 'dl_org',
            'opciones' => OpcionesDesplegable::enOrden($this->delegacionDropdown->delegacionesURegiones($sfsvInt, true)),
            'selected' => $dl_default,
            'blanco' => true,
        ];
    }
}
