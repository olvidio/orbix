<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\DescTeleco;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase DescTeleco y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface DescTelecoRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayDescTelecoPersonas(string $sdepende): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * DescTeleco que cumplen el criterio.
     *
     * @return list<DescTeleco>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<DescTeleco>
     */
    public function getDescsTeleco(array $aWhere=[], array $aOperators=[]): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayDescTelecoUbis(string $sdepende): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(DescTeleco $DescTeleco): bool;

	public function Guardar(DescTeleco $DescTeleco): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_item
     * @return array<string, mixed>|false
	
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item): ?DescTeleco;

    public function getNewId(): int;
}