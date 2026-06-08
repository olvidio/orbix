<?php

namespace src\actividades\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\actividades\domain\entity\TiposActividades;
use function src\shared\domain\helpers\is_true;


/**
 * Clase que adapta la tabla a_tipos_actividad a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PgTipoDeActividadRepository extends ClaseRepository implements TipoDeActividadRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('a_tipos_actividad');
    }

    public function getArrayTiposActividad(string $sid_tipo_activ = '......'): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query = "SELECT id_tipo_activ
		   	FROM $nom_tabla  where id_tipo_activ::text ~'" . $sid_tipo_activ . "' order by id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $a_id_tipos = [];
        foreach ($stmt->fetchAll() as $row) {
            $id_tipo_activ = $row['id_tipo_activ'];
            $oTiposActividades = new TiposActividades($id_tipo_activ);
            $nom_tipo = $oTiposActividades->getNom();

            $a_id_tipos[$id_tipo_activ] = $nom_tipo;
        }

        return $a_id_tipos;
    }

    /**
     * @return list<int>
     */
    public function getTiposDeProcesos(string $sid_tipo_activ = '......', bool $bdl_propia = true, string $sfsv = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $a_sfsv = [];
        switch ($sfsv) {
            case 'all':
                $a_sfsv = [1, 2];
                break;
            case '1':
                $a_sfsv = [1];
                break;
            case '2':
                $a_sfsv = [2];
                break;
            default:
                $isfsv = ConfigGlobal::mi_sfsv();
                $a_sfsv = [$isfsv];
        }

        $aTiposDeProcesos = [];
        foreach ($a_sfsv as $isfsv) {
            if ($isfsv == 1) {
                $nom_tipo_proceso = "id_tipo_proceso_sv";
                $nom_tipo_proceso_ex = "id_tipo_proceso_ex_sv";
            } else {
                $nom_tipo_proceso = "id_tipo_proceso_sf";
                $nom_tipo_proceso_ex = "id_tipo_proceso_ex_sf";
            }
            if (is_true($bdl_propia)) {
                $sQry = "SELECT $nom_tipo_proceso as id_tipo_proceso 
                            FROM $nom_tabla 
                            WHERE id_tipo_activ::text ~ '^$sid_tipo_activ' 
                            GROUP BY $nom_tipo_proceso";
            } else {
                $sQry = "SELECT $nom_tipo_proceso_ex as id_tipo_proceso 
                        FROM $nom_tabla 
                        WHERE id_tipo_activ::text ~ '^$sid_tipo_activ' 
                        GROUP BY $nom_tipo_proceso_ex";
            }
            $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
            if ($stmt === false) {
                return [];
            }
            foreach ($stmt as $aDades) {
                if (!is_array($aDades)) {
                    continue;
                }
                $idTipoProceso = $aDades['id_tipo_proceso'] ?? null;
                if (is_numeric($idTipoProceso)) {
                    $aTiposDeProcesos[] = (int) $idTipoProceso;
                }
            }
        }
        return $aTiposDeProcesos;
    }

    public function getId_tipoPosibles(string $regexp, string $filtro_regexp_txt): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query = "SELECT substring(id_tipo_activ::text from '" . $regexp . "')
		   	FROM $nom_tabla  where id_tipo_activ::text ~'" . $filtro_regexp_txt . "' order by id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $a_id_tipos = [];
        foreach ($stmt->fetchAll() as $row) {
            if (!is_array($row)) {
                continue;
            }
            $id_tipo = $row[0] ?? null;
            if (is_int($id_tipo) || is_string($id_tipo)) {
                $a_id_tipos[$id_tipo] = true;
            }
        }
        return $a_id_tipos;
    }

    public function getNom_tipoPosibles(int $num_digitos, string $filtro_regexp_txt): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query = "SELECT * FROM $nom_tabla where id_tipo_activ::text ~'$filtro_regexp_txt' order by id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return ['tipo_nom' => [], 'nom_tipo' => []];
        }
        $tipo_nom = [];
        $nom_tipo = [];
        $i = 0;
        $char_ini = 6 - $num_digitos;
        foreach ($stmt->fetchAll() as $row) {
            if (!is_array($row)) {
                continue;
            }
            $i++;
            $nombreRaw = $row['nombre'] ?? '';
            $idTipoRaw = $row['id_tipo_activ'] ?? '';
            $nombre = is_scalar($nombreRaw) ? (string) $nombreRaw : '';
            $idTipo = is_scalar($idTipoRaw) ? (string) $idTipoRaw : '';
            $nom_tipo[$i] = $nombre . '#' . $idTipo;
            $num = substr($idTipo, $char_ini, $num_digitos);
            $tipo_nom[$num] = $nombre;
        }
        return ['tipo_nom' => $tipo_nom,
            'nom_tipo' => $nom_tipo];
    }

    public function getAsistentesPosibles(array $aText, string $filtro_regex_txt): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query_ta = "select substr(id_tipo_activ::text,2,1) as ta2
			from $nom_tabla where id_tipo_activ::text ~'" . $filtro_regex_txt . "' group by ta2 order by ta2";
        $stmt = $this->pdoQuery($oDbl, $query_ta, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $asistentes = [];
        foreach ($stmt->fetchAll() as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = $row[0] ?? null;
            if (is_int($key) || is_string($key)) {
                $asistentes[$key] = $aText[$key] ?? '';
            }
        }
        return $asistentes;
    }

    /**
     *
     * @param integer $num_digitos Número de digitos que se toman (1 o 2)
     * @param array $aText
     * @param string $expr_txt
     * @return string[]
     */
    /**
     * @param array<int|string, string> $aText
     * @return array<int|string, string>
     */
    public function getActividadesPosibles(int $num_digitos, array $aText, string $expr_txt): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query_ta = "select substr(id_tipo_activ::text,3,$num_digitos) as ta3
			from $nom_tabla where id_tipo_activ::text ~'$expr_txt' group by ta3 order by ta3";
        $stmt = $this->pdoQuery($oDbl, $query_ta, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $actividades = [];
        foreach ($stmt->fetchAll() as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = $row[0] ?? null;
            if (is_int($key) || is_string($key)) {
                $actividades[$key] = $aText[$key] ?? '';
            }
        }
        return $actividades;
    }

    public function getSfsvPosibles(array $aText): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "select substr(id_tipo_activ::text,1,1) as ta1 from $nom_tabla where id_tipo_activ::text ~ '' group by ta1 order by ta1";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $sfsv = [];
        foreach ($stmt->fetchAll() as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = $row[0] ?? null;
            if (is_int($key) || is_string($key)) {
                $sfsv[$key] = $aText[$key] ?? '';
            }
        }
        return $sfsv;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<TipoDeActividad>
     */
    public function getTiposDeActividades(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $TipoDeActividadSet = new Set();
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
        /** @var list<TipoDeActividad> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = TipoDeActividad::fromArray($normalized);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDeActividad $TipoDeActividad): bool
    {
        $id_tipo_activ = $TipoDeActividad->getId_tipo_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_activ = $id_tipo_activ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoDeActividad $TipoDeActividad): bool
    {
        $id_tipo_activ = $TipoDeActividad->getId_tipo_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_activ);

        $aDatos = $TipoDeActividad->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_activ']);
            $update = "
					nombre                   = :nombre,
					id_tipo_proceso_sv       = :id_tipo_proceso_sv,
					id_tipo_proceso_ex_sv    = :id_tipo_proceso_ex_sv,
					id_tipo_proceso_sf       = :id_tipo_proceso_sf,
					id_tipo_proceso_ex_sf    = :id_tipo_proceso_ex_sf";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_activ = $id_tipo_activ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_tipo_activ,nombre,id_tipo_proceso_sv,id_tipo_proceso_ex_sv,id_tipo_proceso_sf,id_tipo_proceso_ex_sf)";
            $valores = "(:id_tipo_activ,:nombre,:id_tipo_proceso_sv,:id_tipo_proceso_ex_sv,:id_tipo_proceso_sf,:id_tipo_proceso_ex_sf)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_activ = $id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * @param int $id_tipo_activ
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_activ): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_activ = $id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_tipo_activ en la base de datos .
     */
    public function findById(int $id_tipo_activ): ?TipoDeActividad
    {
        $aDatos = $this->datosById($id_tipo_activ);
        if ($aDatos === false) {
            return null;
        }
        return TipoDeActividad::fromArray($aDatos);
    }
}