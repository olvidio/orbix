<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\infrastructure\repositories\PgCentroEllasRepository;


use function core\is_true;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use core\ConverterDate;
/**
 *
 * Clase para gestionar la lista de objetos tipo CentroEllas
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class CentroEllasRepository implements CentroEllasRepositoryInterface
{

    /**$
     * @var CentroEllasRepositoryInterface
     */
    private CentroEllasRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgCentroEllasRepository();
    }

    public function getArrayCentros($sCondicion = ''): array
    {
        return $this->repository->getArrayCentros($sCondicion);
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo CentroEllas
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo CentroEllas
	
	 */
	public function getCentros(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getCentros($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(CentroEllas $CentroEllas): bool
    {
        return $this->repository->Eliminar($CentroEllas);
    }

	public function Guardar(CentroEllas $CentroEllas): bool
    {
        return $this->repository->Guardar($CentroEllas);
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
     * @param int $id_ubi
     * @return array|bool
	
     */
    public function datosById(int $id_ubi): array|bool
    {
        return $this->repository->datosById($id_ubi);
    }
	
    /**
     * Busca la clase con id_ubi en el repositorio.
	
     */
    public function findById(int $id_ubi): ?CentroEllas
    {
        return $this->repository->findById($id_ubi);
    }
}