<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBView;

/**
 * Refresca materialized views en instalaciones cr-stgr (una vez por sesión).
 */
final class RefreshCrStgrMaterializedViews
{
    public function executeIfNeeded(int|string $userSfsv, ?string $esquema, ?string $esquemav, ?string $esquemaf): void
    {
        if (ConfigGlobal::mi_region() !== ConfigGlobal::mi_delef()) {
            return;
        }
        if (isset($_SESSION['Refresh'])) {
            return;
        }

        try {
            if ($userSfsv == 1) {
                $schema_vf = $esquemav;
            } elseif ($userSfsv == 2) {
                $schema_vf = $esquemaf;
            } else {
                return;
            }

            if ($schema_vf === null || $schema_vf === '') {
                return;
            }

            $userSfsvInt = (int) $userSfsv;

            $views = [
                'd_profesor_latin',
                'd_profesor_ampliacion',
                'd_profesor_director',
                'd_profesor_juramento',
                'd_profesor_stgr',
                'd_publicaciones',
                'd_congresos',
                'd_docencia_stgr',
                'd_titulo_est',
                'p_agregados',
                'p_numerarios',
                'personas_dl',
                'd_teleco_personas_dl',
                'u_centros_dl',
            ];

            $oMatView = new DBView($schema_vf, $userSfsvInt, 'interior');
            foreach ($views as $view) {
                $oMatView->setView($view);
                if ($oMatView->ExisteYEsIgual()) {
                    $oMatView->Refresh();
                } else {
                    $oMatView->create();
                }
            }

            $views = [
                'd_asistentes_out',
                'd_asistentes_dl',
                'd_cargos_activ_dl',
            ];

            $oMatView = new DBView($schema_vf, $userSfsvInt, 'exterior_select');
            foreach ($views as $view) {
                $oMatView->setView($view);
                if ($oMatView->ExisteYEsIgual()) {
                    $oMatView->Refresh();
                } else {
                    $oMatView->create();
                }
            }

            if ($esquema === null || $esquema === '') {
                $_SESSION['Refresh'] = 'ok';
                return;
            }

            $views = [
                'av_actividades',
                'xa_tipo_tarifa',
            ];

            $oMatView = new DBView($esquema, null, 'comun_select');
            foreach ($views as $view) {
                $oMatView->setView($view);
                if ($oMatView->ExisteYEsIgual(true)) {
                    $oMatView->Refresh();
                } else {
                    $oMatView->create(true);
                }
            }

            $_SESSION['Refresh'] = 'ok';
        } catch (\PDOException $e) {
            echo '/*';
            echo _('No puedo refrescar las vistas') . ':<br>';
            echo $e->getMessage();
            echo '*/';
            $_SESSION['Refresh'] = 'error';
        }
    }
}
