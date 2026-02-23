<?php

namespace src\actividadcargos\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\ConverterDate;
use core\Set;
use PDO;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\traits\DispatchesDomainEvents;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;


/**
 * Clase que adapta la tabla d_cargos_activ_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/12/2025
 */
class PgActividadCargoDlRepository extends ClaseRepository implements ActividadCargoRepositoryInterface
{
    use HandlesPdoErrors;
    use DispatchesDomainEvents;

    protected UnitOfWorkInterface $unitOfWork;

    public function __construct(UnitOfWorkInterface $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_cargos_activ_dl');
    }

    /**
     * retorna l'array de id_nom dels sacd que atenen l'activitat
     *
     */
    public function getActividadIdSacds(int $iid_activ): array
    {
        // Los sacd los pongo en la base de datos comun.
        $oDbl = $GLOBALS['oDBC_Select'];
        $nom_tabla = 'c' . $this->getNomTabla();

        // valores del id_cargo de tipo_cargo = sacd:
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $sQuery = "SELECT id_nom, id_cargo
				FROM $nom_tabla
				WHERE id_activ= $iid_activ  AND id_cargo IN ($txt_where_cargos)
				ORDER BY id_cargo";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aLista = [];
        foreach ($stmt as $aDades) {
            $aLista[] = $aDades['id_nom'];
        }
        return $aLista;
    }

    /**
     * retorna l'array d'objectes de tipus Persona
     *
     */
    public function getActividadSacds(int $iid_activ): array
    {
        // Los sacd los pongo en la base de datos comun.
        $oDbl = $GLOBALS['oDBC_Select'];
        $nom_tabla = 'c' . $this->getNomTabla();
        $oPersonaSet = new Set();

        // valores del id_cargo de tipo_cargo = sacd:
        $CargoREpository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoREpository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $sQuery = "SELECT id_nom, id_cargo
				FROM $nom_tabla
				WHERE id_activ = $iid_activ AND id_cargo IN ($txt_where_cargos)
				ORDER BY id_cargo";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        foreach ($stmt as $aDades) {
            $id_nom = $aDades['id_nom'];
            $oPersona = $PersonaSacdRepository->findById($id_nom);
            if ($oPersona === null) {
                // si estoy dentro y soy sv, puedo mirar la tabla correcta:
                if (ConfigGlobal::is_dmz() === FALSE && ConfigGlobal::mi_sfsv() === 1) {
                    $oPersona = Persona::findPersonaEnGlobal($id_nom);
                    if ($oPersona === null) {
                        continue;
                    }
                    $oPersonaSet->add($oPersona);
                } else {
                    // Si es de otra dl, ya es lo que toca: No tengo acceso a la tablas de cp_sacd.
                    // Desde dentro accedo a PersonaIn, pero desde fuera NO.
                    // nom actividad:
                    $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
                    $oActividad = $ActividadAllRepository->findById($iid_activ);
                    $nom_activ = $oActividad->getNom_activ();
                    $msg = sprintf(_("No se tiene acceso al nombre de (es de otra dl o el sacd no está en DB-comun) id_nom: %s"), $id_nom);
                    $msg .= '<br>';
                    $msg .= sprintf(_("afecta a la actividad: %s"), $nom_activ);
                    $msg .= '<br>';
                    echo $msg;
                }
            } else {
                $oPersonaSet->add($oPersona);
            }
        }
        return $oPersonaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ActividadCargo
     *
     */
    public function getActividadCargosDeAsistente(array $aWhereNom, $aWhere = [], $aOperators = []): array
    {
        // seleccionar las actividades según los criterios de búsqueda.
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $aListaIds = $ActividadAllRepository->getArrayIdsWithKeyFini($aWhere, $aOperators);

        $cCargos = $this->getActividadCargos($aWhereNom);
        // descarto los que no están.
        $cCargosOk = [];
        $i = 0;
        foreach ($cCargos as $oActividadCargo) {
            $id_activ = $oActividadCargo->getId_activ();
            if (in_array($id_activ, $aListaIds)) {
                $i++;
                $oActividad = $ActividadAllRepository->findById($id_activ);
                $oF_ini = $oActividad->getF_ini();
                $f_ini_iso = $oF_ini->format('Y-m-d') . '#' . $i; // Añado $i por si empiezan el mismo dia.
                $cCargosOk[$f_ini_iso] = $oActividadCargo;
            }
        }
        ksort($cCargosOk);

        return $cCargosOk;
    }


