<?php

namespace src\ubis\domain\contracts;

interface TrasladoUbiRepositoryInterface
{
    public function trasladoCdc(int $id_ubi, string $esquema_org, string $esquema_dst): bool;

    public function trasladoCtr(int $id_ubi, string $esquema_org, string $esquema_dst): bool;

    public function existeCdcDl(string $esquema): bool;

    /**
     * Mueve una casa de resto.u_cdc_ex (y direcciones/telecos asociadas) a {esquema}.u_cdc_dl.
     * Conserva id_ubi. El origen debe estar ya actualizado (dl/región/campos).
     */
    public function trasladoCdcDesdeResto(int $id_ubi, string $esquema_dst): bool;
}
