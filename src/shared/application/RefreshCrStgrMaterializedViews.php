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
    /** Vistas agregadas región en BD interior (sv/sf), p. ej. matrículas por DL. */
    private const INTERIOR_REGION_VIEWS = [
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
        'd_matriculas_activ_dl',
        'd_asignaturas_activ_dl',
    ];

    private const EXTERIOR_SELECT_VIEWS = [
        'd_asistentes_out',
        'd_asistentes_dl',
        'd_cargos_activ_dl',
    ];

    private const COMUN_SELECT_VIEWS = [
        'av_actividades',
        'xa_tipo_tarifa',
    ];

    public function executeIfNeeded(int|string $userSfsv, ?string $esquema, ?string $esquemav, ?string $esquemaf): void
    {
        if (ConfigGlobal::mi_region() !== ConfigGlobal::mi_delef()) {
            return;
        }

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

        $sessionRefresh = $_SESSION['Refresh'] ?? null;
        if ($sessionRefresh === 'ok' && !$this->viewsNeedRefresh($schema_vf, $esquema, $userSfsvInt)) {
            return;
        }

        try {
            $oMatView = new DBView($schema_vf, $userSfsvInt, 'interior');
            foreach (self::INTERIOR_REGION_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, false);
            }

            $oMatView = new DBView($schema_vf, $userSfsvInt, 'exterior_select');
            foreach (self::EXTERIOR_SELECT_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, false);
            }

            if ($esquema === null || $esquema === '') {
                $_SESSION['Refresh'] = 'ok';
                unset($_SESSION['Refresh_error']);

                return;
            }

            $oMatView = new DBView($esquema, null, 'comun_select');
            foreach (self::COMUN_SELECT_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, true);
            }

            $_SESSION['Refresh'] = 'ok';
            unset($_SESSION['Refresh_error']);
        } catch (\PDOException $e) {
            $this->reportRefreshFailure($e);
        }
    }

    private function ensureMaterializedView(DBView $oMatView, string $view, bool $comun): void
    {
        $oMatView->setView($view);
        if ($oMatView->ExisteYEsIgual($comun)) {
            if (!$oMatView->Refresh()) {
                throw new \PDOException(sprintf(
                    _('No puedo refrescar la vista materializada %s'),
                    $view,
                ));
            }

            return;
        }

        if (!$oMatView->create($comun)) {
            throw new \PDOException(sprintf(
                _('No puedo crear la vista materializada %s'),
                $view,
            ));
        }
    }

    private function reportRefreshFailure(\PDOException $e): void
    {
        $message = _('No puedo refrescar las vistas') . ': ' . $e->getMessage();
        error_log('[RefreshCrStgrMaterializedViews] ' . $message);
        $_SESSION['Refresh_error'] = $message;
        echo '/*';
        echo $message . '<br>';
        echo '*/';
        $_SESSION['Refresh'] = 'error';
    }

    private function viewsNeedRefresh(string $schemaVf, ?string $esquema, int $userSfsv): bool
    {
        if ($this->comunViewsNeedPopulation($esquema)) {
            return true;
        }

        return $this->interiorActividadestudiosViewsNeedRefresh($schemaVf, $userSfsv);
    }

    private function comunViewsNeedPopulation(?string $esquema): bool
    {
        if ($esquema === null || $esquema === '') {
            return false;
        }

        foreach (self::COMUN_SELECT_VIEWS as $view) {
            $oMatView = new DBView($esquema, null, 'comun_select');
            $oMatView->setView($view);
            if ($oMatView->exists() && !$oMatView->isPopulated()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Matrículas/asignaturas CA agregadas por región (consulta vía `oDB` en H-Hv / M-Mv).
     */
    private function interiorActividadestudiosViewsNeedRefresh(string $schemaVf, int $userSfsv): bool
    {
        $oMatView = new DBView($schemaVf, $userSfsv, 'interior');
        foreach (['d_matriculas_activ_dl', 'd_asignaturas_activ_dl'] as $view) {
            $oMatView->setView($view);
            if (!$oMatView->exists()) {
                return true;
            }
            if (!$oMatView->isPopulated()) {
                return true;
            }
        }

        return false;
    }
}
