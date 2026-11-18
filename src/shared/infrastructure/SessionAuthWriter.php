<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

/**
 * Escribe claves de `$_SESSION['session_auth']` de forma que se guarden
 * aunque la sesión ya esté cerrada.
 *
 * Las peticiones AJAX a `/src/...` pasan por `frontend/shared/controller/src_ajax.php`:
 * `FrontBootstrap::boot()` cierra la sesión (`session_write_close()`) y el
 * controlador se despacha en el mismo proceso. Un `$_SESSION[...] = ...` en ese
 * punto solo cambia la copia en memoria y se pierde al acabar la petición.
 */
final class SessionAuthWriter
{
    /**
     * @param array<string, mixed> $valores clave de `session_auth` => valor
     */
    public static function actualizar(array $valores): void
    {
        $abiertaAqui = false;
        if (session_status() !== PHP_SESSION_ACTIVE && session_id() !== '' && !headers_sent()) {
            session_start();
            $abiertaAqui = true;
        }

        try {
            if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
                return;
            }
            foreach ($valores as $clave => $valor) {
                $_SESSION['session_auth'][$clave] = $valor;
            }
        } finally {
            if ($abiertaAqui) {
                session_write_close();
            }
        }
    }
}
