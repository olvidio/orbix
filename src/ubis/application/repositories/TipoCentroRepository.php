<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\entity\TipoCentro;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\infrastructure\repositories\PgTipoCentroRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo TipoCentro
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class TipoCentroRepository implements TipoCentroRepositoryInterface
{

    /**$
     * @var TipoCentroRepositoryInterface
     */
    private TipoCentroRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgTipoCentroRepository();
    }

    public function getArrayTiposCentro(): array
    {
        return $this->repository->getArrayTiposCentro();
    }
/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo TipoCentro
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo TipoCentro
	
	 */
	public function getTiposCentro(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getTiposCentro($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoCentro $TipoCentro): bool
    {
        return $this->repository->Eliminar($TipoCentro);
    }

	public function Guardar(TipoCentro $TipoCentro): bool
    {
        return $this->repository->Guardar($TipoCentro);
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
     * @param string $tipo_ctr
     * @return array|bool
	
     */
    public function datosById(string $tipo_ctr): array|bool
    {
        return $this->repository->datosById($tipo_ctr);
    }
	
    /**
     * Busca la clase con tipo_ctr en el repositorio.
	
     */
    public function findById(string $tipo_ctr): ?TipoCentro
    {
        return $this->repository->findById($tipo_ctr);
    }
}