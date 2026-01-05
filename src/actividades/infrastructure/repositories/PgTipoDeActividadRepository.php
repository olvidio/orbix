<?php

namespace src\actividades\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use PDO;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\shared\traits\HandlesPdoErrors;
use web\TiposActividades;
use function core\is_true;


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
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_tipos_actividad');
    }

    public function getArrayTiposActividad(string $sid_tipo_activ = '......'): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query = "SELECT id_tipo_activ
		   	FROM $nom_tabla  where id_tipo_activ::text ~'" . $sid_tipo_activ . "' order by id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);

        $a_id_tipos = [];
        foreach ($stmt->fetchAll() as $row) {
            $id_tipo_activ = $row['id_tipo_activ'];
            $oTiposActividades = new TiposActividades($id_tipo_activ);
            $nom_tipo = $oTiposActividades->getNom();

            $a_id_tipos[$id_tipo_activ] = $nom_tipo;
        }

        return  $a_id_tipos;
    }

    /**
     * retorna l'array de tipos de procesos posibles per el tipus d'activitat.
     *
     * @param string sid_tipo_activ
     * @param boolean dl_propia
     * @param string ssfsv ( '',1,2,all)
     * @return array|false
     */
    public function getTiposDeProcesos($sid_tipo_activ = '......', $bdl_propia = true, $sfsv = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $a_sfsv = [];
        switch ($sfsv) {
            case 'all':
                $a_sfsv = [1, 2];
                break;
            case 1:
                $a_sfsv = [1];
                break;
            case 2:
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
            foreach ($stmt as $aDades) {
                if (!empty($aDades['id_tipo_proceso'])) {
                    $aTiposDeProcesos[] = $aDades['id_tipo_proceso'];
                }
            }
        }
        return $aTiposDeProcesos;
    }

    public function getId_tipoPosibles($regexp, $expr_txt): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query = "SELECT substring(id_tipo_activ::text from '" . $regexp . "')
		   	FROM $nom_tabla  where id_tipo_activ::text ~'" . $expr_txt . "' order by id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl,$query, __METHOD__, __FILE__, __LINE__);

        $a_id_tipos = [];
        foreach ($stmt->fetchAll() as $row) {
            $id_tipo = $row[0];
            $a_id_tipos[$id_tipo] = true;
        }
        return $a_id_tipos;
    }

    public function getNom_tipoPosibles($num_digitos, $expr_txt): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query = "SELECT * FROM $nom_tabla where id_tipo_activ::text ~'$expr_txt' order by id_tipo_activ";
        $stmt = $this->pdoQuery($oDbl,$query, __METHOD__, __FILE__, __LINE__);

        $tipo_nom = [];
        $nom_tipo = [];
        $i = 0;
        $char_ini = 6 - $num_digitos;
        foreach ($stmt->fetchAll() as $row) {
            $i++;
            $nom_tipo[$i] = $row['nombre'] . '#' . $row['id_tipo_activ'];
            $num = substr($row['id_tipo_activ'], $char_ini, $num_digitos);
            $tipo_nom[$num] = $row['nombre'];
        }
        return ['tipo_nom' => $tipo_nom,
            'nom_tipo' => $nom_tipo];
    }

    public function getAsistentesPosibles($aText, $regexp): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query_ta = "select substr(id_tipo_activ::text,2,1) as ta2
			from $nom_tabla where id_tipo_activ::text ~'" . $regexp . "' group by ta2 order by ta2";
        $stmt = $this->pdoQuery($oDbl, $query_ta, __METHOD__, __FILE__, __LINE__);

        $asistentes = [];
        foreach ($stmt->fetchAll() as $row) {
            $asistentes[$row[0]] = $aText[$row[0]];
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
    public function getActividadesPosibles(int $num_digitos, array $aText, string $expr_txt): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $query_ta = "select substr(id_tipo_activ::text,3,$num_digitos) as ta3
			from $nom_tabla where id_tipo_activ::text ~'$expr_txt' group by ta3 order by ta3";
        $stmt = $this->pdoQuery($oDbl, $query_ta, __METHOD__, __FILE__, __LINE__);

        $actividades = [];
        foreach ($stmt->fetchAll() as $row) {
            $actividades[$row[0]] = $aText[$row[0]];
        }
        return $actividades;
    }

    public function getSfsvPosibles($aText): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "select substr(id_tipo_activ::text,1,1) as ta1 from $nom_tabla where id_tipo_activ::text ~ '' group by ta1 order by ta1";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $sfsv = [];
        foreach ($stmt->fetchAll() as $row) {
            $sfsv[$row[0]] = $aText[$row[0]];
        }
        return $sfsv;
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDeActividad
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TipoDeActividad
     */
    public function getTiposDeActividades(array $aWhere = [], array $aOperators = []): array|false
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
            $TipoDeActividad =  TipoDeActividad::fromArray($aDatos);
            $TipoDeActividadSet->add($TipoDeActividad);
        }
        return $TipoDeActividadSet->getTot();
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

        $aDatos = [];
        $aDatos['nombre'] = $TipoDeActividad->getNombre();
        $aDatos['id_tipo_proceso_sv'] = $TipoDeActividad->getId_tipo_proceso_sv();
        $aDatos['id_tipo_proceso_ex_sv'] = $TipoDeActividad->getId_tipo_proceso_ex_sv();
        $aDatos['id_tipo_proceso_sf'] = $TipoDeActividad->getId_tipo_proceso_sf();
        $aDatos['id_tipo_proceso_ex_sf'] = $TipoDeActividad->getId_tipo_proceso_ex_sf();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
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
            $aDatos['id_tipo_activ'] = $TipoDeActividad->getId_tipo_activ();
            $campos = "(id_tipo_activ,nombre,id_tipo_proceso_sv,id_tipo_proceso_ex_sv,id_tipo_proceso_sf,id_tipo_proceso_ex_sf)";
            $valores = "(:id_tipo_activ,:nombre,:id_tipo_proceso_sv,:id_tipo_proceso_ex_sv,:id_tipo_proceso_sf,:id_tipo_proceso_ex_sf)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_activ = $id_tipo_activ";
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
     * @param int $id_tipo_activ
     * @return array|bool
     */
    public function datosById(int $id_tipo_activ): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_activ = $id_tipo_activ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_tipo_activ en la base de datos .
     */
    public function findById(int $id_tipo_activ): ?TipoDeActividad
    {
        $aDatos = $this->datosById($id_tipo_activ);
        if (empty($aDatos)) {
            return null;
        }
        return TipoDeActividad::fromArray($aDatos);
    }
}