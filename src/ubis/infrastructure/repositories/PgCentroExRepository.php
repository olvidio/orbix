<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\ConverterDate;
use core\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\entity\CentroEx;
use src\utils_database\domain\GenerateIdGlobal;
use function core\is_true;

/**
 * Clase que adapta la tabla u_centros_ex a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
class PgCentroExRepository extends ClaseRepository implements CentroExRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBR'];
        $oDbl_Select = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('u_centros_ex');
    }

    public function getArrayCentros($sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $orden = 'nombre_ubi';
        if (empty($sCondicion)) $sCondicion = "WHERE active = 't'";
        $sQuery = "SELECT id_ubi, nombre_ubi FROM $nom_tabla $sCondicion ORDER BY $orden";
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
     * devuelve una colección (array) de objetos de tipo CentroEx
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CentroEx
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CentroExSet = new Set();
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
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere,__METHOD__, __FILE__, __LINE__);

        foreach ($stmt as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
            $CentroEx = CentroEx::fromArray($aDatos);
            $CentroExSet->add($CentroEx);
        }
        return $CentroExSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CentroEx $CentroEx): bool
    {
        $id_ubi = $CentroEx->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_ubi = $id_ubi";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CentroEx $CentroEx): bool
    {
        $id_ubi = $CentroEx->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_ubi);

        $aDatos = $CentroEx->toArrayForDatabase([
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
    public function findById(int $id_ubi): ?CentroEx
    {
        $aDatos = $this->datosById($id_ubi);
        if (empty($aDatos)) {
            return null;
        }
        return CentroEx::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_centros_ex_id_auto_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    /**
     * @throws \Exception
     */
    public function getNewIdUbi($id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }
}