<?php

declare(strict_types=1);

namespace src\shared\application;

use PDOException;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBView;

/**
 * Refresca materialized views en instalaciones cr-stgr (H-Hv, M-Mv, …).
 *
 * Se ejecuta desde `global_object.inc` y desde `FrontBootstrap`. No marca la sesión
 * como lista hasta que **todas** las vistas existen y están pobladas.
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
        if ($sessionRefresh === 'ok' && $this->allViewsReady($schema_vf, $esquema, $userSfsvInt)) {
            return;
        }

        $previousLimit = ini_get('max_execution_time');
        set_time_limit(0);

        $_SESSION['Refresh'] = 'in_progress';
        unset($_SESSION['Refresh_error']);

        try {
            if ($esquema !== null && $esquema !== '') {
                $oMatView = new DBView($esquema, null, 'comun_select');
                foreach (self::COMUN_SELECT_VIEWS as $view) {
                    $this->ensureMaterializedView($oMatView, $view, true);
                }
            }

            $oMatView = new DBView($schema_vf, $userSfsvInt, 'interior');
            foreach (self::INTERIOR_REGION_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, false);
            }

            $oMatView = new DBView($schema_vf, $userSfsvInt, 'exterior_select');
            foreach (self::EXTERIOR_SELECT_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, false);
            }

            if (!$this->allViewsReady($schema_vf, $esquema, $userSfsvInt)) {
                throw new PDOException(_('Tras el refresh siguen faltando vistas materializadas por poblar.'));
            }

            $_SESSION['Refresh'] = 'ok';
            unset($_SESSION['Refresh_error']);
        } catch (PDOException $e) {
            $this->reportRefreshFailure($e);
        } finally {
            if ($previousLimit !== false && $previousLimit !== '') {
                set_time_limit((int) $previousLimit);
            }
        }
    }

    /**
     * Mensaje de error pendiente para mostrar al usuario (HTML escapado en {@see formatUserAvisoHtml}).
     */
    public static function pendingErrorMessage(): ?string
    {
        $message = $_SESSION['Refresh_error'] ?? null;

        return is_string($message) && $message !== '' ? $message : null;
    }

    public static function formatUserAvisoHtml(?string $message = null): string
    {
        $message ??= self::pendingErrorMessage();
        if ($message === null || $message === '') {
            return '';
        }

        $titulo = _('Actualización de datos de región');

        return '<div class="refresh-mv-aviso" role="alert" style="margin:1em 0;padding:0.75em 1em;border:2px solid #c0392b;background:#fdecea;color:#922b21;">'
            . '<strong>' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . ':</strong> '
            . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            . '</div>';
    }

    /**
     * Muestra el aviso en peticiones HTML (no en `/src/…` JSON).
     */
    public static function emitUserAvisoIfHtmlRequest(): void
    {
        if (self::isSrcJsonRequest()) {
            return;
        }

        $html = self::formatUserAvisoHtml();
        if ($html !== '') {
            echo $html;
        }
    }

    public static function isSrcJsonRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($uri)) {
            return false;
        }

        return str_contains($uri, '/src/');
    }

    private function ensureMaterializedView(DBView $oMatView, string $view, bool $comun): void
    {
        $oMatView->setView($view);

        if ($oMatView->exists() && !$oMatView->isPopulated()) {
            $this->refreshViewOrFail($oMatView, $view);

            return;
        }

        if ($oMatView->ExisteYEsIgual($comun)) {
            $this->refreshViewOrFail($oMatView, $view);

            return;
        }

        if (!$oMatView->create($comun)) {
            throw new PDOException(sprintf(
                _('No puedo crear la vista materializada %s'),
                $view,
            ));
        }

        $this->assertViewPopulated($oMatView, $view);
    }

    private function refreshViewOrFail(DBView $oMatView, string $view): void
    {
        if (!$oMatView->Refresh()) {
            throw new PDOException(sprintf(
                _('No puedo refrescar la vista materializada %s'),
                $view,
            ));
        }

        $this->assertViewPopulated($oMatView, $view);
    }

    private function assertViewPopulated(DBView $oMatView, string $view): void
    {
        if (!$oMatView->exists() || !$oMatView->isPopulated()) {
            throw new PDOException(sprintf(
                _('La vista materializada %s sigue sin poblar tras REFRESH'),
                $view,
            ));
        }
    }

    private function reportRefreshFailure(PDOException $e): void
    {
        $message = _('No puedo refrescar las vistas') . ': ' . $e->getMessage();
        error_log('[RefreshCrStgrMaterializedViews] ' . $message);
        $_SESSION['Refresh_error'] = $message;
        $_SESSION['Refresh'] = 'error';
        self::emitUserAvisoIfHtmlRequest();
    }

    private function allViewsReady(string $schemaVf, ?string $esquema, int $userSfsv): bool
    {
        if ($esquema !== null && $esquema !== '') {
            foreach (self::COMUN_SELECT_VIEWS as $view) {
                if (!$this->viewIsReady($esquema, null, 'comun_select', $view)) {
                    return false;
                }
            }
        }

        foreach (self::INTERIOR_REGION_VIEWS as $view) {
            if (!$this->viewIsReady($schemaVf, $userSfsv, 'interior', $view)) {
                return false;
            }
        }

        foreach (self::EXTERIOR_SELECT_VIEWS as $view) {
            if (!$this->viewIsReady($schemaVf, $userSfsv, 'exterior_select', $view)) {
                return false;
            }
        }

        return true;
    }

    private function viewIsReady(string $schema, ?int $userSfsv, string $db, string $view): bool
    {
        $oMatView = new DBView($schema, $userSfsv, $db);
        $oMatView->setView($view);

        return $oMatView->exists() && $oMatView->isPopulated();
    }
}
