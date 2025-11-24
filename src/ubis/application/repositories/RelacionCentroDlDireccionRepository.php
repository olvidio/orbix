<?php

namespace src\ubis\application\repositories;

use src\ubis\domain\contracts\RelacionUbiDireccionRepositoryInterface;
use src\ubis\infrastructure\repositories\PgCentroDlDireccionRepository;

/**
 * Repositorio de aplicación que delega en infraestructura (PgCasaDireccionRepository)
 * para gestionar la relación Casa-Dirección.
 */
class RelacionCentroDlDireccionRepository implements RelacionUbiDireccionRepositoryInterface
{
    private RelacionUbiDireccionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgCentroDlDireccionRepository();
    }


    public function getRelacionesPorUbi(int $id_ubi): array
    {
        return $this->repository->getRelacionesPorUbi($id_ubi);
    }

    public function asociarDireccion(int $id_ubi, int $id_direccion, ?bool $principal = null): bool
    {
        return $this->repository->asociarDireccion($id_ubi, $id_direccion, $principal);
    }

    public function desasociarDireccion(int $id_ubi, int $id_direccion): bool
    {
        return $this->repository->desasociarDireccion($id_ubi, $id_direccion);
    }

    public function getDireccionesPorUbi(int $id_ubi): array
    {
        return $this->repository->getDireccionesPorUbi($id_ubi);
    }

    public function getUbisPorDireccion(int $id_direccion): array
    {
        return $this->repository->getUbisPorDireccion($id_direccion);
    }

    public function existeRelacion(int $id_ubi, int $id_direccion): bool
    {
        return $this->repository->existeRelacion($id_ubi, $id_direccion);
    }

    public function getDireccionPrincipal(int $id_ubi): ?int
    {
        return $this->repository->getDireccionPrincipal($id_ubi);
    }

    public function establecerDireccionPrincipal(int $id_ubi, int $id_direccion): bool
    {
        return $this->repository->establecerDireccionPrincipal($id_ubi, $id_direccion);
    }

    public function updatePropietario(int $id_ubi, int $id_direccion, bool $esPropietario): bool
    {
        return $this->repository->updatePropietario($id_ubi, $id_direccion, $esPropietario);
    }
}
