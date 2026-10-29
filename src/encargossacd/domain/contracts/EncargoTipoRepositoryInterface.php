<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\EncargoTipo;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase EncargoTipo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoTipoRepositoryInterface
{

    public function id_tipo_encargo(int|string $grupo, string $nom_tipo): string;

    /**
     * @return array<string, mixed>
     */
    public function encargo_de_tipo(int|string $id_tipo_enc): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * EncargoTipo que cumplen el criterio.
     *
     * @return list<EncargoTipo>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<EncargoTipo>
     */
    public function getEncargoTipos(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoTipo $EncargoTipo): bool;

    public function Guardar(EncargoTipo $EncargoTipo): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_tipo_enc
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_enc): array|false;

    /**
     * Busca la clase con id_tipo_enc en el repositorio.
     */
    public function findById(int $id_tipo_enc): ?EncargoTipo;
}