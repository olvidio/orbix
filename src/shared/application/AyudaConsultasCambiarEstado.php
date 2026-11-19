<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\value_objects\AyudaConsultaEstado;

/**
 * Marca como resueltas, descartadas o de nuevo pendientes las consultas a la ayuda que la
 * pantalla de revisión agrupa en una fila (la misma pregunta hecha varias veces).
 */
final class AyudaConsultasCambiarEstado
{
    public function __construct(
        private readonly AyudaConsultaRepositoryInterface $repository,
    ) {
    }

    /**
     * @param list<string> $uuids
     * @return string `''` si se ha guardado; si no, el texto del error.
     */
    public function execute(array $uuids, int $estado): string
    {
        if (!array_key_exists($estado, AyudaConsultaEstado::getArrayEstado())) {
            return _("El estado indicado no existe.");
        }

        $cambiadas = 0;
        foreach ($uuids as $uuid) {
            // Una consulta que ya no está (la purga la borró) no impide cambiar las demás.
            $consulta = $this->repository->findById($uuid);
            if ($consulta === null) {
                continue;
            }
            $consulta->setEstado($estado);
            if ($this->repository->Guardar($consulta) === false) {
                return _("No se ha podido guardar el estado de la consulta.");
            }
            $cambiadas++;
        }

        return $cambiadas === 0 ? _("No se encuentra ninguna de las consultas indicadas.") : '';
    }
}
