<?php

namespace src\encargossacd\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTipo;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla encargo_tipo a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class PgEncargoTipoRepository extends ClaseRepository implements EncargoTipoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('encargo_tipo');
    }

    /**
     * Devuelve el número del tipo de encargo para hacer una selección SQL.
     *
     *     En función de los parámetros que se le pasan:
     *        $grupo        ctr,cgi,igl,otros,personales
     *        $nom tipo    (el encargo en concreto)
     *    Si un parámetro se omite, se pone un punto (.) para que la búsqueda sea cualquier número
     *    ejemplo: 12....
     */
    public function id_tipo_encargo($grupo, $nom_tipo): string
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $condta1 = '.';
        $condta2 = '.';
        $condta3 = '..';
        $condta = $condta1 . $condta2 . $condta3;

        if ($nom_tipo and $nom_tipo !== "...") {
            $condicion = "id_tipo_enc::text ~ '" . $condta . "'";
            $query = "SELECT * FROM $nom_tabla where $condicion";
            $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);
            $row = $stmt->fetch();
            $id_tipo_enc = $row["id_tipo_enc"];
            $condta = $id_tipo_enc;
        }

        return $condta;
    }

    /**
     * Devuelve los parámetros de un encargo en función del tipo de encargo.
     *
     * Es la función inversa de "id_tipo_encargo()".
     * Se le pasa el id_tipo_encargo, y devuelve un array ($tipo) con los siguientes valores:
     *
     *        grupo        ctr,cgi,igl,otros,personales
     *        nom_tipo    (el encargo en concreto)
     *
     * @author    Daniel Serrabou
     * @since        28/2/06.
     *
     */
    public function encargo_de_tipo($id_tipo_enc): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $t_grupo = EncargoTipo::GRUPO;

        $ta1 = substr($id_tipo_enc, 0, 1);
        $ta2 = substr($id_tipo_enc, 1, 3);

        if ($ta1 === ".") {
            ksort($t_grupo);
            $grupo = $t_grupo;
        } else {
            $grupo = $t_grupo[$ta1];
        }

        $query = "SELECT * FROM $nom_tabla where id_tipo_enc::text ~ '^" . $id_tipo_enc . "' order by tipo_enc";
        $stmt = $this->pdoQuery($oDbl, $query, __METHOD__, __FILE__, __LINE__);

        $nom_tipo = [];
        if ($ta2 === "...") {
            $i = 0;
            foreach ($stmt->fetchAll() as $row) {
                $nom_tipo[$row["id_tipo_enc"]] = $row["tipo_enc"];
                $i++;
            }
        } else {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $nom_tipo = $row["tipo_enc"];
        }

        $tipo = array(
            "grupo" => $grupo,
            "nom_tipo" => $nom_tipo
        );

        return $tipo;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoTipo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo EncargoTipo
     */
    public function getEncargoTipos(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $EncargoTipoSet = new Set();
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
            $EncargoTipo = EncargoTipo::fromArray($aDatos);
            $EncargoTipoSet->add($EncargoTipo);
        }
        return $EncargoTipoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoTipo $EncargoTipo): bool
    {
        $id_tipo_enc = $EncargoTipo->getId_tipo_enc();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_enc = $id_tipo_enc";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(EncargoTipo $EncargoTipo): bool
    {
        $id_tipo_enc = $EncargoTipo->getId_tipo_enc();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_enc);

        $aDatos = $EncargoTipo->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_enc']);
            $update = "
					tipo_enc                 = :tipo_enc,
					mod_horario              = :mod_horario";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_enc = $id_tipo_enc";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_tipo_enc,tipo_enc,mod_horario)";
            $valores = "(:id_tipo_enc,:tipo_enc,:mod_horario)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);    }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_enc): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_enc = $id_tipo_enc";
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
     * @param int $id_tipo_enc
     * @return array|bool
     */
    public function datosById(int $id_tipo_enc): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_enc = $id_tipo_enc";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_tipo_enc en la base de datos .
     */
    public function findById(int $id_tipo_enc): ?EncargoTipo
    {
        $aDatos = $this->datosById($id_tipo_enc);
        if (empty($aDatos)) {
            return null;
        }
        return EncargoTipo::fromArray($aDatos);
    }
}