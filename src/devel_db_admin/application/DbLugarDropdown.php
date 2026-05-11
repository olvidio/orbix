<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Opciones del desplegable de delegación (dl) filtradas por región, para pantallas de administración de BD.
 */
final class DbLugarDropdown
{
    /**
     * @return array<string, string> valor => etiqueta para {@see \frontend\shared\web\Desplegable}
     */
    public static function opcionesPorRegion(string $region, DelegacionRepositoryInterface $repoDl): array
    {
        $aWhere = ['active' => true];
        if ($region !== '') {
            $aWhere['region'] = $region;
        }
        $cDelegaciones = $repoDl->getDelegaciones($aWhere, ['_ordre' => 'dl']);
        $aOpciones = [];
        if (is_array($cDelegaciones)) {
            foreach ($cDelegaciones as $oDeleg) {
                $dl = $oDeleg->getDlVo()->value();
                $aOpciones[$dl] = $dl;
            }
        }
        $aOpciones[$region] = _("para gestión global");

        return $aOpciones;
    }
}
