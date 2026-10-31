<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\TipoCentro;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase TipoCentro y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
interface TipoCentroRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposCentro(): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * TipoCentro que cumplen el criterio.
     *
     * @return list<TipoCentro>
     */
    public function matching(Criteria $criteria): array;

	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoCentro $TipoCentro): bool;

	public function Guardar(TipoCentro $TipoCentro): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $tipo_ctr
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
    public function datosById(string $tipo_ctr): array|false;
	
    /**
     * Busca la clase con tipo_ctr en el repositorio.
	
     */
    public function findById(string $tipo_ctr): ?TipoCentro;
}