<?php

namespace src\asistentes\application\services;

use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\shared\domain\contracts\AggregateRoot;
use src\shared\domain\contracts\UnitOfWorkInterface;

/**
 * Servicio de aplicación para gestionar Asistentes
 *
 * Encapsula la lógica de negocio y coordina el repositorio con el Unit of Work
 * para garantizar que los eventos de dominio se despachan correctamente.
 *
 * @package orbix
 * @subpackage asistentes\application
 */
class AsistenteApplicationService
{
    public function __construct(
        private AsistenteRepositoryInterface $repository,
        private UnitOfWorkInterface $unitOfWork
    ) {
    }

    /**
     * Busca un asistente por su ID
     *
     * @param int $id_activ
     * @param int $id_nom
     * @return Asistente|null
     */
    public function findById(int $id_activ, int $id_nom): ?Asistente
    {
        return $this->repository->findById($id_activ, $id_nom);
    }

    /**
     * Obtiene una lista de asistentes según criterios
     *
     * @param array $aWhere
     * @param array $aOperators
     * @return array|false
     */
    public function getAsistentes(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getAsistentes($aWhere, $aOperators);
    }

    /**
     * Guarda un asistente dentro de una transacción
     *
     * Los eventos de dominio se despachan automáticamente si la operación tiene éxito
     *
     * @param Asistente $asistente
     * @return bool
     */
    public function guardar(Asistente $asistente): bool
    {
        return $this->unitOfWork->execute(function ($uow) use ($asistente) {
            $success = $this->repository->Guardar($asistente);

            if ($success && $asistente instanceof AggregateRoot) {
                $uow->registerEntity($asistente);
            }

            return $success;
        });
    }

    /**
     * Elimina un asistente dentro de una transacción
     *
     * Los eventos de dominio se despachan automáticamente si la operación tiene éxito
     *
     * @param Asistente $asistente
     * @return bool
     */
    public function eliminar(Asistente $asistente): bool
    {
        return $this->unitOfWork->execute(function ($uow) use ($asistente) {
            $success = $this->repository->Eliminar($asistente);

            if ($success && $asistente instanceof AggregateRoot) {
                $uow->registerEntity($asistente);
            }

            return $success;
        });
    }

    /**
     * Elimina un asistente por su ID
     *
     * @param int $id_activ
     * @param int $id_nom
     * @return bool
     */
    public function eliminarById(int $id_activ, int $id_nom): bool
    {
        $asistente = $this->findById($id_activ, $id_nom);

        if (!$asistente) {
            return false;
        }

        return $this->eliminar($asistente);
    }
}
