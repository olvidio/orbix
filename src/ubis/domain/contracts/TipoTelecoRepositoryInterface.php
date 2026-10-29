<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\TipoTeleco;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase TipoTeleco y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface TipoTelecoRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTelecoPersona(): array;
    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTelecoUbi(): array;
    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTeleco(): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * TipoTeleco que cumplen el criterio.
     *
     * @return list<TipoTeleco>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<TipoTeleco>
     */
    public function getTiposTeleco(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoTeleco $TipoTeleco): bool;

	public function Guardar(TipoTeleco $TipoTeleco): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id
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
    public function datosById(int $id): array|false;
	
    /**
     * Busca la clase con id en el repositorio.
	
     */
    public function findById(int $id): ?TipoTeleco;
    public function getNewId(): int;
}