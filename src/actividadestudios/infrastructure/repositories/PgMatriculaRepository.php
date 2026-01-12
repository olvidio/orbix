<?php

namespace src\actividadestudios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;


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
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_matriculas_activ');
    }


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

        foreach ($stmt as $aDatos) {
            $Matricula =  Matricula::fromArray($aDatos);
            $MatriculaSet->add($Matricula);
        }
        return $MatriculaSet->getTot();
    }


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Matricula
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Matricula
     */
    public function getMatriculas(array $aWhere = [], array $aOperators = []): array|false
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
            $Matricula =  Matricula::fromArray($aDatos);
            $MatriculaSet->add($Matricula);
        }
        return $MatriculaSet->getTot();
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
            $update = "
					id_nivel                 = :id_nivel,
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
            $aDatos['id_activ'] = $id_activ;
            $aDatos['id_asignatura'] = $id_asignatura;
            $aDatos['id_nom'] = $id_nom;
            $campos = "(id_activ,id_asignatura,id_nom,id_situacion,preceptor,id_nivel,nota_num,nota_max,id_preceptor,acta)";
            $valores = "(:id_activ,:id_asignatura,:id_nom,:id_situacion,:preceptor,:id_nivel,:nota_num,:nota_max,:id_preceptor,:acta)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ, int $id_asignatura, int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";
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
     * @param int $id_activ
     * @return array|bool
     */
    public function datosById(int $id_activ, int $id_asignatura, int $id_nom): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
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
}