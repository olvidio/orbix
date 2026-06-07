<?php

namespace src\dossiers\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\traits\HandlesPdoErrors;


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
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setNomTabla('d_dossiers_abiertos');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Dossier
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Dossier> Una colección de objetos de tipo Dossier
     */
    public function getDossieres(array $aWhere = [], array $aOperators = []): array
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
        if (isset($aWhere['_ordre']) && is_scalar($aWhere['_ordre']) && (string) $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . (string) $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && is_scalar($aWhere['_limit']) && (string) $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . (string) $aWhere['_limit'];
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
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_camb_dossier'] = (new ConverterDate('date', $aDatos['f_camb_dossier']))->fromPg();
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
            $Dossier = Dossier::fromArray($aDatos);
            $DossierSet->add($Dossier);
        }
        return array_values($DossierSet->getTot());
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Dossier $Dossier): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_tipo_dossier = $Dossier->getId_tipo_dossier();
        $id_pau = $Dossier->getId_pau();
        $tabla = $Dossier->getTablaVo()->value();

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
        $tabla = $Dossier->getTablaVo()->value();
        $bInsert = $this->isNew($id_tipo_dossier, $id_pau, $tabla);

        $aDatos = $Dossier->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_camb_dossier' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_active' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_dossier']);
            unset($aDatos['id_pau']);
            unset($aDatos['tabla']);
            $update = "
					f_ini                    = :f_ini,
					f_camb_dossier           = :f_camb_dossier,
					active                   = :active,
					f_active                 = :f_active";
            $sql = "UPDATE $nom_tabla SET $update WHERE tabla = '$tabla'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_tipo_dossier,id_pau,tabla,f_ini,f_camb_dossier,active,f_active)";
            $valores = "(:id_tipo_dossier,:id_pau,:tabla,:f_ini,:f_camb_dossier,:active,:f_active)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_dossier, int $id_pau, string $tabla): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sql = "SELECT * FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return true;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_dossier, int $id_pau, string $tabla): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
        $aDatos['f_camb_dossier'] = (new ConverterDate('date', $aDatos['f_camb_dossier']))->fromPg();
        $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(DossierPk $pk): array|false
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

}