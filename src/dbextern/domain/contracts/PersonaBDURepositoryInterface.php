<?php

namespace src\dbextern\domain\contracts;

use src\dbextern\domain\entity\PersonaBDU;

interface PersonaBDURepositoryInterface
{
    /**
     * @return list<PersonaBDU>
     */
    public function getPersonaBDUQuery(string $sQuery = ''): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PersonaBDU>
     */
    public function getIdMatchPersonas(array $aWhere = [], array $aOperators = []): array;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_listas): array|false;

    public function findById(int $id_listas): ?PersonaBDU;
}
