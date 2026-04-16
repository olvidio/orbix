<?php

namespace src\ubis\domain\contracts;

interface TrasladoUbiRepositoryInterface
{
    public function trasladoCdc(int $id_ubi, string $esquema_org, string $esquema_dst): bool;

    public function trasladoCtr(int $id_ubi, string $esquema_org, string $esquema_dst): bool;
}
