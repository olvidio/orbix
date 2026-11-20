<?php

declare(strict_types=1);

namespace src\usuarios\application\support;

use src\shared\security\HashB;
use src\usuarios\domain\entity\Usuario;

/**
 * Lo común de los dos accesos (web y app móvil) al abrir la sesión de un usuario.
 */
final class SesionAlEntrar
{
    /**
     * Copia del usuario para guardar en la sesión: sin el hash de la contraseña ni el secreto 2FA, que no
     * hacen falta después de entrar y no deben quedar en el almacén de sesiones.
     */
    public static function usuarioSinCredenciales(Usuario $usuario): Usuario
    {
        $copia = clone $usuario;
        $copia->setPasswordVo(null);
        $copia->setSecret2faVo(null);

        return $copia;
    }

    /**
     * El usuario ha cambiado su contraseña: la sesión deja de estar limitada al formulario de cambio.
     * La sesión ya está cerrada para escritura en este punto del arranque, así que se reabre un momento.
     */
    public static function cambioDePasswordHecho(): void
    {
        $reabrir = session_status() !== PHP_SESSION_ACTIVE && PHP_SAPI !== 'cli' && !headers_sent();
        if ($reabrir) {
            session_start();
        }
        foreach (['session_auth', 'config'] as $clave) {
            if (is_array($_SESSION[$clave] ?? null) && array_key_exists('expire', $_SESSION[$clave])) {
                $_SESSION[$clave]['expire'] = '';
            }
        }
        // Esta sesión sigue; las demás del usuario, abiertas con la contraseña anterior, se cerrarán.
        \src\shared\infrastructure\ui\http\SesionVigencia::olvidarHuella();
        if ($reabrir) {
            session_write_close();
        }
    }

    /**
     * Sesión nueva para el usuario que entra: no queda nada de la anterior (permisos en caché, navegación),
     * y cambian el identificador de sesión y la clave de firma de las cápsulas.
     */
    public static function iniciarLimpia(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }
        HashB::renovarClave();
    }
}
