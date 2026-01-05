<?php

namespace src\asignaturas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\shared\traits\HandlesPdoErrors;
use stdClass;
use function core\is_true;

/**
 * Clase que adapta la tabla xa_asignaturas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class PgAsignaturaRepository extends ClaseRepository implements AsignaturaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xa_asignaturas');
    }

    /**
     * retorna JSON llista d'Asignaturas
     *
     * @param string sQuery la query a executar.
     * @return false|object
     */
    public function getJsonAsignaturas($aWhere): string
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sCondi = '';
        foreach ($aWhere as $camp => $val) {
            if ($camp === 'nombre_asignatura' && !empty($val)) {
                $sCondi .= "WHERE active=true AND nombre_asignatura ILIKE '%$val%'";
            }
            if ($camp === 'id' && !empty($val)) {
                if (!empty($sCondi)) {
                    $sCondi .= " AND id_asignatura = $val";
                } else {
                    $sCondi .= "WHERE id_asignatura = $val";
                }
            }
        }
        $sOrdre = " ORDER BY id_nivel";
        $sLimit = " LIMIT 25";
        $sQuery = "SELECT DISTINCT id_asignatura,nombre_asignatura,id_nivel FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        $json = '[';
        $i = 0;
        foreach ($stmt as $aClave) {
            $i++;
            $id_asignatura = $aClave[0];
            $nombre_asignatura = $aClave[1];
            $nombre_asignatura = str_replace('"', '\\"', $nombre_asignatura);
            $nombre_asignatura = str_replace("'", "\\'", $nombre_asignatura);
            $json .= ($i > 1) ? ',' : '';
            $json .= "{\"value\":\"$id_asignatura\",\"label\":\"$nombre_asignatura\"}";
        }
        $json .= ']';

        return $json;
    }

    /**
     * retorna un array del tipus: id_asignatura => array(nombre_asignatura, creditos)
     *
     * @return array|false
     */
    public function getArrayAsignaturasCreditos(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_asignatura, nombre_asignatura, creditos FROM $nom_tabla ORDER BY id_nivel";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        $aOpciones = [];
        foreach ($stmt as $row) {
            $id_asignatrura = $row[0];
            $nombre_asignatura = $row[1];
            $creditos = $row[2];
            $aOpciones[$id_asignatrura] = array('nombre_asignatura' => $nombre_asignatura, 'creditos' => $creditos);
        }
        return $aOpciones;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Les posibles asignatures
     *
     * @param bool $op_genericas listar o no opcionales genéricas (opcional I...)
     * @return false|object
     */
    public function getArrayAsignaturasConSeparador(bool $op_genericas = true): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sWhere = "WHERE active = 't' ";
        if (!$op_genericas) {
            $genericas = $this->getListaOpGenericas('csv');
            $sWhere .= " AND id_nivel NOT IN ($genericas)";
        }
        //para hacer listados que primero salgan las normales y después las opcionales:
        $sQuery = "SELECT id_asignatura, nombre_asignatura, CASE WHEN id_nivel < 3000 THEN xa_asignaturas.id_nivel ELSE 3001 END AS op FROM $nom_tabla $sWhere ORDER BY op,nombre_asignatura;";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        $aOpciones = [];
        $c = 0;
        foreach ($stmt as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $id_op = $aClave[2];
            if ($id_op > 3000 && $c < 1) {
                $aOpciones[3000] = '----------';
                $c = 1;
            }
            $aOpciones[$clave] = $val;
        }

        return $aOpciones;
    }

    /**
     * Devuelve una lista con los id_nivel de las opcionales.
     *
     * @param string $formato 'csv'
     * @return string
     */
    public function getListaOpGenericas(string $formato = ''): string
    {
        switch ($formato) {
            case 'json':
                $genericas = "[\"1230\",\"1231\",\"1232\",\"2430\",\"2431\",\"2432\",\"2433\",\"2434\"]";
                break;
            case 'csv':
            default:
                $genericas = "1230,1231,1232,2430,2431,2432,2433,2434";
        }
        return $genericas;
    }

    public function getArrayAsignaturas(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_asignatura,nombre_corto FROM $nom_tabla ORDER BY id_asignatura";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            $id_asignatura = $aClave[0];
            $nombre_corto = $aClave[1];
            $aOpciones[$id_asignatura] = $nombre_corto;
        }

        return $aOpciones;
    }

    public function getAsignaturasAsJson($aWhere = [], $aOperators = array()): string
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $jsonAsignaturas = [];
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') {
            $sCondi = " WHERE " . $sCondi;
        }
        $sLimit = '';
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') {
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
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        foreach ($stmt as $aDatos) {
            $oAsignatura =  Asignatura::fromArray($aDatos);
            $oMin = new stdClass();
            $oMin->id_asignatura = $oAsignatura->getId_asignatura();
            $oMin->id_nivel = $oAsignatura->getId_nivel();
            $oMin->nombre_asignatura = $oAsignatura->getNombre_signatura();
            $oMin->creditos = $oAsignatura->getCreditos();
            $jsonAsignaturas[] = json_encode($oMin);
        }
        return json_encode($jsonAsignaturas);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Asignatura
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Asignatura
     */
    public function getAsignaturas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $AsignaturaSet = new Set();
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
            $Asignatura =  Asignatura::fromArray($aDatos);
            $AsignaturaSet->add($Asignatura);
        }
        return $AsignaturaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Asignatura $Asignatura): bool
    {
        $id_asignatura = $Asignatura->getId_asignatura();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_asignatura = $id_asignatura";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Asignatura $Asignatura): bool
    {
        $id_asignatura = $Asignatura->getId_asignatura();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_asignatura);

        $aDatos = [];
        $aDatos['id_nivel'] = $Asignatura->getId_nivel();
        $aDatos['nombre_asignatura'] = $Asignatura->getNombre_signatura();
        $aDatos['nombre_corto'] = $Asignatura->getNombre_corto();
        $aDatos['creditos'] = $Asignatura->getCreditos();
        $aDatos['year'] = $Asignatura->getYear();
        $aDatos['id_sector'] = $Asignatura->getId_sector();
        $aDatos['active'] = $Asignatura->isActive();
        $aDatos['id_tipo'] = $Asignatura->getId_tipo();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['active'])) {
            $aDatos['active'] = 'true';
        } else {
            $aDatos['active'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_nivel                 = :id_nivel,
					nombre_asignatura        = :nombre_asignatura,
					nombre_corto             = :nombre_corto,
					creditos                 = :creditos,
					year                     = :year,
					id_sector                = :id_sector,
					active                   = :active,
					id_tipo                  = :id_tipo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_asignatura = $id_asignatura";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $aDatos['id_asignatura'] = $Asignatura->getId_asignatura();
            $campos = "(id_asignatura,id_nivel,nombre_asignatura,nombre_corto,creditos,year,id_sector,active,id_tipo)";
            $valores = "(:id_asignatura,:id_nivel,:nombre_asignatura,:nombre_corto,:creditos,:year,:id_sector,:active,:id_tipo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_asignatura): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_asignatura = $id_asignatura";
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
     * @param int $id_asignatura
     * @return array|bool
     */
    public function datosById(int $id_asignatura): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_asignatura = $id_asignatura";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Busca la clase con id_asignatura en la base de datos .
     */
    public function findById(int $id_asignatura): ?Asignatura
    {
        $aDatos = $this->datosById($id_asignatura);
        if (empty($aDatos)) {
            return null;
        }
        return Asignatura::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xa_asignaturas_id_asignatura_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}