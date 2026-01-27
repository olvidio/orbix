<?php

namespace src\actividades\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use DateInterval;
use PDO;
use planning\domain\PlanningStyle;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use function core\curso_est;


/**
 * Clase que adapta la tabla a_actividades_all a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class PgActividadAllRepository extends ClaseRepository implements ActividadAllRepositoryInterface
{
    use HandlesPdoErrors;

    private TipoTelecoRepositoryInterface $tipoTelecoRepository;

    public function __construct(TipoTelecoRepositoryInterface $tipoTelecoRepository)
    {
        $this->tipoTelecoRepository = $tipoTelecoRepository;
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_actividades_all');
    }


    /**
     * Devuelve un array con las actividades de una casa en un periodo.
     * Se modifican las fechas de inicio (si es anterior al periodo),
     *  para que empiece en el inicio del periodo
     * Se requiere del array $_SESSION['oPermActividades'] para saber si se tiene permisos para ver...
     *
     */
    public function actividadesDeUnaCasa(int $id_ubi, DateTimeLocal $oFini, DateTimeLocal $oFfin, $cdc_sel = 0): array|false
    {
        $oIniPlanning = $oFini;
        $a = 0;
        $a_cdc = [];
        $aWhere = [];
        $aOperador = [];
        if (empty($id_ubi) || $id_ubi == 1) { // en estos casos sólo miro las actividades de cada sección.
            if (empty($id_ubi)) {
                $aOperador['id_ubi'] = 'IS NULL';
            }
            switch ($cdc_sel) {
                case 11:
                    $aWhere['id_tipo_activ'] = '^1';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
                case 12:
                    $aWhere['id_tipo_activ'] = '^2';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
            }
        }
        $aWhere['f_ini'] = "'" . $oFfin->format('Y-m-d') . "'";
        $aOperador['f_ini'] = '<=';
        $aWhere['f_fin'] = "'" . $oFini->format('Y-m-d') . "'";
        $aOperador['f_fin'] = '>=';
        $aWhere['id_ubi'] = $id_ubi;
        $aWhere['status'] = 4;
        $aOperador['status'] = '<';
        $oActividades = $this->getActividades($aWhere, $aOperador);
        foreach ($oActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $oF_ini_act = $oActividad->getF_ini();
            $h_ini = $oActividad->getH_ini();
            $oF_fin_act = $oActividad->getF_fin();
            $h_fin = $oActividad->getH_fin();
            $dl_org = $oActividad->getDl_org();
            $nom_activ = $oActividad->getNom_activ();
            $css = PlanningStyle::clase($id_tipo_activ, '', '', $oActividad->getStatus());

            $oTipoActividad = $this->tipoTelecoRepository->findById($id_tipo_activ);
            $ssfsv = $oTipoActividad->getSfsvText();

            //para el caso de que la actividad comience antes
            //del periodo de inicio obligo a que tome una hora de inicio
            //en el entorno de las primeras del día (a efectos del planning
            //ya es suficiente con la 1:16 de la madrugada)
            if ($oIniPlanning > $oF_ini_act) {
                $ini = $oFini->getFromLocal();
                $hini = "1:16";
            } else {
                $ini = (string)$oF_ini_act->getFromLocal();
                $hini = (string)$h_ini;
            }
            $fi = (string)$oF_fin_act->getFromLocal();
            $hfi = (string)$h_fin;

            // mirar permisos.
            $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
            $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

            if ($oPermActiv->have_perm_activ('ocupado') === false) {
                $a++;
                continue;
            } // no tiene permisos ni para ver.
            if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                $nom_curt = $ssfsv;
                $nom_llarg = "$ssfsv ($ini-$fi)";
            } else {
                $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                $nom_llarg = $nom_activ;
            }

            if ($oPermActiv->have_perm_activ('modificar')) { // puede modificar
                // en realidad creo que simplemente tiene que haber algo. Activa la función de javascript: cambiar_activ.
                $pagina = 'programas/actividad_ver.php';
            } else {
                $pagina = '';
            }

            $a_cdc[] = array(
                'nom_curt' => $nom_curt,
                'nom_llarg' => $nom_llarg,
                'f_ini' => $ini,
                'h_ini' => $hini,
                'f_fi' => $fi,
                'h_fi' => $hfi,
                'id_tipo_activ' => $id_tipo_activ,
                'pagina' => $pagina,
                'id_activ' => $id_activ,
                'css' => $css,
            );
            $a++;
        }
        // En caso de que todas=0, si no hay actividad, no pongo la casa
        if ($a > 0) {
            return $a_cdc;
        }

        return false;
    }

    /**
     * retorna si hi ha una activitat coincident en dates de l'altre secció.
     */
    public function getCoincidencia($oActividad): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $iTolerancia = 1;
        $interval = "P$iTolerancia" . "D";
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $id = (string)$id_tipo_activ; // para convertir id_tipo_activ en un string.
        $seccion = ($id[0] == "1") ? 2 : 1;
        $oFini0 = $oActividad->getF_ini();
        $oFini1 = clone $oFini0;
        $oFfin0 = $oActividad->getF_fin();
        $oFfin1 = clone $oFfin0;
        $oFini0->sub(new DateInterval($interval));
        $oFini1->add(new DateInterval($interval));
        $oFfin0->sub(new DateInterval($interval));
        $oFfin1->add(new DateInterval($interval));
        $sql_ini = "f_ini between '" . $oFini0->format('Y-m-d') . "' and '" . $oFini1->format('Y-m-d') . "'";
        $sql_fin = "f_fin between '" . $oFfin0->format('Y-m-d') . "' and '" . $oFfin1->format('Y-m-d') . "'";

        $sql = "SELECT count(*)";
        $sql .= " FROM $nom_tabla";
        $sql .= " WHERE id_tipo_activ::text ~ '^" . $seccion . "[45]' ";
        $sql .= " AND $sql_ini";
        $sql .= " AND $sql_fin";

        //echo "sql: $sql<br>";
        $stmt = $this->prepareAndExecute($oDbl, $sql, [], __METHOD__, __FILE__, __LINE__);

        return ($stmt->fetchColumn() > 0) ? true : false;
    }

    /**
     * retorna l'array amb el id_ubi de les activitats sel·leccionades
     *
     */
    public function getUbis($aWhere = [], $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') $sCondi = " WHERE " . $sCondi;
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador("$nom_tabla", $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT id_ubi FROM $nom_tabla " . $sCondi . " GROUP BY id_ubi" . $sOrdre;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $aUbis = [];
        foreach ($stmt as $aDades) {
            $aUbis[] = $aDades['id_ubi'];
        }
        return $aUbis;
    }

    public function getArrayActividadesDeTipo($sid_tipo = '......', $scondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE id_tipo_activ::text ~ '" . $sid_tipo . "' $scondicion
		   ORDER by f_ini";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    public function getArrayActividadesEstudios(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $cond_nivel_stgr = "(nivel_stgr < 6 OR nivel_stgr=11)";
        $any_final = $_SESSION['oConfig']?->any_final_curs('est')?? date('Y');
        $any = $any_final - 2;
        $inicurs = curso_est("inicio", $any, "est")->format('Y-m-d');
        $scondicion = "AND f_ini > '$inicurs'";
        $sQuery = "SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE " . $cond_nivel_stgr . " $scondicion
		   ORDER by f_ini";
        $stmt = $this->prepareAndExecute($oDbl, $sQuery, [], __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    /**
     * retorna l'array de id d'Actividad
     *
     */
    public function getArrayIdsWithKeyFini($aWhere = [], $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') $sCondi = " WHERE " . $sCondi;
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla" . $sCondi . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $i = 0;
        $aListaId = [];
        foreach ($stmt as $aDades) {
            $i++;
            $f_ini_iso = $aDades['f_ini'] . '#' . $i; // Añado $i por si empiezan el mismo dia.
            $aListaId[$f_ini_iso] = $aDades['id_activ'];
        }
        return $aListaId;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadAll
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadAll
     */
    public function getActividades(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ActividadAllSet = new Set();
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
            $aDatos['h_ini'] = (new ConverterDate('time', $aDatos['h_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $aDatos['h_fin'] = (new ConverterDate('time', $aDatos['h_fin']))->fromPg();
            // Usa el método fromArray() del trait Hydratable
            $ActividadAll = ActividadAll::fromArray($aDatos);
            $ActividadAllSet->add($ActividadAll);
        }
        return $ActividadAllSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadAll $ActividadAll): bool
    {
        $id_activ = $ActividadAll->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadAll $ActividadAll): bool
    {
        $id_activ = $ActividadAll->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ);

        $aDatos = $ActividadAll->toArrayForDatabase([
            'h_ini' => fn($v) => (new ConverterDate('time', $v))->toPg(),
            'h_fin' => fn($v) => (new ConverterDate('time', $v))->toPg(),
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_auto']);
            unset($aDatos['id_activ']);
            $update = "
					id_tipo_activ            = :id_tipo_activ,
					dl_org                   = :dl_org,
					nom_activ                = :nom_activ,
					id_ubi                   = :id_ubi,
					desc_activ               = :desc_activ,
					f_ini                    = :f_ini,
					h_ini                    = :h_ini,
					f_fin                    = :f_fin,
					h_fin                    = :h_fin,
					tipo_horario             = :tipo_horario,
					precio                   = :precio,
					num_asistentes           = :num_asistentes,
					status                   = :status,
					observ                   = :observ,
					nivel_stgr               = :nivel_stgr,
					observ_material          = :observ_material,
					lugar_esp                = :lugar_esp,
					tarifa                   = :tarifa,
					id_repeticion            = :id_repeticion,
					publicado                = :publicado,
					id_tabla                 = :id_tabla,
					plazas                   = :plazas,
                    idioma                   = :idioma";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_activ,id_tipo_activ,dl_org,nom_activ,id_ubi,desc_activ,f_ini,h_ini,f_fin,h_fin,tipo_horario,precio,num_asistentes,status,observ,nivel_stgr,observ_material,lugar_esp,tarifa,id_repeticion,publicado,id_tabla,plazas,idioma)";
            $valores = "(:id_activ,:id_tipo_activ,:dl_org,:nom_activ,:id_ubi,:desc_activ,:f_ini,:h_ini,:f_fin,:h_fin,:tipo_horario,:precio,:num_asistentes,:status,:observ,:nivel_stgr,:observ_material,:lugar_esp,:tarifa,:id_repeticion,:publicado,:id_tabla,:plazas,:idioma)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
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
    public function datosById(int $id_activ): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['h_ini'] = (new ConverterDate('time', $aDatos['h_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $aDatos['h_fin'] = (new ConverterDate('time', $aDatos['h_fin']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ): ?ActividadAll
    {
        $aDatos = $this->datosById($id_activ);
        if (empty($aDatos)) {
            return null;
        }
        // Usa el método fromArray() del trait Hydratable (más limpio)
        return ActividadAll::fromArray($aDatos);
    }
}