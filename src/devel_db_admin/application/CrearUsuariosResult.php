<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

final class CrearUsuariosResult
{
    public function __construct(
        public readonly string $esquema,
        public readonly string $esquemaPwd,
        public readonly string $esquemav,
        public readonly string $esquemavPwd,
        public readonly string $esquemaf,
        public readonly string $esquemafPwd,
    ) {
    }
}
