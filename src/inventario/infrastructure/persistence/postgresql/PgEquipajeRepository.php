<?php

namespace src\inventario\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla i_equipajes_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgEquipajeRepository extends ClaseRepository implements EquipajeRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_equipajes_dl');
    }

    public function getEquipajesCoincidentes(string $f_ini_iso, string $f_fin_iso): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT id_equipaje,nom_equipaje FROM $nom_tabla 
			WHERE (f_ini BETWEEN '$f_ini_iso' AND '$f_fin_iso')
		   		OR (f_fin BETWEEN '$f_ini_iso' AND '$f_fin_iso')
			ORDER BY nom_equipaje";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave) || !isset($aClave[0]) || !is_numeric($aClave[0])) {
                continue;
            }
            $aOpciones[] = (int) $aClave[0];
        }
        return $aOpciones;
    }

    public function getArrayEquipajes(string $f_ini_iso = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $where = '';
        if (!empty($f_ini_iso)) $where = "WHERE f_ini > '$f_ini_iso'";
        $sQuery = "SELECT id_equipaje,nom_equipaje FROM $nom_tabla 
			$where
			ORDER BY f_ini,nom_equipaje";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0];
            $val = $aClave[1];
            if ((!is_int($clave) && !is_string($clave)) || (!is_scalar($val) && $val !== null)) {
                continue;
            }
            $aOpciones[(int) $clave] = (string) $val;
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Equipaje
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Equipaje> Una colección de objetos de tipo Equipaje
     */
    public function getEquipajes(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $EquipajeSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
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
            $sCondicion = " WHERE " . $sCondicion;
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
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
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
            $aDatos = $this->normalizeAssocRow($aDatos);
            // para las fechas del postgres (texto iso)
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $Equipaje = Equipaje::fromArray($aDatos);
            $EquipajeSet->add($Equipaje);
        }
        /** @var list<Equipaje> $result */
        $result = array_values($EquipajeSet->getTot());
        return $result;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Equipaje $Equipaje): bool
    {
        $id_equipaje = $Equipaje->getIdEquipajeVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_equipaje = $id_equipaje";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Equipaje $Equipaje): bool
    {
        $id_equipaje = $Equipaje->getIdEquipajeVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_equipaje);

        $aDatos = $Equipaje->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        /*
        $aDatos = [];
        $aDatos['ids_activ'] = $Equipaje->getIdsActivVo()?->value();
        $aDatos['lugar'] = $Equipaje->getLugarVo()?->value();
        $aDatos['id_ubi_activ'] = $Equipaje->getId_ubi_activ();
        $aDatos['nom_equipaje'] = $Equipaje->getNomEquipajeVo()?->value();
        $aDatos['cabecera'] = $Equipaje->getCabeceraVo()?->value();
        $aDatos['pie'] = $Equipaje->getPieVo()?->value();
        $aDatos['cabecerab'] = $Equipaje->getCabecerabVo()?->value();
        // para las fechas
        $aDatos['f_ini'] = (new ConverterDate('date', $Equipaje->getF_ini()))->toPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $Equipaje->getF_fin()))->toPg();
        array_walk($aDatos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_equipaje']);
            $update = "
                    ids_activ                = :ids_activ,
                    lugar                    = :lugar,
                    f_ini                    = :f_ini,
                    f_fin                    = :f_fin,
                    id_ubi_activ             = :id_ubi_activ,
                    nom_equipaje             = :nom_equipaje,
                    cabecera                 = :cabecera,
                    pie                      = :pie,
                    cabecerab                = :cabecerab";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_equipaje = $id_equipaje";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_equipaje,ids_activ,lugar,f_ini,f_fin,id_ubi_activ,nom_equipaje,cabecera,pie,cabecerab)";
            $valores = "(:id_equipaje,:ids_activ,:lugar,:f_ini,:f_fin,:id_ubi_activ,:nom_equipaje,:cabecera,:pie,:cabecerab)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
    }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_equipaje): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_equipaje = $id_equipaje";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_equipaje
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_equipaje): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_equipaje = $id_equipaje";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        // para las fechas del postgres (texto iso)
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * Busca la clase con id_equipaje en la base de datos .
     */
    public function findById(int $id_equipaje): ?Equipaje
    {
        $aDatos = $this->datosById($id_equipaje);
        if ($aDatos === false) {
            return null;
        }
        return Equipaje::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_equipajes_dl_id_equipaje_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}