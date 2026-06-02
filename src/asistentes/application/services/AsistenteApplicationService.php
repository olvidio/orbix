<?php

namespace src\asistentes\application\services;

use Psr\Container\ContainerInterface;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
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
        private UnitOfWorkInterface $unitOfWork,
        private ContainerInterface $container,
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
     * @return array|bool
     */
    public function getAsistentes(array $aWhere = [], array $aOperators = []): array|bool
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
        // Mismo criterio que en eliminar: si el asistente ya existe en alguna
        // tabla hija (d_asistentes_dl/out/ex/de_paso) hay que actualizarlo ahi,
        // o de lo contrario el UPDATE no afecta a ninguna fila y se acaba
        // insertando un duplicado en d_asistentes_dl. Si es nuevo se resuelve la
        // tabla destino segun la dl de la persona y la de la actividad.
        $this->repository = $this->resolverRepositorioDeAsistente($asistente)
            ?? $this->resolverRepositorioDestino($asistente);
        return $this->unitOfWork->execute(function ($uow) use ($asistente) {
            $success = $this->repository->Guardar($asistente);

            if ($success && $asistente instanceof AggregateRoot) {
                $uow->registerEntity($asistente);
            }

            return $success;
        });
    }

    /**
     * Determina la tabla destino de un asistente nuevo (que aun no existe en
     * ninguna tabla hija) delegando en el resolver de dominio, que decide segun
     * la dl de la persona y la de la actividad (y enruta los "de paso" con ids
     * negativos a `d_asistentes_ex`).
     */
    private function resolverRepositorioDestino(Asistente $asistente): AsistenteRepositoryInterface
    {
        /** @var AsistenteActividadService $actividadService */
        $actividadService = $this->container->get(AsistenteActividadService::class);
        $claseRepo = $actividadService->getRepoAsistente($asistente->getId_nom(), $asistente->getId_activ());

        /** @var AsistenteRepositoryInterface $repository */
        return $this->container->get($claseRepo);
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
        // Un asistente puede vivir en distintas tablas hijas de d_asistentes_all
        // (d_asistentes_dl, d_asistentes_out, d_asistentes_ex, d_asistentes_de_paso).
        // Hay que eliminarlo de la tabla donde realmente esta; si se usa siempre la
        // tabla de la dl, el DELETE no afecta ninguna fila y la operacion "tiene
        // exito" sin borrar nada.
        $repository = $this->resolverRepositorioDeAsistente($asistente);
        if ($repository === null) {
            return false;
        }
        $this->repository = $repository;
        return $this->unitOfWork->execute(function ($uow) use ($asistente) {
            $success = $this->repository->Eliminar($asistente);

            if ($success && $asistente instanceof AggregateRoot) {
                $uow->registerEntity($asistente);
            }

            return $success;
        });
    }

    /**
     * Localiza el repositorio (tabla hija) que contiene realmente al asistente.
     *
     * @return AsistenteRepositoryInterface|null null si no se encuentra en ninguna tabla.
     */
    private function resolverRepositorioDeAsistente(Asistente $asistente): ?AsistenteRepositoryInterface
    {
        $id_activ = $asistente->getId_activ();
        $id_nom = $asistente->getId_nom();

        $candidatas = [
            AsistenteDlRepositoryInterface::class,
            AsistenteOutRepositoryInterface::class,
            AsistenteExRepositoryInterface::class,
            AsistentePubRepositoryInterface::class,
        ];

        foreach ($candidatas as $clase) {
            /** @var AsistenteRepositoryInterface $repo */
            $repo = $this->container->get($clase);
            if ($repo->findById($id_activ, $id_nom) !== null) {
                return $repo;
            }
        }

        return null;
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
