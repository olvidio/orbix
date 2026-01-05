<?php

namespace src\notas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\shared\traits\HandlesPdoErrors;


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

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas');
    }

    /**
     * retorna l'última acta d'una regió.
     *
     * @param string regió/dl/? en el que buscar la últim número d'acta.
     * @return integer
     */
    public function getUltimaActa($any, $sRegion = '?'): int
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
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $num = $oDblSt->fetchColumn();
        if ($num != false) {
            // Quitar los {}.
            $num = (integer)trim($num, '{}');
        } else {
            $num = 0;
        }
        return $num;
    }

    /**
     * retorna l'última linea del llibre.
     *
     * @param integer iLibro libro en el que buscar la últmia linea.
     * @return integer
     */
    function getUltimaLinea($iLibro = 1)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ult_pag = $this->getUltimaPagina($iLibro);
        $sQuery = "SELECT max(linea) FROM $nom_tabla WHERE libro='$iLibro' AND pagina='$ult_pag' GROUP BY COALESCE(linea,0) ";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetchColumn();
    }

    /**
     * retorna l'última pàgina del llibre.
     *
     * @param integer iLibro libro en el que buscar la últmia pàgina.
     * @return integer
     */
    function getUltimaPagina($iLibro = 1)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT max(pagina) FROM $nom_tabla WHERE libro=$iLibro GROUP BY libro";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetchColumn();
    }

    /**
     * retorna l'últim llibre d'actes.
     *
     * @return integer
     */
    function getUltimoLibro()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT max(libro) FROM $nom_tabla";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetchColumn();
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActaDl
     */
    public function getActas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
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
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para los bytea: (resources)
            $handle = $aDatos['pdf'];
            if ($handle !== null) {
                $contents = stream_get_contents($handle);
                fclose($handle);
                $pdf = $contents;
                $aDatos['pdf'] = $pdf;
            }
            // para las fechas del postgres (texto iso)
            $aDatos['f_acta'] = (new ConverterDate('date', $aDatos['f_acta']))->fromPg();
            $ActaDl = Acta::fromArray($aDatos);
            $ActaDlSet->add($ActaDl);
        }
        return $ActaDlSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Acta $Acta): bool
    {
        $acta = $Acta->getActa();
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
        $acta = $Acta->getActa();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($acta);

        $aDatos = [];
        $aDatos['id_asignatura'] = $Acta->getId_asignatura();
        $aDatos['id_activ'] = $Acta->getId_activ();
        $aDatos['libro'] = $Acta->getLibro();
        $aDatos['pagina'] = $Acta->getPagina();
        $aDatos['linea'] = $Acta->getLinea();
        $aDatos['lugar'] = $Acta->getLugar();
        $aDatos['observ'] = $Acta->getObserv();
        // para los bytea
        $aDatos['pdf'] = bin2hex($Acta->getPdf());
        // para las fechas
        $aDatos['f_acta'] = (new ConverterDate('date', $Acta->getF_acta()))->toPg();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
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
            $aDatos['acta'] = $Acta->getActa();
            $campos = "(acta,id_asignatura,id_activ,f_acta,libro,pagina,linea,lugar,observ,pdf)";
            $valores = "(:acta,:id_asignatura,:id_activ,:f_acta,:libro,:pagina,:linea,:lugar,:observ,:pdf)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $acta): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE acta = '$acta'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * @return array|bool
     */
    public function datosById(string $acta): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE acta = '$acta'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        // para los bytea, sobre escribo los valores:
        $spdf = '';
        $stmt->bindColumn('pdf', $spdf, PDO::PARAM_STR);
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($aDatos !== false) {
            $aDatos['pdf'] = hex2bin($spdf ?? '');
        }
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_acta'] = (new ConverterDate('date', $aDatos['f_acta']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con acta en la base de datos .
     */
    public function findById(string $acta): ?Acta
    {
        $aDatos = $this->datosById($acta);
        if (empty($aDatos)) {
            return null;
        }
        return Acta::fromArray($aDatos);
    }
}