<?php

declare(strict_types=1);

namespace src\usuarios\domain\contracts;

/**
 * Estado de los intentos de login de un usuario (bloqueo por fallos y último código 2FA aceptado).
 *
 * `$esquema` es el esquema del usuario (`H-dlbv`, `H-dlbf`…) y `$sfsv` la sección de su BD
 * (1 = sv, 2 = sf), tal como los resuelve el login.
 */
interface LoginIntentosRepositoryInterface
{
    public function estaBloqueado(string $esquema, int $sfsv, int $idUsuario): bool;

    /** Suma un fallo; al llegar a `$maxFallos`, bloquea `$minutosBloqueo` minutos y vuelve a contar desde cero. */
    public function anotarFallo(string $esquema, int $sfsv, int $idUsuario, int $maxFallos, int $minutosBloqueo): void;

    /** Login correcto: contador a cero y sin bloqueo. */
    public function anotarAcierto(string $esquema, int $sfsv, int $idUsuario): void;

    /**
     * Acepta el paso de tiempo de un código 2FA válido si es posterior al último aceptado, y lo guarda.
     * False si ese código (o uno posterior) ya se usó.
     */
    public function aceptarPasoTotp(string $esquema, int $sfsv, int $idUsuario, int $paso): bool;
}
