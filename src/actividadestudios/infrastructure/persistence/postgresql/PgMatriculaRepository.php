<?php

namespace src\actividadestudios\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\actividadestudios\domain\value_objects\ActividadMatriculaPk;
use src\shared\traits\HandlesPdoErrors;
use function src\shared\domain\helpers\is_true;


/**
 * Clase que adapta la tabla d_matriculas_activ a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
class PgMatriculaRepository extends ClaseRepository implements MatriculaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_matriculas_activ');
    }


    /**
     * @return list<Matricula>
     */
    public function getMatriculasPendientes(?int $id_nom = null): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $MatriculaSet = new Set();

        if (!empty($id_nom)) {
            $sQry = "SELECT * FROM $nom_tabla Where id_nom = $id_nom AND id_situacion IS NULL";
        } else {
            $sQry = "SELECT * FROM $nom_tabla Where id_situacion IS NULL";
        }
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        /** @var list<Matricula> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = Matricula::fromArray($normalized);
        }
        return $items;
    }


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Matricula>
     */
    public function getMatriculas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $MatriculaSet = new Set();
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
        if (isset($aWhere['_ordre']) && is_scalar($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . (string) $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && is_scalar($aWhere['_limit']) && $aWhere['_limit'] !== '') {
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
        /** @var list<Matricula> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = Matricula::fromArray($normalized);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Matricula $Matricula): bool
    {
        $id_activ = $Matricula->getId_activ();
        $id_asignatura = $Matricula->getIdAsignaturaVo()->value();
        $id_nom = $Matricula->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Matricula $Matricula): bool
    {
        $id_activ = $Matricula->getId_activ();
        $id_asignatura = $Matricula->getIdAsignaturaVo()->value();
        $id_nom = $Matricula->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_asignatura, $id_nom);

        $aDatos = $Matricula->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            unset($aDatos['id_asignatura']);
            unset($aDatos['id_nom']);
            $update = "
					id_situacion             = :id_situacion,
					preceptor                = :preceptor,
					id_nivel                 = :id_nivel,
					nota_num                 = :nota_num,
					nota_max                 = :nota_max,
					id_preceptor             = :id_preceptor,
					acta                     = :acta";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_activ,id_asignatura,id_nom,id_situacion,preceptor,id_nivel,nota_num,nota_max,id_preceptor,acta)";
            $valores = "(:id_activ,:id_asignatura,:id_nom,:id_situacion,:preceptor,:id_nivel,:nota_num,:nota_max,:id_preceptor,:acta)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ, int $id_asignatura, int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";
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
    public function datosById(int $id_activ, int $id_asignatura, int $id_nom): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * @return array<string, mixed>|false
     */
    public function datosByPk(ActividadMatriculaPk $pk): array|false
    {
        return $this->datosById($pk->idActiv(), $pk->idAsignatura(), $pk->idNom());
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ, int $id_asignatura, int $id_nom): ?Matricula
    {
        $aDatos = $this->datosById($id_activ, $id_asignatura, $id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return Matricula::fromArray($aDatos);
    }

    public function findByPk(ActividadMatriculaPk $pk): ?Matricula
    {
        return $this->findById($pk->idActiv(), $pk->idAsignatura(), $pk->idNom());
    }
}