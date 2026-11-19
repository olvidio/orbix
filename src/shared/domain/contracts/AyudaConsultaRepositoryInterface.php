<?php

declare(strict_types=1);

namespace src\shared\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Registro de las preguntas hechas a la ayuda.
 */
interface AyudaConsultaRepositoryInterface
{
    /**
     * Consultas que cumplen el criterio.
     *
     * @return list<AyudaConsulta>
     */
    public function matching(Criteria $criteria): array;

    public function findById(string $uuid_item): ?AyudaConsulta;

    public function Guardar(AyudaConsulta $ayudaConsulta): bool;

    public function Eliminar(AyudaConsulta $ayudaConsulta): bool;

    /**
     * Borra las consultas anteriores a la fecha, salvo las que alguien marcó como «no me ha
     * servido» y siguen pendientes de revisar. Devuelve cuántas ha borrado.
     */
    public function purgarAnterioresA(DateTimeLocal $fecha): int;
}
