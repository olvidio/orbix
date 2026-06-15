<?php

declare(strict_types=1);

namespace src\shared\application;

use PDOException;
use src\shared\config\ConfigGlobal;
use src\shared\config\ReplicaSelectPolicy;
use src\shared\infrastructure\persistence\postgresql\DBView;

/**
 * Refresca materialized views en instalaciones cr-stgr (H-Hv, M-Mv, …).
 *
 * Se ejecuta desde `global_object.inc` y desde `FrontBootstrap`. No marca la sesión
 * como lista hasta que **todas** las vistas existen y están pobladas.
 *
 * Vistas comun (`av_actividades`, `xa_tipo_tarifa`) se leen en `comun_select` (réplica).
 * Vistas exterior sv-e (`d_asistentes_*`, `d_cargos_activ_dl`) se leen en `sv-e_select`.
 * Fuera de docker {@see DBView} refresca primario y réplica; en monolito docker basta una BD.
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

    private bool $progressOverlayEmitted = false;

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

        $this->progressOverlayEmitted = self::shouldEmitProgressOverlay();
        if ($this->progressOverlayEmitted) {
            self::emitProgressOverlayStart();
        }

        try {
            if ($esquema !== null && $esquema !== '') {
                $this->refreshComunRegionViews($esquema);
            }

            $oMatView = new DBView($schema_vf, $userSfsvInt, 'interior');
            foreach (self::INTERIOR_REGION_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, false);
            }

            $this->refreshExteriorRegionViews($schema_vf, $userSfsvInt);

            if (!$this->allViewsReady($schema_vf, $esquema, $userSfsvInt)) {
                throw new PDOException(_('Tras el refresh siguen faltando vistas materializadas por poblar.'));
            }

            $_SESSION['Refresh'] = 'ok';
            unset($_SESSION['Refresh_error']);

            if ($this->progressOverlayEmitted) {
                self::redirectAfterRefresh();
            }
        } catch (PDOException $e) {
            if ($this->progressOverlayEmitted) {
                $this->reportRefreshFailureOnOverlay($e);
            } else {
                $this->reportRefreshFailure($e);
            }
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

    /**
     * Pantalla de espera en login / index (petición HTML completa, no AJAX).
     */
    private static function shouldEmitProgressOverlay(): bool
    {
        if (self::isSrcJsonRequest() || self::isAjaxRequest() || headers_sent()) {
            return false;
        }

        return true;
    }

    private static function isAjaxRequest(): bool
    {
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return is_string($requestedWith) && strtolower($requestedWith) === 'xmlhttprequest';
    }

    public static function emitProgressOverlayStart(): void
    {
        if (headers_sent()) {
            return;
        }

        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        $titulo = htmlspecialchars(_('Orbix'), ENT_QUOTES, 'UTF-8');
        $mensaje = htmlspecialchars(_('Actualizando vistas materializadas…'), ENT_QUOTES, 'UTF-8');

        echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>'
            . $titulo
            . '</title><style>'
            . 'html,body{height:100%;margin:0;font-family:Arial,sans-serif;background:#eceeee;}'
            . '.refresh-mv-overlay{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(236,238,238,.92);z-index:99999;}'
            . '.refresh-mv-panel{text-align:center;padding:2rem 2.5rem;border:1px solid #42464b;border-radius:8px;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.12);max-width:28rem;}'
            . '.refresh-mv-spinner{width:40px;height:40px;margin:0 auto 1rem;border:4px solid #d0d5d8;border-top-color:#37a69b;border-radius:50%;animation:refresh-mv-spin .9s linear infinite;}'
            . '@keyframes refresh-mv-spin{to{transform:rotate(360deg);}}'
            . '.refresh-mv-panel p{margin:0;color:#42464b;font-size:1.05rem;line-height:1.4;}'
            . '.refresh-mv-error{margin-top:1rem;padding:.75em 1em;border:2px solid #c0392b;background:#fdecea;color:#922b21;text-align:left;}'
            . '</style></head><body>'
            . '<div id="refresh-mv-overlay" class="refresh-mv-overlay" role="status" aria-live="polite">'
            . '<div class="refresh-mv-panel"><div class="refresh-mv-spinner" aria-hidden="true"></div>'
            . '<p>' . $mensaje . '</p></div></div>';

        echo str_repeat(' ', 2048);

        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        flush();
    }

    /**
     * Tras el refresh en login, recarga la misma URL por GET (sin repetir el POST).
     */
    private static function redirectAfterRefresh(): never
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $_SESSION['Refresh_continue_primera'] = 1;
        }

        header('Location: ' . self::redirectUriAfterRefresh(), true, 303);
        exit;
    }

    private static function redirectUriAfterRefresh(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/index.php';
        if (!is_string($uri) || $uri === '') {
            return '/index.php';
        }

        $path = parse_url($uri, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = '/index.php';
        }

        $query = parse_url($uri, PHP_URL_QUERY);

        return $path . ($query !== null && $query !== '' ? '?' . $query : '');
    }

    private function reportRefreshFailureOnOverlay(PDOException $e): never
    {
        $message = _('No puedo refrescar las vistas') . ': ' . $e->getMessage();
        error_log('[RefreshCrStgrMaterializedViews] ' . $message);
        $_SESSION['Refresh_error'] = $message;
        $_SESSION['Refresh'] = 'error';

        $titulo = htmlspecialchars(_('Actualización de datos de región'), ENT_QUOTES, 'UTF-8');
        $detalle = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        echo '<script>document.querySelector(".refresh-mv-spinner")?.remove();</script>'
            . '<div class="refresh-mv-error" role="alert"><strong>' . $titulo . ':</strong> '
            . $detalle . '</div></body></html>';
        exit;
    }

    private function refreshComunRegionViews(string $esquema): void
    {
        foreach ($this->comunRefreshTargets() as $db) {
            $oMatView = new DBView($esquema, null, $db);
            foreach (self::COMUN_SELECT_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, true);
            }
        }
    }

    private function refreshExteriorRegionViews(string $schemaVf, int $userSfsv): void
    {
        foreach ($this->exteriorRefreshTargets($userSfsv) as $db) {
            $oMatView = new DBView($schemaVf, $userSfsv, $db);
            foreach (self::EXTERIOR_SELECT_VIEWS as $view) {
                $this->ensureMaterializedView($oMatView, $view, false);
            }
        }
    }

    /**
     * @return list<'exterior'|'exterior_select'>
     */
    private function exteriorRefreshTargets(int $userSfsv): array
    {
        if ($userSfsv === 1 && ReplicaSelectPolicy::incluirSelect()) {
            return ['exterior', 'exterior_select'];
        }

        return ['exterior_select'];
    }

    /**
     * @return list<'comun'|'comun_select'>
     */
    private function comunRefreshTargets(): array
    {
        if (ReplicaSelectPolicy::incluirSelect()) {
            return ['comun', 'comun_select'];
        }

        return ['comun_select'];
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
