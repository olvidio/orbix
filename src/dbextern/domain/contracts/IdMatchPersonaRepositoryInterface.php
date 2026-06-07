<?php

namespace src\dbextern\domain\contracts;

use src\dbextern\domain\entity\IdMatchPersona;

interface IdMatchPersonaRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<IdMatchPersona>
     */
    public function getIdMatchPersonas(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(IdMatchPersona $IdMatchPersona): bool;

    public function Guardar(IdMatchPersona $IdMatchPersona): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_listas): array|false;

    public function findById(int $id_listas): ?IdMatchPersona;
}
