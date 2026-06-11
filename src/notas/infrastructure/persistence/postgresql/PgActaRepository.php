<?php

namespace src\notas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\HandlesPgBytea;


/**
 * Clase que adapta la tabla e_actas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
class PgActaRepository extends ClaseRepository implements ActaRepositoryInterface
{
    use HandlesPdoErrors;
    use HandlesPgBytea;

    private const ACTAS_LIST_COLUMNS = 'acta, id_asignatura, id_activ, f_acta, libro, pagina, linea, lugar, observ';

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas');
    }

    /**
     * retorna l'última acta d'una regió.
     *
     * @param string $sRegion regió/dl/? en el que buscar la últim número d'acta.
     * @return integer
     */
    public function getUltimaActa(string|int $any, string $sRegion = '?'): int
    {
        $sRegion = ($sRegion === '?') ? "\\" . $sRegion : $sRegion;
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT  (regexp_matches(acta, '^\w{1,8}\s+(\d+)/$any'))::numeric[] as num
			FROM $nom_tabla WHERE acta ~* '^$sRegion\s+.*/$any'
			ORDER BY num DESC
			LIMIT 1";
        //echo "ss: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimaActa';
                    $gestor = $_SESSION['oGestorErrores'] ?? null;
        if (is_object($gestor) && method_exists($gestor, 'addErrorAppLastError')) {
            $gestor->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
            return 0;
        }
        $num = $oDblSt->fetchColumn();
        if (is_string($num)) {
            $num = (int) trim($num, '{}');
        } elseif (is_numeric($num)) {
            $num = (int) $num;
        } else {
            $num = 0;
        }
        return $num;
    }

    /**
     * retorna l'última linea del llibre.
     *
     * @param int $iLibro libro en el que buscar la últmia linea.
     * @return integer
     */
    function getUltimaLinea(int $iLibro = 1): int
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ult_pag = $this->getUltimaPagina($iLibro);
        $sQuery = "SELECT max(linea) FROM $nom_tabla WHERE libro='$iLibro' AND pagina='$ult_pag' GROUP BY COALESCE(linea,0) ";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
                    $gestor = $_SESSION['oGestorErrores'] ?? null;
        if (is_object($gestor) && method_exists($gestor, 'addErrorAppLastError')) {
            $gestor->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
            return 0;
        }
        $val = $oDblSt->fetchColumn();
        return is_numeric($val) ? (int) $val : 0;
    }

    /**
     * retorna l'última pàgina del llibre.
     *
     * @param int $iLibro libro en el que buscar la últmia pàgina.
     * @return integer
     */
    function getUltimaPagina(int $iLibro = 1): int
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT max(pagina) FROM $nom_tabla WHERE libro=$iLibro GROUP BY libro";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
                    $gestor = $_SESSION['oGestorErrores'] ?? null;
        if (is_object($gestor) && method_exists($gestor, 'addErrorAppLastError')) {
            $gestor->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
            return 0;
        }
        $val = $oDblSt->fetchColumn();
        return is_numeric($val) ? (int) $val : 0;
    }

    /**
     * retorna l'últim llibre d'actes.
     *
     * @return integer
     */
    function getUltimoLibro(): int
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT max(libro) FROM $nom_tabla";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
                    $gestor = $_SESSION['oGestorErrores'] ?? null;
        if (is_object($gestor) && method_exists($gestor, 'addErrorAppLastError')) {
            $gestor->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
            return 0;
        }
        $val = $oDblSt->fetchColumn();
        return is_numeric($val) ? (int) $val : 0;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaDl
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<\src\notas\domain\entity\Acta> Una colección de objetos de tipo ActaDl
     */
    public function getActas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sinPdf = !empty($aWhere['_sin_pdf']);
        if (isset($aWhere['_sin_pdf'])) {
            unset($aWhere['_sin_pdf']);
        }
        $ActaDlSet = new Set();
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
        $select = $sinPdf ? self::ACTAS_LIST_COLUMNS : '*';
        $sQry = "SELECT $select FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
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
            if (!$sinPdf) {
                // para los bytea: (resources)
                $normalized['pdf'] = $this->normalizeBytea($this->readByteaField($normalized['pdf'] ?? null));
            }
            // para las fechas del postgres (texto iso)
            $normalized['f_acta'] = (new ConverterDate('date', $normalized['f_acta']))->fromPg();
            $ActaDl = Acta::fromArray($normalized);
            $ActaDlSet->add($ActaDl);
        }
        /** @var list<Acta> $items */
        $items = array_values($ActaDlSet->getTot());
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Acta $Acta): bool
    {
        $acta = $Acta->getActaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE acta = '$acta'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Acta $Acta): bool
    {
        $acta = $Acta->getActaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($acta);

        $aDatos = $Acta->toArrayForDatabase([
            'pdf' => fn($v) => ($v ? ('\\x' . bin2hex($v)) : null),
            'f_acta' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['acta']);
            $update = "
					id_asignatura            = :id_asignatura,
					id_activ                 = :id_activ,
					f_acta                   = :f_acta,
					libro                    = :libro,
					pagina                   = :pagina,
					linea                    = :linea,
					lugar                    = :lugar,
					observ                   = :observ,
					pdf                      = :pdf";
            $sql = "UPDATE $nom_tabla SET $update WHERE acta = '$acta'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(acta,id_asignatura,id_activ,f_acta,libro,pagina,linea,lugar,observ,pdf)";
            $valores = "(:acta,:id_asignatura,:id_activ,:f_acta,:libro,:pagina,:linea,:lugar,:observ,:pdf)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $acta): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE acta = '$acta'";
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
     * @param string $acta
     * @return array<string, mixed>|false
     */
    public function datosById(string $acta): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE acta = '$acta'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        // para los bytea: (resources)
        $aDatos['pdf'] = $this->normalizeBytea($this->readByteaField($aDatos['pdf'] ?? null));

        // para las fechas del postgres (texto iso)
        $aDatos['f_acta'] = (new ConverterDate('date', $aDatos['f_acta'] ?? null))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con acta en la base de datos .
     */
    public function findById(string $acta): ?Acta
    {
        $aDatos = $this->datosById($acta);
        if ($aDatos === false) {
            return null;
        }
        return Acta::fromArray($aDatos);
    }
}