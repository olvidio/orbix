<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\TipoCasa;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase TipoCasa y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
interface TipoCasaRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposCasa(): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * TipoCasa que cumplen el criterio.
     *
     * @return list<TipoCasa>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<TipoCasa>
     */
    public function getTiposCasa(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoCasa $TipoCasa): bool;

	public function Guardar(TipoCasa $TipoCasa): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $tipo_casa
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
    public function datosById(string $tipo_casa): array|false;
	
    /**
     * Busca la clase con tipo_casa en el repositorio.
	
     */
    public function findById(string $tipo_casa): ?TipoCasa;
}