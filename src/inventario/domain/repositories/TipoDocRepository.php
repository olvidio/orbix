<?php

namespace src\inventario\domain\repositories;

use src\inventario\infrastructure\PgTipoDocRepository;
use PDO;
use src\inventario\domain\entity\TipoDoc;


/**
 *
 * Clase para gestionar la lista de objetos tipo TipoDoc
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class TipoDocRepository implements TipoDocRepositoryInterface
{

    /**$
     * @var TipoDocRepositoryInterface
     */
    private TipoDocRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgTipoDocRepository();
    }

    public function getArrayTipoDoc():array
    {
        return $this->repository->getArrayTipoDoc();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo TipoDoc
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo TipoDoc
	
	 */
	public function getTipoDocs(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getTipoDocs($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TipoDoc $TipoDoc): bool
    {
        return $this->repository->Eliminar($TipoDoc);
    }

	public function Guardar(TipoDoc $TipoDoc): bool
    {
        return $this->repository->Guardar($TipoDoc);
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
     * @param int $id_tipo_doc
     * @return array|bool
	
     */
    public function datosById(int $id_tipo_doc): array|bool
    {
        return $this->repository->datosById($id_tipo_doc);
    }
	
    /**
     * Busca la clase con id_tipo_doc en el repositorio.
	
     */
    public function findById(int $id_tipo_doc): ?TipoDoc
    {
        return $this->repository->findById($id_tipo_doc);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}