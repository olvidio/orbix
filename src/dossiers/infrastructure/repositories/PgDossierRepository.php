<?php

namespace src\dossiers\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\ConverterDate;
use core\Set;
use PDO;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;


/**
 * Clase que adapta la tabla d_dossiers_abiertos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class PgDossierRepository extends ClaseRepository implements DossierRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_dossiers_abiertos');
    }

    public function DossiersNotEmpty($pau = '', $id = ''): array|false
    {
        $esquema = ConfigGlobal::mi_region_dl();
        $oDbl = $this->getoDbl();
        $oDossierSet = new Set();
        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $cTiposDossier = $TipoDossierRepository->getTiposDossiers(array('tabla_from' => $pau));
        $db_anterior = 0;
        foreach ($cTiposDossier as $oTipoDossier) {
            $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
            $tabla_to = $oTipoDossier->getTabla_to();
            $campo_to = $oTipoDossier->getCampo_to();
            $db = $oTipoDossier->getDb();
            // Cambiar la conexión a la DB si está en otra:
            if ($db != $db_anterior) {
                $this->cambiarConexion($db);
                $oDbl = $this->getoDbl();
            }
            //comprobar que la tabla existe
            if (empty($tabla_to)) {
                continue;
            }
            $sQry = "SELECT to_regclass('\"$esquema\".$tabla_to')";
            $exist = $oDbl->query($sQry)->fetchColumn();
            if (empty($exist)) {
                $db_anterior = $db;
                continue;
            }
            //miro si tiene contenido
            $sQuery = "SELECT * FROM $tabla_to WHERE $campo_to = $id LIMIT 2";
            if (($oDblSt = $oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorDossier.comprobar.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            if ($oDblSt->rowCount() > 0) {
                $a_pkey = array('tabla' => $pau,
                    'id_pau' => $id,
                    'id_tipo_dossier' => $id_tipo_dossier);
                $oDossier = new Dossier($a_pkey);
                $oDossier->DBCarregar();
                $oDossierSet->add($oDossier);
            }
            $db_anterior = $db;
        }
        // Volver la conexión al orignal, por si acaso.
        $this->cambiarConexion(TipoDossier::DB_INTERIOR);

        return $oDossierSet->getTot();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Dossier
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Dossier
     */
    public function getDossieres(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $DossierSet = new Set();
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
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_camb_dossier'] = (new ConverterDate('date', $aDatos['f_camb_dossier']))->fromPg();
            $aDatos['f_status'] = (new ConverterDate('date', $aDatos['f_status']))->fromPg();
            $Dossier = new Dossier();
            $Dossier->setAllAttributes($aDatos);
            $DossierSet->add($Dossier);
        }
        return $DossierSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Dossier $Dossier): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_tipo_dossier = $Dossier->getId_tipo_dossier();
        $id_pau = $Dossier->getId_pau();
        $tabla = $Dossier->getTabla();

        $sql = "DELETE FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Dossier $Dossier): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_tipo_dossier = $Dossier->getId_tipo_dossier();
        $id_pau = $Dossier->getId_pau();
        $tabla = $Dossier->getTabla();
        $bInsert = $this->isNew($id_tipo_dossier, $id_pau, $tabla);

        $aDatos = [];
        $aDatos['status_dossier'] = $Dossier->isStatus_dossier();
        // para las fechas
        $aDatos['f_ini'] = (new ConverterDate('date', $Dossier->getF_ini()))->toPg();
        $aDatos['f_camb_dossier'] = (new ConverterDate('date', $Dossier->getF_camb_dossier()))->toPg();
        $aDatos['f_status'] = (new ConverterDate('date', $Dossier->getF_status()))->toPg();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['status_dossier'])) {
            $aDatos['status_dossier'] = 'true';
        } else {
            $aDatos['status_dossier'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					f_ini                    = :f_ini,
					f_camb_dossier           = :f_camb_dossier,
					status_dossier           = :status_dossier,
					f_status                 = :f_status";
            $sql = "UPDATE $nom_tabla SET $update WHERE tabla = '$tabla'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_tipo_dossier'] = $id_tipo_dossier;
            $aDatos['id_pau'] = $id_pau;
            $aDatos['tabla'] = $tabla;
            $campos = "(id_tipo_dossier,id_pau,tabla,f_ini,f_camb_dossier,status_dossier,f_status)";
            $valores = "(:id_tipo_dossier,:id_pau,:tabla,:f_ini,:f_camb_dossier,:status_dossier,:f_status)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_dossier, int $id_pau, string $tabla): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sql = "SELECT * FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    public function datosById(int $id_tipo_dossier, int $id_pau, string $tabla): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_camb_dossier'] = (new ConverterDate('date', $aDatos['f_camb_dossier']))->fromPg();
            $aDatos['f_status'] = (new ConverterDate('date', $aDatos['f_status']))->fromPg();
        }
        return $aDatos;
    }

    public function datosByPk(DossierPk $pk): array|bool
    {
        return $this->datosById($pk->idTipoDossier(), $pk->idPau(), $pk->tabla());
    }

    public function crearDossier(DossierPk $pk): Dossier
    {
        $aDatos['tabla'] = $pk->tabla();
        $aDatos['id_pau'] = $pk->idPau();
        $aDatos['id_tipo_dossier'] = $pk->idTipoDossier();

        return (new Dossier())->setAllAttributes($aDatos);
    }

    /**
     * Busca la clase con tabla en la base de datos .
     */
    public function findById(int $id_tipo_dossier, int $id_pau, string $tabla): ?Dossier
    {
        $aDatos = $this->datosById($id_tipo_dossier, $id_pau, $tabla);
        if (empty($aDatos)) {
            return null;
        }
        return (new Dossier())->setAllAttributes($aDatos);
    }

    public function findByPk(DossierPk $pk): ?Dossier
    {
        return $this->findById($pk->idTipoDossier(), $pk->idPau(), $pk->tabla());
    }

    private function cambiarConexion($db)
    {
        switch ($db) {
            case TipoDossier::DB_COMUN:
                $oDbl = $GLOBALS['oDBC'];
                $this->setoDbl($oDbl);
                break;
            case TipoDossier::DB_INTERIOR:
                $oDbl = $GLOBALS['oDB'];
                $this->setoDbl($oDbl);
                break;
            case TipoDossier::DB_EXTERIOR:
                $oDbl = $GLOBALS['oDBE'];
                $this->setoDbl($oDbl);
                break;
        }
    }
}