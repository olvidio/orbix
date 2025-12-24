<?php

namespace src\dossiers\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\TipoDossierId;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;


/**
 * Clase que adapta la tabla d_tipos_dossiers a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class PgTipoDossierRepository extends ClaseRepository implements TipoDossierRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_tipos_dossiers');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDossier
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TipoDossier
     */
    public function getTiposDossiers(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $TipoDossierSet = new Set();
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
            $TipoDossier = new TipoDossier();
            $TipoDossier->setAllAttributes($aDatos);
            $TipoDossierSet->add($TipoDossier);
        }
        return $TipoDossierSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function findByIdVO(TipoDossierId $id): ?TipoDossier
    {
        return $this->findById($id->value());
    }

    public function datosByIdVO(TipoDossierId $id): array|bool
    {
        return $this->datosById($id->value());
    }

    public function Eliminar(TipoDossier $TipoDossier): bool
    {
        $id_tipo_dossier = $TipoDossier->getId_tipo_dossier();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_dossier = $id_tipo_dossier";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoDossier $TipoDossier): bool
    {
        $id_tipo_dossier = $TipoDossier->getId_tipo_dossier();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_dossier);

        $aDatos = [];
        $aDatos['descripcion'] = $TipoDossier->getDescripcion();
        $aDatos['tabla_from'] = $TipoDossier->getTabla_from();
        $aDatos['tabla_to'] = $TipoDossier->getTabla_to();
        $aDatos['campo_to'] = $TipoDossier->getCampo_to();
        $aDatos['id_tipo_dossier_rel'] = $TipoDossier->getId_tipo_dossier_rel();
        $aDatos['permiso_lectura'] = $TipoDossier->getPermiso_lectura();
        $aDatos['permiso_escritura'] = $TipoDossier->getPermiso_escritura();
        $aDatos['depende_modificar'] = $TipoDossier->isDepende_modificar();
        $aDatos['app'] = $TipoDossier->getApp();
        $aDatos['class'] = $TipoDossier->getClass();
        $aDatos['db'] = $TipoDossier->getDb();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['depende_modificar'])) {
            $aDatos['depende_modificar'] = 'true';
        } else {
            $aDatos['depende_modificar'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					descripcion              = :descripcion,
					tabla_from               = :tabla_from,
					tabla_to                 = :tabla_to,
					campo_to                 = :campo_to,
					id_tipo_dossier_rel      = :id_tipo_dossier_rel,
					permiso_lectura          = :permiso_lectura,
					permiso_escritura        = :permiso_escritura,
					depende_modificar        = :depende_modificar,
					app                      = :app,
					class                    = :class,
					db                       = :db";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_dossier = $id_tipo_dossier";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_tipo_dossier'] = $TipoDossier->getId_tipo_dossier();
            $campos = "(id_tipo_dossier,descripcion,tabla_from,tabla_to,campo_to,id_tipo_dossier_rel,permiso_lectura,permiso_escritura,depende_modificar,app,class,db)";
            $valores = "(:id_tipo_dossier,:descripcion,:tabla_from,:tabla_to,:campo_to,:id_tipo_dossier_rel,:permiso_lectura,:permiso_escritura,:depende_modificar,:app,:class,:db)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private
    function isNew(int $id_tipo_dossier): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_dossier = $id_tipo_dossier";
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
     * @param int $id_tipo_dossier
     * @return array|bool
     */
    public
    function datosById(int $id_tipo_dossier): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_dossier = $id_tipo_dossier";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_tipo_dossier en la base de datos .
     */
    public
    function findById(int $id_tipo_dossier): ?TipoDossier
    {
        $aDatos = $this->datosById($id_tipo_dossier);
        if (empty($aDatos)) {
            return null;
        }
        return (new TipoDossier())->setAllAttributes($aDatos);
    }
}