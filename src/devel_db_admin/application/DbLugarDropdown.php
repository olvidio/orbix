<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Datos del desplegable de delegación (dl) filtradas por región, para pantallas de administración de BD.
 */
final class DbLugarDropdown
{
    /**
     * Payload JSON estándar de desplegable (contrato refactor.md).
     *
     * @return array{id: string, opciones: array<string, string>, selected: string, blanco: bool}
     */
    public static function getData(string $region, DelegacionRepositoryInterface $repoDl): array
    {
        return [
            'id' => 'dl',
            'opciones' => $region === '' ? [] : self::opcionesPorRegion($region, $repoDl),
            'selected' => '',
            'blanco' => false,
        ];
    }

    /**
     * @return array<string, string> valor => etiqueta
     */
    public static function opcionesPorRegion(string $region, DelegacionRepositoryInterface $repoDl): array
    {
        $aWhere = ['active' => true];
        if ($region !== '') {
            $aWhere['region'] = $region;
        }
        $cDelegaciones = $repoDl->getDelegaciones($aWhere, ['_ordre' => 'dl']);
        $aOpciones = [];
        foreach ($cDelegaciones as $oDeleg) {
            $dlValue = $oDeleg->getDlVo()->value();
            if (!is_string($dlValue) || $dlValue === '') {
                continue;
            }
            $dl = $dlValue;
            $aOpciones[$dl] = $dl;
        }
        if ($region !== '') {
            $aOpciones[$region] = (string) _("para gestión global");
        }

        return $aOpciones;
    }
}
