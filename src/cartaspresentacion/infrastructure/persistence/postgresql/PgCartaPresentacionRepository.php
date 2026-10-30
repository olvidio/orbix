<?php

namespace src\cartaspresentacion\infrastructure\persistence\postgresql;

use PDO;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\cartaspresentacion\domain\value_objects\PresentacionPk;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\cartaspresentacion\domain\criteria\CartaPresentacionField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla du_presentacion a la interfaz del repositorio
 */
class PgCartaPresentacionRepository extends ClaseRepository implements CartaPresentacionRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBP'));
        $this->setoDbl_Select(GlobalPdo::get('oDBP'));
        $this->setNomTabla('du_presentacion');
    }

    /**
     * Cartas de presentación que cumplen el criterio.
     *
     * @return list<CartaPresentacion>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(CartaPresentacionField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = CartaPresentacion::fromArray($aDatos);
        }

        return $result;
    }


    public function Eliminar(CartaPresentacion $CartaPresentacion): bool
    {
        $id_ubi = $CartaPresentacion->getId_ubi();
        $id_direccion = $CartaPresentacion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CartaPresentacion $CartaPresentacion): bool
    {
        $id_ubi = $CartaPresentacion->getId_ubi();
        $id_direccion = $CartaPresentacion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_ubi, $id_direccion);

        $aDatos = $CartaPresentacion->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_ubi']);
            unset($aDatos['id_direccion']);
            $update = "
					pres_nom                 = :pres_nom,
					pres_telf                = :pres_telf,
					pres_mail                = :pres_mail,
					zona                     = :zona,
					observ                   = :observ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_direccion,id_ubi,pres_nom,pres_telf,pres_mail,zona,observ)';
            $valores = '(:id_direccion,:id_ubi,:pres_nom,:pres_telf,:pres_mail,:zona,:observ)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_ubi, int $id_direccion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return true;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_ubi, int $id_direccion): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string)$key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(PresentacionPk $pk): array|false
    {
        return $this->datosById($pk->idUbi(), $pk->idDireccion());
    }

    public function findById(int $id_ubi, int $id_direccion): ?CartaPresentacion
    {
        $aDatos = $this->datosById($id_ubi, $id_direccion);
        if ($aDatos === false) {
            return null;
        }
        return CartaPresentacion::fromArray($aDatos);
    }

    public function findByPk(PresentacionPk $pk): ?CartaPresentacion
    {
        return $this->findById($pk->idUbi(), $pk->idDireccion());
    }
}
