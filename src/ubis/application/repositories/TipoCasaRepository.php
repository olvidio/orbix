<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\entity\TipoCasa;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\infrastructure\repositories\PgTipoCasaRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo TipoCasa
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class TipoCasaRepository implements TipoCasaRepositoryInterface
{

    /**$
     * @var TipoCasaRepositoryInterface
     */
    private TipoCasaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgTipoCasaRepository();
    }

    public function getArrayTiposCasa(): array
    {
        return $this->repository->getArrayTiposCasa();
    }
/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo TipoCasa
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo TipoCasa
	
	 */
	public function getTiposCasa(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getTiposCasa($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoCasa $TipoCasa): bool
    {
        return $this->repository->Eliminar($TipoCasa);
    }

	public function Guardar(TipoCasa $TipoCasa): bool
    {
        return $this->repository->Guardar($TipoCasa);
    }

	public function getErrorTxt(): string
    {
        return $this->repository->getErrorTxt();
    }

	public function getoDbl(): PDO
    {
        return $this->repository->getoDbl();
    }

	public function setoDbl(PDO $oDbl): void
    {
        $this->repository->setoDbl($oDbl);
    }

	public function getNomTabla(): string
    {
        return $this->repository->getNomTabla();
    }
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $tipo_casa
     * @return array|bool
	
     */
    public function datosById(string $tipo_casa): array|bool
    {
        return $this->repository->datosById($tipo_casa);
    }
	
    /**
     * Busca la clase con tipo_casa en el repositorio.
	
     */
    public function findById(string $tipo_casa): ?TipoCasa
    {
        return $this->repository->findById($tipo_casa);
    }
}