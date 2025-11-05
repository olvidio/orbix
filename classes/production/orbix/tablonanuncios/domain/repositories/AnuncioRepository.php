<?php

namespace tablonanuncios\domain\repositories;

use PDO;
use tablonanuncios\domain\AnuncioId;
use tablonanuncios\domain\entity\Anuncio;
use tablonanuncios\infrastructure\PgAnuncioRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Anuncio
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
class AnuncioRepository implements AnuncioRepositoryInterface
{

    private AnuncioRepositoryInterface|PgAnuncioRepository $repository;

    public function __construct()
    {
        $this->repository = new PgAnuncioRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Anuncio
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Anuncio
	
	 */
	public function getAnuncios(array $aWhere=[], array $aOperators=[]): false|array
    {
	    return $this->repository->getAnuncios($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Anuncio $Anuncio): bool
    {
        return $this->repository->Eliminar($Anuncio);
    }

	public function Guardar(Anuncio $Anuncio): bool
    {
        return $this->repository->Guardar($Anuncio);
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
     * @param AnuncioId $uuid_item
     * @return array|bool
	
     */
    public function datosById(AnuncioId $uuid_item): bool|array
    {
        return $this->repository->datosById($uuid_item);
    }
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(AnuncioId $uuid_item): ?Anuncio
    {
        return $this->repository->findById($uuid_item);
    }
	
}