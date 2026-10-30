<?php

namespace src\pasarela\domain\contracts;

use src\pasarela\domain\entity\PasarelaConfig;
use src\shared\domain\criteria\Criteria;

interface PasarelaConfigRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Configuraciones de pasarela que cumplen el criterio.
     *
     * @return list<PasarelaConfig>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PasarelaConfig $PasarelaConfig): bool;

    public function Guardar(PasarelaConfig $PasarelaConfig): bool;

    public function getErrorTxt(): string;


    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $nom_parametro
     * @return array<string, mixed>|false
     */
    public function datosById(string $nom_parametro): array|false;

    /**
     * Busca la clase con parametro en el repositorio.
     */
    public function findById(string $nom_parametro): ?PasarelaConfig;
}