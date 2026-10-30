<?php

namespace src\inventario\domain\contracts;

use src\inventario\domain\entity\Documento;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Documento y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface DocumentoRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Documento que cumplen el criterio.
     *
     * @return list<Documento>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Documento $Documento): bool;

    public function Guardar(Documento $Documento): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_doc
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_doc): array|false;

    /**
     * Busca la clase con id_doc en el repositorio.
     */
    public function findById(int $id_doc): ?Documento;

    public function getNewId(): int;
}