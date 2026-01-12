<?php

namespace src\actividadestudios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_asignaturas_activ_all a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
class PgActividadAsignaturaRepository extends ClaseRepository implements ActividadAsignaturaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asignaturas_activ_all');
    }

    /**
     * retorna l'array amb les asignatures, credits i nivell stgr del ca
     *
     * @param int biginteger id_activ
     * @param string tipo  tipo='p' para preceptor
     * @return array asignaturas es un array (id_asignatura=>Creditos);
     */
    public function getAsignaturasCa(int $id_activ, string $tipo = ''):array
    {
        /**
         * Array con  id_asignatura => array(nombre_asignatura, creditos)
         * para no tener que consultar cada vez a la base de datos.
         *
         */
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $aAsigDatos = $AsignaturaRepository->getArrayAsignaturasCreditos();

        // por cada ca creo un array con las asignaturas y los créditos.
        $aWhere['id_activ'] = $id_activ;
        $aOperador = [];
        if (empty($tipo)) {
            $aWhere['tipo'] = 'NULL';
            $aOperador['tipo'] = 'IS NULL';
        } else {
            $aWhere['tipo'] = $tipo;
        }
        $cActividadAsignaturas = $this->getActividadAsignaturas($aWhere, $aOperador);
        $aAsignaturasCa = [];
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $id_asignatura = $oActividadAsignatura->getIdAsignaturaVo()->value();
            if (empty($aAsigDatos[$id_asignatura])) {
                $aAsignaturasCa[$id_asignatura] = array('nombre_asignatura' => '??', 'creditos' => '??');
            } else {
                $aAsignaturasCa[$id_asignatura] = $aAsigDatos[$id_asignatura];
            }
        }
        return $aAsignaturasCa;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadAsignatura
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadAsignatura
     */
    public function getActividadAsignaturas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ActividadAsignaturaSet = new Set();
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
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $ActividadAsignatura = ActividadAsignatura::fromArray($aDatos);
            $ActividadAsignaturaSet->add($ActividadAsignatura);
        }
        return $ActividadAsignaturaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadAsignatura $ActividadAsignatura): bool
    {
        $id_activ = $ActividadAsignatura->getId_activ();
        $id_asignatura = $ActividadAsignatura->getIdAsignaturaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadAsignatura $ActividadAsignatura): bool
    {
        $id_activ = $ActividadAsignatura->getId_activ();
        $id_asignatura = $ActividadAsignatura->getIdAsignaturaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_asignatura);

        $aDatos = $ActividadAsignatura->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        /*
        $aDatos = [];
        $aDatos['id_profesor'] = $ActividadAsignatura->getId_profesor();
        $aDatos['avis_profesor'] = $ActividadAsignatura->getAvis_profesor();
        $aDatos['tipo'] = $ActividadAsignatura->getTipoActividadAsignaturaVo()->value();
        // para las fechas
        $aDatos['f_ini'] = (new ConverterDate('date', $ActividadAsignatura->getF_ini()))->toPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $ActividadAsignatura->getF_fin()))->toPg();
        array_walk($aDatos, 'core\poner_null');
        */

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_profesor              = :id_profesor,
					avis_profesor            = :avis_profesor,
					tipo                     = :tipo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_activ'] = $ActividadAsignatura->getId_activ();
            $aDatos['id_asignatura'] = $ActividadAsignatura->getIdAsignaturaVo()->value();
            $campos = "(id_activ,id_asignatura,id_profesor,avis_profesor,tipo,f_ini,f_fin)";
            $valores = "(:id_activ,:id_asignatura,:id_profesor,:avis_profesor,:tipo,:f_ini,:f_fin)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ, int $id_asignatura): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
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
    public function datosById(int $id_activ, int $id_asignatura): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ, int $id_asignatura): ?ActividadAsignatura
    {
        $aDatos = $this->datosById($id_activ, $id_asignatura);
        if (empty($aDatos)) {
            return null;
        }
        return ActividadAsignatura::fromArray($aDatos);
    }
}