    /**
     * retorna un array amb els asistents i el carrec (si el té):
     *        $aAsis[$id_activ] = array('id_activ','id_nom','propio','id_cargo');
     *
     * @param array $aWhere para la asistencia (id_nom y plaza)
     * @param array $aOperador para la asistencia (id_nom y plaza)
     * @param array $aWhereAct para la Actividad
     * @param array $aOperadorAct para la Actividad
     * @return array|false
     */
    public function getAsistenteCargoDeActividad(array $aWhere, $aOperador = [], $aWhereAct = [], $aOperadorAct = []): array|false
    {

        if (empty($aWhere['id_nom'])) {
            return FALSE;
        }
        $id_nom = $aWhere['id_nom'];

        $service = $GLOBALS['container']->get(AsistenteActividadService::class);
        $cAsistentes = $service->getActividadesDeAsistente($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

        $cCargos = $this->getActividadCargos(array('id_nom' => $id_nom));
        // seleccionar las actividades según los criterios de búsqueda.
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $aListaIds = $ActividadRepository->getArrayIdsWithKeyFini($aWhereAct, $aOperadorAct);
        // descarto los que no estan.
        $cActividadesOk = [];
        foreach ($cCargos as $oCargo) {
            $id_activ = $oCargo->getId_activ();
            if (in_array($id_activ, $aListaIds)) {
                $cActividadesOk[$id_activ] = $oCargo;
            }
        }
        // lista de id_activ ordenados.
        $aAsis = [];
        foreach ($cAsistentes as $f_ini_iso => $oAsistente) {
            $id_activ = $oAsistente->getId_activ();
            $propio = $oAsistente->isPropio();
            $plaza = $oAsistente->getPlazaVo()->value();
            $aAsis[$id_activ] = ['id_activ' => $id_activ,
                'id_nom' => $id_nom,
                'propio' => $propio,
                'plaza' => $plaza,
            ];
        }
        // Añado los cargos
        foreach ($cActividadesOk as $id_activ => $oCargo) {
            $oCargo = $cActividadesOk[$id_activ];
            $id_cargo = $oCargo->getId_cargo();
            if (array_key_exists($id_activ, $aAsis)) {
                // Añado al primero el id_cargo del segundo.
                $aAsis[$id_activ]['id_cargo'] = $id_cargo;
            } else {
                // añado la actividad
                $aAsis[$id_activ] = ['id_activ' => $id_activ,
                    'id_nom' => $id_nom,
                    'propio' => 'f',
                    'id_cargo' => $id_cargo,
                    'plaza' => 0,
                ];
            }
        }
        return $aAsis;
    }

    /**
     * retorna un array amb els carrecs (perque sigui compatible amb: getAsistenteCargoDeActividad).
     *       $aAsis[$id_activ] = array('id_activ','id_nom','propio','id_cargo');
     *
     * @param array $aWhere para la asistencia (id_nom y plaza)
     * @param array $aOperador para la asistencia (id_nom y plaza)
     * @param array $aWhereAct para la Actividad
     * @param array $aOperadorAct para la Actividad
     * @return array|false
     */
    public function getCargoDeActividad(array $aWhere, $aOperador = [], $aWhereAct = [], $aOperadorAct = []): array|false
    {

        if (empty($aWhere['id_nom'])) {
            return FALSE;
        }
        $id_nom = $aWhere['id_nom'];

        $cCargos = $this->getActividadCargos(array('id_nom' => $id_nom));
        // seleccionar las actividades segun los criterios de búsqueda.
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $aListaIds = $ActividadRepository->getArrayIdsWithKeyFini($aWhereAct, $aOperadorAct);
        // descarto los que no están.
        $cActividadesOk = [];
        foreach ($cCargos as $oCargo) {
            $id_activ = $oCargo->getId_activ();
            if (in_array($id_activ, $aListaIds)) {
                $cActividadesOk[$id_activ] = $oCargo;
            }
        }
        // lista de id_activ ordenados.
        $aAsis = [];
        foreach ($cActividadesOk as $id_activ => $oCargo) {
            $oCargo = $cActividadesOk[$id_activ];
            $id_cargo = $oCargo->getId_cargo();
            if (array_key_exists($id_activ, $aAsis)) {
                // Añado al primero el id_cargo del segundo.
                $aAsis[$id_activ]['id_cargo'] = $id_cargo;
            } else {
                // añado la actividad
                $aAsis[$id_activ] = ['id_activ' => $id_activ,
                    'id_nom' => $id_nom,
                    'propio' => 'f',
                    'id_cargo' => $id_cargo,
                    'plaza' => 0,
                ];
            }
        }
        return $aAsis;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadCargo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadCargo
     */
    public function getActividadCargos(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ActividadCargoSet = new Set();
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
            $ActividadCargo = ActividadCargo::fromArray($aDatos);
            $ActividadCargoSet->add($ActividadCargo);
        }
        return $ActividadCargoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadCargo $ActividadCargo): bool
    {
        $id_item = $ActividadCargo->getId_item();

        // Obtener datos actuales antes de eliminar
        $datosActuales = $this->datosById($id_item);

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        $success = $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($success && $datosActuales) {
            // Marcar como eliminada y despachar eventos
            $this->markAsDeleted($ActividadCargo, $datosActuales);
        }

        return $success;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadCargo $ActividadCargo): bool
    {
        $id_item = $ActividadCargo->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        // Obtener datos actuales si es UPDATE
        $datosActuales = $bInsert ? [] : $this->datosById($id_item)?? [];

        $aDatos = $ActividadCargo->toArrayForDatabase();
        unset($aDatos['domainEvents']);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_activ                 = :id_activ,
					id_cargo                 = :id_cargo,
					id_nom                   = :id_nom,
					puede_agd                = :puede_agd,
					observ                   = :observ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_activ,id_cargo,id_nom,puede_agd,observ,id_item)";
            $valores = "(:id_activ,:id_cargo,:id_nom,:puede_agd,:observ,:id_item)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        $success =  $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);

        if ($success) {
            // Marcar evento de dominio (se despachará por el UnitOfWork)
            if ($bInsert) {
                $this->markAsNew($ActividadCargo, $datosActuales);
            } else {
                $this->markAsModified($ActividadCargo, $datosActuales);
            }
        }

        return $success;
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_item): ?ActividadCargo
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return ActividadCargo::fromArray($aDatos);

    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_cargos_activ_dl_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

}