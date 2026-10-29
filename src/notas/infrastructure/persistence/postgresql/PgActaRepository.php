<?php

namespace src\notas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\notas\domain\criteria\ActaField;
use src\notas\infrastructure\persistence\postgresql\traits\NotasCriteriaTrait;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\ConverterDate;
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
    use NotasCriteriaTrait;
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
     * Acta que cumplen el criterio (con el pdf).
     *
     * @return list<Acta>
     */
    public function matching(Criteria $criteria): array
    {
        return $this->actasDesdeFilas($this->filasNotasMatching($criteria, ActaField::class), false);
    }

    /**
     * Igual que {@see matching()} pero sin leer el bytea `pdf` (listados).
     *
     * @return list<Acta>
     */
    public function matchingSinPdf(Criteria $criteria): array
    {
        return $this->actasDesdeFilas(
            $this->filasNotasMatching($criteria, ActaField::class, self::ACTAS_LIST_COLUMNS),
            true
        );
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @return list<Acta>
     */
    private function actasDesdeFilas(array $filas, bool $sinPdf): array
    {
        $items = [];
        foreach ($filas as $normalized) {
            if (!$sinPdf) {
                // para los bytea: (resources)
                $normalized['pdf'] = $this->normalizeBytea($this->readByteaField($normalized['pdf'] ?? null));
            }
            // para las fechas del postgres (texto iso)
            $normalized['f_acta'] = (new ConverterDate('date', $normalized['f_acta']))->fromPg();
            $items[] = Acta::fromArray($normalized);
        }
        return $items;
    }

    /**
     * @deprecated usar matching(Criteria) / matchingSinPdf(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Acta>
     */
    public function getActas(array $aWhere = [], array $aOperators = []): array
    {
        $sinPdf = !empty($aWhere['_sin_pdf']);
        unset($aWhere['_sin_pdf']);
        $adapter = new LegacyWhereAdapter(ActaField::class, []);
        $criteria = $adapter->toCriteria($aWhere, $aOperators);
        return $sinPdf ? $this->matchingSinPdf($criteria) : $this->matching($criteria);
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
					pdf                      = :pdf,
					hash_impreso             = :hash_impreso";
            $sql = "UPDATE $nom_tabla SET $update WHERE acta = '$acta'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(acta,id_asignatura,id_activ,f_acta,libro,pagina,linea,lugar,observ,pdf,hash_impreso)";
            $valores = "(:acta,:id_asignatura,:id_activ,:f_acta,:libro,:pagina,:linea,:lugar,:observ,:pdf,:hash_impreso)";
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