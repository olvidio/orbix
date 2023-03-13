<?php

namespace certificados\domain\repositories;

use certificados\infrastructure\PgCertificadoPublicRepository;
use PDO;
use certificados\domain\entity\Certificado;
/**
 *
 * Clase para gestionar la lista de objetos tipo Certificado
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
class CertificadoPublicRepository implements CertificadoRepositoryInterface
{

    /**
     * @var CertificadoRepositoryInterface
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new PgCertificadoPublicRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Certificado
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Certificado
	
	 */
	public function getCertificados(array $aWhere=[], array $aOperators=[])
	{
	    return $this->repository->getCertificados($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Certificado $Certificado)
    {
        return $this->repository->Eliminar($Certificado);
    }

	public function Guardar(Certificado $Certificado)
    {
        return $this->repository->Guardar($Certificado);
    }

	public function getErrorTxt()
    {
        return $this->repository->getErrorTxt();
    }

	public function getoDbl(): PDO
    {
        return $this->repository->getoDbl();
    }

	public function setoDbl(PDO $oDbl)
    {
        $this->repository->setoDbl($oDbl);
    }

	public function getNomTabla()
    {
        return $this->repository->getNomTabla();
    }
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item)
    {
        return $this->repository->datosById($id_item);
    }
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item)
    {
        return $this->repository->findById($id_item);
    }
	
    public function getNewId_item()
    {
        return $this->repository->getNewId_item();
    }
}