<?php

declare(strict_types=1);

namespace src\usuarios\infrastructure\security;

use Closure;
use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

/**
 * Enlace del correo de recuperación del doble factor (2FA), sin sesión
 * (página `frontend/usuarios/controller/recovery.php`).
 *
 * - {@see comprobar()}: el enlace (usuario + token) vale si el token coincide y no ha caducado.
 * - {@see desactivar()}: con un enlace válido, quita el 2FA del usuario y anula el enlace. **No abre
 *   sesión**: el usuario entra después con su contraseña y configura un 2FA nuevo.
 *
 * El token se guarda como sha256 en `aux_usuarios.token_recuperacion_2fa`, con la caducidad en UTC
 * (`recuperar_2fa_mail.php`). La conexión es la del esquema del usuario: sv-e si acaba en `v`, sf-e si
 * acaba en `f` y el servidor es el de sf, y si no, `comun_select` con el esquema sin la letra.
 */
final class Recuperacion2fa
{
    /**
     * @param Closure(string): ?PDO $pdo conexión para el esquema del enlace (null si no se puede abrir)
     */
    public function __construct(private Closure $pdo)
    {
    }

    public static function porDefecto(string $ubicacion, string $private): self
    {
        return new self(static function (string $esquema) use ($ubicacion, $private): ?PDO {
            try {
                return self::conexion($esquema, $ubicacion, $private);
            } catch (\Throwable) {
                return null;
            }
        });
    }

    public function comprobar(string $esquema, int $idUsuario, string $token): bool
    {
        return $this->pdoSiValido($esquema, $idUsuario, $token) !== null;
    }

    public function desactivar(string $esquema, int $idUsuario, string $token): bool
    {
        $pdo = $this->pdoSiValido($esquema, $idUsuario, $token);
        if ($pdo === null) {
            return false;
        }
        // Sin esquema fijo: la conexión ya apunta al esquema del usuario.
        $st = $pdo->prepare('UPDATE aux_usuarios SET has_2fa = false, secret_2fa = NULL, token_recuperacion_2fa = NULL, token_expiracion_2fa = NULL WHERE id_usuario = :id');

        return $st !== false && $st->execute([':id' => $idUsuario]);
    }

    private function pdoSiValido(string $esquema, int $idUsuario, string $token): ?PDO
    {
        if ($token === '' || $esquema === '' || $idUsuario <= 0) {
            return null;
        }
        $pdo = ($this->pdo)($esquema);
        if ($pdo === null) {
            return null;
        }
        try {
            // Comparar en UTC: la expiración se guarda con gmdate() en recuperar_2fa_mail.php.
            $st = $pdo->prepare("SELECT id_usuario FROM aux_usuarios WHERE id_usuario = :id AND token_recuperacion_2fa = :hash AND token_expiracion_2fa > (now() AT TIME ZONE 'utc')");
            $valido = $st !== false
                && $st->execute([':id' => $idUsuario, ':hash' => hash('sha256', $token)])
                && $st->fetchColumn() !== false;
        } catch (\Throwable) {
            $valido = false;
        }

        return $valido ? $pdo : null;
    }

    private static function conexion(string $esquema, string $ubicacion, string $private): PDO
    {
        $useSfDb = ($ubicacion === 'sf' || $private === 'sf');
        if (substr($esquema, -1) === 'v') {
            return (new DBConnection((new ConfigDB('sv-e'))->getEsquema($esquema)))->getPDO();
        }
        if (substr($esquema, -1) === 'f') {
            if ($useSfDb) {
                try {
                    return (new DBConnection((new ConfigDB('sf-e'))->getEsquema($esquema)))->getPDO();
                } catch (\Throwable) {
                    // sigue con comun, como antes
                }
            }
            $esquema = substr($esquema, 0, -1);
        }

        return (new DBConnection((new ConfigDB('comun_select'))->getEsquema($esquema)))->getPDO();
    }
}
