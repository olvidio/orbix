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
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
            $Dossier = Dossier::fromArray($aDatos);
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
        $aDatos['active'] = $Dossier->isActive();
        // para las fechas
        $aDatos['f_ini'] = (new ConverterDate('date', $Dossier->getF_ini()))->toPg();
        $aDatos['f_camb_dossier'] = (new ConverterDate('date', $Dossier->getF_camb_dossier()))->toPg();
        $aDatos['f_active'] = (new ConverterDate('date', $Dossier->getF_active()))->toPg();
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
					f_ini                    = :f_ini,
					f_camb_dossier           = :f_camb_dossier,
					active                   = :active,
					f_active                 = :f_active";
            $sql = "UPDATE $nom_tabla SET $update WHERE tabla = '$tabla'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_tipo_dossier'] = $id_tipo_dossier;
            $aDatos['id_pau'] = $id_pau;
            $aDatos['tabla'] = $tabla;
            $campos = "(id_tipo_dossier,id_pau,tabla,f_ini,f_camb_dossier,active,f_active)";
            $valores = "(:id_tipo_dossier,:id_pau,:tabla,:f_ini,:f_camb_dossier,:active,:f_active)";
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
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
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

        return Dossier::fromArray($aDatos);
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
        return Dossier::fromArray($aDatos);
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