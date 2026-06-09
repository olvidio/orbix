<?php

namespace src\cartaspresentacion\infrastructure\persistence\postgresql;

use PDO;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\cartaspresentacion\domain\value_objects\PresentacionPk;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla du_presentacion a la interfaz del repositorio
 */
class PgCartaPresentacionRepository extends ClaseRepository implements CartaPresentacionRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBP'));
        $this->setNomTabla('du_presentacion');
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CartaPresentacion>
     */
    public function getCartasPresentacion(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CartaPresentacionSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre' || $camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = ' WHERE ' . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string)$limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $CartaPresentacionSet->add(CartaPresentacion::fromArray($normalized));
        }
        /** @var list<CartaPresentacion> $result */
        $result = array_values($CartaPresentacionSet->getTot());

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
