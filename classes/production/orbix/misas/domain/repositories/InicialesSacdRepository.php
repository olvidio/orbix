<?php

namespace misas\domain\repositories;

use misas\domain\entity\InicialesSacd;
use misas\infrastructure\PgInicialesSacdRepository;
use PDO;


/**
 *
 * Clase para gestionar la lista de objetos tipo Plantilla
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
class InicialesSacdRepository implements InicialesSacdRepositoryInterface
{

    private InicialesSacdRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgInicialesSacdRepository();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Plantilla
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Plantilla
     */
    public function getInicialesSacd(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getInicialesSacd($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(InicialesSacd $InicialesSacd): bool
    {
        return $this->repository->Eliminar($InicialesSacd);
    }

    public function Guardar(InicialesSacd $InicialesSacd): bool
    {
        return $this->repository->Guardar($InicialesSacd);
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
     */
    public function datosById(int $id_nom): array|bool
    {
        return $this->repository->datosById($id_nom);
    }

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_nom): ?InicialesSacd
    {
        return $this->repository->findById($id_nom);
    }

}