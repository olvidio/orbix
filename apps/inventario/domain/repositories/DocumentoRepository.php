<?php

namespace inventario\domain\repositories;

use PDO;
use web\Desplegable;
use inventario\domain\entity\Documento;
use inventario\infrastructure\PgDocumentoRepository;


use web\DateTimeLocal;
use web\NullDateTimeLocal;
use core\ConverterDate;
use function core\is_true;
/**
 *
 * Clase para gestionar la lista de objetos tipo Documento
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class DocumentoRepository implements DocumentoRepositoryInterface
{

    /**$
     * @var DocumentoRepositoryInterface
     */
    private DocumentoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgDocumentoRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Documento
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Documento
	
	 */
	public function getDocumentos(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getDocumentos($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Documento $Documento): bool
    {
        return $this->repository->Eliminar($Documento);
    }

	public function Guardar(Documento $Documento): bool
    {
        return $this->repository->Guardar($Documento);
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
     * @param int $id_doc
     * @return array|bool
	
     */
    public function datosById(int $id_doc): array|bool
    {
        return $this->repository->datosById($id_doc);
    }
	
    /**
     * Busca la clase con id_doc en el repositorio.
	
     */
    public function findById(int $id_doc): ?Documento
    {
        return $this->repository->findById($id_doc);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}