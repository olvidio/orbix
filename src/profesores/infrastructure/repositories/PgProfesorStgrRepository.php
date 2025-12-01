<?php

namespace src\profesores\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use personas\model\entity\GestorPersonaPub;
use personas\model\entity\PersonaDl;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorStgr;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_profesor_stgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class PgProfesorStgrRepository extends ClaseRepository implements ProfesorStgrRepositoryInterface
{

    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_profesor_stgr');
    }

    /**
     * @deprecated Usar getArrayProfesoresAsignaturaVo(AsignaturaId $id_asignatura)
     */
    public function getArrayProfesoresAsignatura($id_asignatura): array
    {
        $oAsignatura = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class)->findById($id_asignatura);
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
        }
        $id_sector = $oAsignatura->getId_sector();
        $SectorRepository = $GLOBALS['container']->get(SectorRepositoryInterface::class);
        $id_departamento = $SectorRepository->findById($id_sector)?->getIdDepartamentoVo()?->value();
        // Profesores departamento
        $aProfesoresDepartamento = $this->getArrayProfesoresDepartamento($id_departamento);
        //profesor ampliación
        $ProfesoresAmpliacionRepository = $GLOBALS['container']->get(ProfesorAmpliacionRepositoryInterface::class);
        $aProfesoresAmpliacion = $ProfesoresAmpliacionRepository->getArrayProfesoresAsignatura($id_asignatura);

        $Opciones['departamento'] = $aProfesoresDepartamento;
        $Opciones['ampliacion'] = $aProfesoresAmpliacion;

        return $Opciones;

    }

    public function getArrayProfesoresAsignaturaVo(AsignaturaId $id_asignatura): array
    {
        return $this->getArrayProfesoresAsignatura($id_asignatura->value());
    }

    /**
     * @deprecated Usar getArrayTodosProfesoresAsignaturaVo(AsignaturaId $id_asignatura)
     */
    public function getArrayTodosProfesoresAsignatura($id_asignatura): array
    {
        $oAsignatura = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class)->findById($id_asignatura);
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
        }
        $id_sector = $oAsignatura->getId_sector();
        $SectorRepository = $GLOBALS['container']->get(SectorRepositoryInterface::class);
        $id_departamento = $SectorRepository->findById($id_sector)?->getIdDepartamentoVo()?->value();
        // Profesores departamento
        $aProfesoresDepartamento = $this->getArrayProfesoresDepartamento($id_departamento);
        //profesor ampliación
        $ProfesoresAmpliacionRepository = $GLOBALS['container']->get(ProfesorAmpliacionRepositoryInterface::class);
        $aProfesoresAmpliacion = $ProfesoresAmpliacionRepository->getArrayProfesoresAsignatura($id_asignatura);

        return $aProfesoresDepartamento + array("----------") + $aProfesoresAmpliacion;
    }

    public function getArrayTodosProfesoresAsignaturaVo(AsignaturaId $id_asignatura): array
    {
        return $this->getArrayTodosProfesoresAsignatura($id_asignatura->value());
    }

    public function getListaProfesoresPub(): array
    {
        $gesPersonaPub = new GestorPersonaPub();
        $cPersonasPub = $gesPersonaPub->getPersonas(array('profesor_stgr' => 't'));

        $aProfesores = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        foreach ($cPersonasPub as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            // comprobar situación
            $situacion = $oPersona->getSituacion();
            if ($situacion !== 'A') {
                continue;
            }
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $aProfesores[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom);
            $aAp1[] = $oPersona->getApellido1();
            $aAp2[] = $oPersona->getApellido2();
            $aNom[] = $oPersona->getNom();
        }
        $multisort_args = [];
        $multisort_args[] = $aAp1;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aAp2;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aNom;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = &$aProfesores;   // finally add the source array, by reference
        call_user_func_array("array_multisort", $multisort_args);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $clave = $aClave['id_nom'];
            $val = $aClave['ap_nom'];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    public function getArrayProfesoresConDl(): array
    {
        $gesProfesores = $this->getProfesoresStgr(array('f_cese' => ''), array('f_cese' => 'IS NULL'));
        $aProfesores = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = new PersonaDl($id_nom);
            // comprobar situación
            $situacion = $oPersonaDl->getSituacion();
            if ($situacion !== 'A') {
                continue;
            }
            $ap_nom = $oPersonaDl->getPrefApellidosNombre();
            $dl = $oPersonaDl->getDl();
            $aProfesores[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom, 'dl' => $dl);
            $aAp1[] = $oPersonaDl->getApellido1();
            $aAp2[] = $oPersonaDl->getApellido2();
            $aNom[] = $oPersonaDl->getNom();
        }
        $multisort_args = [];
        $multisort_args[] = $aAp1;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aAp2;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aNom;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = &$aProfesores;   // finally add the source array, by reference
        call_user_func_array("array_multisort", $multisort_args);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $clave = $aClave['id_nom'];
            //$val=$aClave['ap_nom'];
            //$dl=$aClave['dl'];
            $aOpciones[$clave] = $aClave;
        }
        return $aOpciones;
    }

    public function getArrayProfesoresDl(): array
    {
        $gesProfesores = $this->getProfesoresStgr(array('f_cese' => ''), array('f_cese' => 'IS NULL'));
        $aProfesores = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = new PersonaDl($id_nom);
            // comprobar situación
            $situacion = $oPersonaDl->getSituacion();
            if ($situacion !== 'A') {
                continue;
            }
            $ap_nom = $oPersonaDl->getPrefApellidosNombre();
            $aProfesores[$id_nom] = $ap_nom;
        }
        uasort($aProfesores, 'core\strsinacentocmp');

        return $aProfesores;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ProfesorStgr
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ProfesorStgr
     */
    public function getProfesoresStgr(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ProfesorStgrSet = new Set();
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
            $aDatos['f_nombramiento'] = (new ConverterDate('date', $aDatos['f_nombramiento']))->fromPg();
            $aDatos['f_cese'] = (new ConverterDate('date', $aDatos['f_cese']))->fromPg();
            $ProfesorStgr = new ProfesorStgr();
            $ProfesorStgr->setAllAttributes($aDatos);
            $ProfesorStgrSet->add($ProfesorStgr);
        }
        return $ProfesorStgrSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorStgr $ProfesorStgr): bool
    {
        $id_item = $ProfesorStgr->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ProfesorStgr $ProfesorStgr): bool
    {
        $id_item = $ProfesorStgr->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = [];
        $aDatos['id_nom'] = $ProfesorStgr->getId_nom();
        $aDatos['id_departamento'] = $ProfesorStgr->getId_departamento();
        $aDatos['escrito_nombramiento'] = $ProfesorStgr->getEscrito_nombramiento();
        $aDatos['id_tipo_profesor'] = $ProfesorStgr->getId_tipo_profesor();
        $aDatos['escrito_cese'] = $ProfesorStgr->getEscrito_cese();
        // para las fechas
        $aDatos['f_nombramiento'] = (new ConverterDate('date', $ProfesorStgr->getF_nombramiento()))->toPg();
        $aDatos['f_cese'] = (new ConverterDate('date', $ProfesorStgr->getF_cese()))->toPg();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_nom                   = :id_nom,
					id_departamento          = :id_departamento,
					escrito_nombramiento     = :escrito_nombramiento,
					f_nombramiento           = :f_nombramiento,
					id_tipo_profesor         = :id_tipo_profesor,
					escrito_cese             = :escrito_cese,
					f_cese                   = :f_cese";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $aDatos['id_item'] = $ProfesorStgr->getId_item();
            $campos = "(id_item,id_nom,id_departamento,escrito_nombramiento,f_nombramiento,id_tipo_profesor,escrito_cese,f_cese)";
            $valores = "(:id_item,:id_nom,:id_departamento,:escrito_nombramiento,:f_nombramiento,:id_tipo_profesor,:escrito_cese,:f_cese)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_nombramiento'] = (new ConverterDate('date', $aDatos['f_nombramiento']))->fromPg();
            $aDatos['f_cese'] = (new ConverterDate('date', $aDatos['f_cese']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ProfesorStgr
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new ProfesorStgr())->setAllAttributes($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_profesor_stgr_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    private function getArrayProfesoresDepartamento($id_departamento): array
    {
        $gesProfesores = $this->getProfesoresStgr(array('id_departamento' => $id_departamento, 'f_cese' => ''), array('f_cese' => 'IS NULL'));
        $aProfesores = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = new PersonaDl($id_nom);
            // comprobar situación
            $situacion = $oPersonaDl->getSituacion();
            if ($situacion !== 'A') {
                continue;
            }
            $ap_nom = $oPersonaDl->getPrefApellidosNombre();
            $aProfesores[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom);
            $aAp1[] = $oPersonaDl->getApellido1();
            $aAp2[] = $oPersonaDl->getApellido2();
            $aNom[] = $oPersonaDl->getNom();
        }
        $multisort_args = [];
        $multisort_args[] = $aAp1;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aAp2;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aNom;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = &$aProfesores;   // finally add the source array, by reference
        call_user_func_array("array_multisort", $multisort_args);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $clave = $aClave['id_nom'];
            $val = $aClave['ap_nom'];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

}