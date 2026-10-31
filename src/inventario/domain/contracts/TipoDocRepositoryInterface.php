<?php

namespace src\inventario\domain\contracts;

use src\inventario\domain\entity\TipoDoc;
use src\shared\domain\criteria\Criteria;
use src\inventario\domain\value_objects\TipoDocId;

/**
 * Interfaz de la clase TipoDoc y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface TipoDocRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayTipoDoc(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * TipoDoc que cumplen el criterio.
     *
     * @return list<TipoDoc>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDoc $TipoDoc): bool;

    public function Guardar(TipoDoc $TipoDoc): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_tipo_doc
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_doc): array|false;

    /**
     * Busca la clase con id_tipo_doc en el repositorio.
     */
    public function findById(int $id_tipo_doc): ?TipoDoc;

    public function getNewId(): int;
}