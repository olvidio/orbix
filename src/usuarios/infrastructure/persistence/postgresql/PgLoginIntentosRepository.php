<?php

declare(strict_types=1);

namespace src\usuarios\infrastructure\persistence\postgresql;

use Closure;
use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\usuarios\domain\contracts\LoginIntentosRepositoryInterface;

/**
 * Columnas `login_fallos`, `login_bloqueo_hasta` y `totp_ultimo_paso` de `aux_usuarios`
 * (migración 202611181000_aux_usuarios_bloqueo_login_y_totp).
 *
 * Se escribe antes de que haya sesión, así que abre su propia conexión de escritura al esquema del
 * usuario (sv-e o sf-e, como `Recuperacion2fa`). Las fechas se comparan en SQL con `now()`.
 *
 * Si la conexión o las columnas fallan (p. ej. código desplegado antes de aplicar la migración), deja
 * entrar como antes y lo anota en el log de PHP: un fallo aquí no puede impedir el login a todos.
 */
final class PgLoginIntentosRepository implements LoginIntentosRepositoryInterface
{
    /** @var Closure(string, int): ?PDO */
    private Closure $pdo;

    /** @var array<string, PDO|null> */
    private array $conexiones = [];

    /**
     * @param (Closure(string, int): ?PDO)|null $pdo conexión de escritura para (esquema, sfsv)
     */
    public function __construct(?Closure $pdo = null)
    {
        $this->pdo = $pdo ?? static function (string $esquema, int $sfsv): ?PDO {
            $base = match ($sfsv) {
                1 => 'sv-e',
                2 => 'sf-e',
                default => null,
            };
            if ($base === null) {
                return null;
            }

            return (new DBConnection((new ConfigDB($base))->getEsquema($esquema)))->getPDO();
        };
    }

    public function estaBloqueado(string $esquema, int $sfsv, int $idUsuario): bool
    {
        $v = $this->ejecutar(
            $esquema,
            $sfsv,
            'SELECT (login_bloqueo_hasta IS NOT NULL AND login_bloqueo_hasta > now()) FROM aux_usuarios WHERE id_usuario = :id',
            [':id' => $idUsuario],
            true
        );

        return $v === true;
    }

    public function anotarFallo(string $esquema, int $sfsv, int $idUsuario, int $maxFallos, int $minutosBloqueo): void
    {
        $this->ejecutar(
            $esquema,
            $sfsv,
            'UPDATE aux_usuarios SET
                login_bloqueo_hasta = CASE WHEN login_fallos + 1 >= CAST(:max1 AS int)
                    THEN now() + make_interval(mins => CAST(:minutos AS int)) ELSE login_bloqueo_hasta END,
                login_fallos = CASE WHEN login_fallos + 1 >= CAST(:max2 AS int) THEN 0 ELSE login_fallos + 1 END
             WHERE id_usuario = :id',
            [':max1' => $maxFallos, ':max2' => $maxFallos, ':minutos' => $minutosBloqueo, ':id' => $idUsuario]
        );
    }

    public function anotarAcierto(string $esquema, int $sfsv, int $idUsuario): void
    {
        $this->ejecutar(
            $esquema,
            $sfsv,
            'UPDATE aux_usuarios SET login_fallos = 0, login_bloqueo_hasta = NULL
             WHERE id_usuario = :id AND (login_fallos <> 0 OR login_bloqueo_hasta IS NOT NULL)',
            [':id' => $idUsuario]
        );
    }

    public function aceptarPasoTotp(string $esquema, int $sfsv, int $idUsuario, int $paso): bool
    {
        // Un solo UPDATE condicionado: dos peticiones simultáneas con el mismo código no pueden aceptarlo las dos.
        $filas = $this->ejecutar(
            $esquema,
            $sfsv,
            'UPDATE aux_usuarios SET totp_ultimo_paso = CAST(:paso1 AS bigint)
             WHERE id_usuario = :id AND (totp_ultimo_paso IS NULL OR totp_ultimo_paso < CAST(:paso2 AS bigint))',
            [':paso1' => $paso, ':paso2' => $paso, ':id' => $idUsuario]
        );

        // null = no se pudo comprobar: se acepta como antes de existir la columna.
        return $filas === null || $filas === 1;
    }

    /**
     * @param array<string, int> $params
     * @return int|bool|null filas afectadas (UPDATE), el valor leído (SELECT) o null si falla
     */
    private function ejecutar(string $esquema, int $sfsv, string $sql, array $params, bool $leer = false): int|bool|null
    {
        try {
            $pdo = $this->conexiones[$sfsv . '|' . $esquema] ??= ($this->pdo)($esquema, $sfsv);
            if ($pdo === null) {
                return null;
            }
            $st = $pdo->prepare($sql);
            $st->execute($params);
            if ($leer) {
                $v = $st->fetchColumn();

                if ($v === false) {
                    return null;
                }

                return in_array($v, [true, 't', 1, '1'], true);
            }

            return $st->rowCount();
        } catch (\Throwable $e) {
            error_log(sprintf('PgLoginIntentosRepository (%s): %s', $esquema, $e->getMessage()));

            return null;
        }
    }
}
