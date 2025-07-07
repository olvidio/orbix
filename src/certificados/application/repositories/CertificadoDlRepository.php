<?php

namespace src\certificados\application\repositories;

use PDO;
use src\certificados\domain\contracts\CertificadoDlRepositoryInterface;
use src\certificados\domain\entity\CertificadoDl;
use src\certificados\infrastructure\repositories\PgCertificadoDlRepository;


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
class CertificadoDlRepository implements CertificadoDlRepositoryInterface
{

    private PgCertificadoDlRepository $repository;

    public function __construct()
    {
        $this->repository = new PgCertificadoDlRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Certificado
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Certificado
	
	 */
	public function getCertificados(array $aWhere=[], array $aOperators=[]): bool|array
    {
	    return $this->repository->getCertificados($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(CertificadoDl $Certificado): bool
    {
        return $this->repository->Eliminar($Certificado);
    }

	public function Guardar(CertificadoDl $Certificado): bool
    {
        return $this->repository->Guardar($Certificado);
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
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): bool|array
    {
        return $this->repository->datosById($id_item);
    }
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item): CertificadoDl
    {
        return $this->repository->findById($id_item);
    }
	
    public function getNewId_item()
    {
        return $this->repository->getNewId_item();
    }
}