<?php

namespace src\ubis\domain\contracts;

/**
 * Interfaz para gestionar la relación Casa-Dirección (tabla u_cross_dir_cdc)
 */
interface RelacionUbiDireccionRepositoryInterface
{
    public function getRelacionesPorUbi(int $id_ubi): array;
    /** Asocia una dirección a una casa */
    public function asociarDireccion(int $id_ubi, int $id_direccion, ?bool $principal = null): bool;

    /** Desasocia una dirección de una casa */
    public function desasociarDireccion(int $id_ubi, int $id_direccion): bool;

    /** Obtiene todas las direcciones asociadas a una casa */
    public function getDireccionesPorUbi(int $id_ubi): array;

    /** Obtiene todas las casas asociadas a una dirección */
    public function getUbisPorDireccion(int $id_direccion): array;

    /** Verifica si existe la relación casa-dirección */
    public function existeRelacion(int $id_ubi, int $id_direccion): bool;

    /** Obtiene la dirección principal (si existe) de una casa */
    public function getDireccionPrincipal(int $id_ubi): ?int;

    /** Establece una dirección como principal para una casa */
    public function establecerDireccionPrincipal(int $id_ubi, int $id_direccion): bool;
    /** * Actualiza solo el campo propietario */
    public function updatePropietario(int $id_ubi, int $id_direccion, bool $esPropietario): bool;

}
