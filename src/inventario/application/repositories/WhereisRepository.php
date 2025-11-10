<?php

namespace src\inventario\application\repositories;

use PDO;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Whereis;
use src\inventario\domain\value_objects\WhereisItemId;
use src\inventario\infrastructure\repositories\PgWhereisRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Whereis
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class WhereisRepository implements WhereisRepositoryInterface
{

    /**$
     * @var WhereisRepositoryInterface
     */
    private WhereisRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgWhereisRepository();
    }

    public function getArrayIdFromIdEgms(array $aEgms): array
    {
        return $this->repository->getArrayIdFromIdEgms($aEgms);
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Whereis
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Whereis
     */
    public function getWhereare(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getWhereare($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Whereis $Whereis): bool
    {
        return $this->repository->Eliminar($Whereis);
    }

    public function Guardar(Whereis $Whereis): bool
    {
        return $this->repository->Guardar($Whereis);
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
     * @param WhereisItemId $id_item_whereis
     * @return array|bool
     */
    public function datosById(WhereisItemId $id_item_whereis): array|bool
    {
        return $this->repository->datosById($id_item_whereis);
    }

    /**
     * Busca la clase con id_item_whereis en el repositorio.
     */
    public function findById(WhereisItemId $id_item_whereis): ?Whereis
    {
        return $this->repository->findById($id_item_whereis);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}