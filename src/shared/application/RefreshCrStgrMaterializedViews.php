<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBView;

/**
 * Refresca materialized views en instalaciones cr-stgr (H-Hv, M-Mv, …).
 *
 * Se ejecuta desde `global_object.inc` y desde `FrontBootstrap` (antes solo corría
 * en el bootstrap completo; al migrar controladores frontend se perdía el refresh
 * previo a `PostRequest` en la misma petición).
 */
final class RefreshCrStgrMaterializedViews
{
    public function executeIfNeeded(int|string $userSfsv, ?string $esquema, ?string $esquemav, ?string $esquemaf): void
    {
        if (ConfigGlobal::mi_region() !== ConfigGlobal::mi_delef()) {
            return;
        }

        $sessionRefresh = $_SESSION['Refresh'] ?? null;
        if ($sessionRefresh === 'ok' && !$this->comunViewsNeedPopulation($esquema)) {
            return;
        }
        if ($sessionRefresh === 'error') {
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
                $this->ensureMaterializedView($oMatView, $view, false);
            }

            $views = [
                'd_asistentes_out',
                'd_asistentes_dl',
                'd_cargos_activ_dl',
            ];

            $oMatView = new DBView($schema_vf, $userSfsvInt, 'exterior_select');
            foreach ($views as $view) {
                $this->ensureMaterializedView($oMatView, $view, false);
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
                $this->ensureMaterializedView($oMatView, $view, true);
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

    private function ensureMaterializedView(DBView $oMatView, string $view, bool $comun): void
    {
        $oMatView->setView($view);
        if ($oMatView->ExisteYEsIgual($comun)) {
            $oMatView->Refresh();
        } else {
            $oMatView->create($comun);
        }
    }

    private function comunViewsNeedPopulation(?string $esquema): bool
    {
        if ($esquema === null || $esquema === '') {
            return false;
        }

        foreach (['av_actividades', 'xa_tipo_tarifa'] as $view) {
            $oMatView = new DBView($esquema, null, 'comun_select');
            $oMatView->setView($view);
            if ($oMatView->ExisteYEsIgual(true) && !$oMatView->isPopulated()) {
                return true;
            }
        }

        return false;
    }
}
