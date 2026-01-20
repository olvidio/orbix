<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Centro;
use function core\is_true;

/**
 * Clase que adapta la tabla u_centros a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
class PgCentroRepository extends ClaseRepository implements CentroRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('u_centros');
    }

    public function getArrayCentrosCdc(string $condicion = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        //$orden = 'nombre_ubi';

        $sWhere = "WHERE active = 't' AND cdc='t' ";
        if (!empty($condicion)) {
            $sWhere .= 'AND ' . $condicion;
        }
        $sQuery = "SELECT id_ubi, nombre_ubi FROM $nom_tabla $sWhere";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $a_ctr = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $a_ctr[$id_ubi] = $nombre_ubi;
        }

        return $a_ctr;
    }

    public function getArrayCentros(string $condicion = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $orden = 'nombre_ubi';
        if (empty($condicion)) $condicion = "WHERE active = 't'";
        $sQuery = "SELECT id_ubi, nombre_ubi FROM $nom_tabla $condicion ORDER BY $orden";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aCentros = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $aCentros[$id_ubi] = $nombre_ubi;
        }

        return $aCentros;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Centro
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Centro
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CentroSet = new Set();
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
            // para las fechas del postgres (texto iso)
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
            $Centro = Centro::fromArray($aDatos);
            $CentroSet->add($Centro);
        }
        return $CentroSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Centro $Centro): bool
    {
        $id_ubi = $Centro->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_ubi = $id_ubi";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Centro $Centro): bool
    {
        $id_ubi = $Centro->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_ubi);

        $aDatos = $Centro->toArrayForDatabase([
            'f_active' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);
        // es posible que tenga los parametros de: repoCasaDireccion y repoDIreccion
        unset($aDatos['repoCasaDireccion'], $aDatos['repoDireccion']);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_ubi']);
            $update = "
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					active                   = :active,
					f_active                 = :f_active,
					sv                       = :sv,
					sf                       = :sf,
					tipo_ctr                 = :tipo_ctr,
					tipo_labor               = :tipo_labor,
					cdc                      = :cdc,
					id_ctr_padre             = :id_ctr_padre";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_ubi = $id_ubi";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,active,f_active,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre)";
            $valores = "(:tipo_ubi,:id_ubi,:nombre_ubi,:dl,:pais,:region,:active,:f_active,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc,:id_ctr_padre)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);    }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_ubi): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi";
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
     * @param int $id_ubi
     * @return array|bool
     */
    public function datosById(int $id_ubi): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
        }
        return $aDatos;
    }

    /**
     * Busca la clase con id_ubi en la base de datos .
     */
    public function findById(int $id_ubi): ?Centro
    {
        $aDatos = $this->datosById($id_ubi);
        if (empty($aDatos)) {
            return null;
        }
        return Centro::fromArray($aDatos);
    }
}