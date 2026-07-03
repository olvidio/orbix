<?php

namespace src\actividadestudios\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\value_objects\ActividadAsignaturaPk;
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

    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asignaturas_activ_all');
    }

    /**
     * @return array<int, array{nombre_asignatura: mixed, creditos: mixed}>
     */
    public function getAsignaturasCa(int $id_activ, string $tipo = ''): array
    {
        /**
         * Array con  id_asignatura => array(nombre_asignatura, creditos)
         * para no tener que consultar cada vez a la base de datos.
         *
         */
        $aAsigDatos = $this->asignaturaRepository->getArrayAsignaturasCreditos();

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

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ActividadAsignatura>
     */
    public function getActividadAsignaturas(array $aWhere = [], array $aOperators = []): array
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
        /** @var list<ActividadAsignatura> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            // para las fechas del postgres (texto iso)
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = ActividadAsignatura::fromArray($normalized);
        }
        return $items;
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
        array_walk($aDatos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            unset($aDatos['id_asignatura']);
            $update = "
					id_profesor              = :id_profesor,
					avis_profesor            = :avis_profesor,
					tipo                     = :tipo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_asignatura=$id_asignatura";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT (d_asignaturas_activ_all exige id_schema NOT NULL; Hydratable no serializa id_schema)
            $campos = "(id_schema,id_activ,id_asignatura,id_profesor,avis_profesor,tipo,f_ini,f_fin)";
            $valores = "(:id_schema,:id_activ,:id_asignatura,:id_profesor,:avis_profesor,:tipo,:f_ini,:f_fin)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($bInsert) {
            $sid = ConfigGlobal::mi_id_schema();
            $idSchema = $sid;
            if ($idSchema < 1) {
                throw new \RuntimeException(_('Falta id_schema de sesión (mi_id_schema) para persistir actividad-asignatura.'));
            }
            $aDatos['id_schema'] = $idSchema;
        }

        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ, int $id_asignatura): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
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
    public function datosById(int $id_activ, int $id_asignatura): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        // para las fechas del postgres (texto iso)
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(ActividadAsignaturaPk $pk): array|false
    {
        return $this->datosById($pk->IdActiv(), $pk->IdAsignatura());
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

    public function findByPk(ActividadAsignaturaPk $pk): ?ActividadAsignatura
    {
        return $this->findById($pk->IdActiv(), $pk->IdAsignatura());
    }
}