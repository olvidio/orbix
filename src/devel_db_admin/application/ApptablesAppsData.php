<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\configuracion\domain\contracts\AppRepositoryInterface;

/**
 * Lista de aplicaciones para el formulario {@see frontend\devel_db_admin\controller\apptables}.
 */
final class ApptablesAppsData
{
    /**
     * @return array{a_apps: array<int|string, string>}
     */
    public static function build(AppRepositoryInterface $appRepository): array
    {
        $a_apps = [];
        foreach ($appRepository->getApps() as $oApp) {
            $id = $oApp->getIdAppVo()->value();
            $a_apps[$id] = $oApp->getNomVo()->value();
        }

        return ['a_apps' => $a_apps];
    }
}
