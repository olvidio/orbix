<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\entity\CasaPeriodo;


/**
 * Clase que adapta la tabla du_periodos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
class PgCasaPeriodoRepository extends ClaseRepository implements CasaPeriodoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(
        private readonly CasaDlRepositoryInterface $casaDlRepository,
    ) {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('du_periodos');
    }

    /**
     * retorna un array per fer cerques més rapid.
     * Si la casa és només de sf o sv, no mira el dossier.
     *
     * @return list<array{iso_ini: string, iso_fin: string, sfsv: int}>
     */
    public function getArrayCasaPeriodos(int $id_ubi, DateTimeLocal $oInicio, DateTimeLocal $oFin): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $inicio_iso = $oInicio->getIso();
        $fin_iso = $oFin->getIso();
        $sQuery = "SELECT to_char(f_ini,'YYYYMMDD') as iso_ini,to_char(f_fin,'YYYYMMDD') as iso_fin, sfsv
			FROM $nom_tabla
			WHERE id_ubi=$id_ubi AND f_fin > '$inicio_iso' AND f_ini <= '$fin_iso'
			ORDER BY f_ini
			";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);        if ($stmt === false) {
            return [];
        }

        $a_periodos = [];
        foreach ($stmt as $row) {
            if (!is_array($row) || !isset($row['iso_ini'], $row['iso_fin'], $row['sfsv'])) {
                continue;
            }
            $a_periodos[] = [
                'iso_ini' => (string) $row['iso_ini'],
                'iso_fin' => (string) $row['iso_fin'],
                'sfsv' => (int) $row['sfsv'],
            ];
        }
        // si no hay resultado miro que el ubi sea solamente de sf o sv.
        if (count($a_periodos) === 0) {
            $oCasa = $this->casaDlRepository->findById($id_ubi);
            if ($oCasa !== null) {
                $sf = $oCasa->isSf();
                $sv = $oCasa->isSv();
                $oInicio->setTime(0, 0, 0);
                $isoIni = $oInicio->format('Ymd');
                $oFin->setTime(23, 59, 59);
                $isoFin = $oFin->format('Ymd');

                if ($sf === true && $sv === false) {
                    $a_periodos[] = array('iso_ini' => $isoIni, 'iso_fin' => $isoFin, 'sfsv' => 2);
                }
                if ($sf === false && $sv === true) {
                    $a_periodos[] = array('iso_ini' => $isoIni, 'iso_fin' => $isoFin, 'sfsv' => 1);
                }
            }
        }
        return $a_periodos;
    }

    /**
     * retorna la suma dels dies d'ocupació d'una secció.
     */
    public function getCasaPeriodosDias(int $iseccion, int $id_ubi, DateTimeLocal $oInicio, DateTimeLocal $oFin): int
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $inicio_iso = $oInicio->getIso();
        $fin_iso = $oFin->getIso();

        $sQuery = "SELECT SUM((date(f_fin)-date(f_ini))+1 )
			FROM $nom_tabla
			WHERE id_ubi=$id_ubi AND f_ini BETWEEN '$inicio_iso' AND '$fin_iso' AND sfsv=$iseccion
			";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return 0;
        }

        $num_dias = $stmt->fetchColumn();
        if (!is_numeric($num_dias)) {
            return 0;
        }

        return (int) $num_dias;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CasaPeriodo
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<CasaPeriodo> Una colección de objetos de tipo CasaPeriodo
     */
    public function getCasaPeriodos(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CasaPeriodoSet = new Set();
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
        $casaPeriodos = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $normalized['f_ini'] = (new ConverterDate('date', $normalized['f_ini']))->fromPg();
            $normalized['f_fin'] = (new ConverterDate('date', $normalized['f_fin']))->fromPg();
            $casaPeriodos[] = CasaPeriodo::fromArray($normalized);
        }
        return $casaPeriodos;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CasaPeriodo $CasaPeriodo): bool
    {
        $id_item = $CasaPeriodo->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CasaPeriodo $CasaPeriodo): bool
    {
        $id_item = $CasaPeriodo->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $CasaPeriodo->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_ubi                   = :id_ubi,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					sfsv                     = :sfsv";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            // INSERT
            $campos = "(id_item,id_ubi,f_ini,f_fin,sfsv)";
            $valores = "(:id_item,:id_ubi,:f_ini,:f_fin,:sfsv)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?CasaPeriodo
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return CasaPeriodo::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('du_periodos_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}