<?php

namespace src\actividades\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use DateInterval;
use PDO;
use src\planning\domain\value_objects\PlanningStyle;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\shared\domain\value_objects\DateTimeLocal;
use src\permisos\domain\PermisosActividades;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\domain\events\EntidadModificada;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\actividades\domain\entity\TiposActividades;

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

    private TiposActividades $tiposActividades;

    public function __construct(TiposActividades $tiposActividades)
    {
        $this->tiposActividades = $tiposActividades;
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        $this->setoDbl_select(GlobalPdo::get('oDBPC_Select'));
        $this->setNomTabla('a_actividades_all');
    }

    protected function getNomTablaSelect(): string
    {
        return $this->getNomTabla();
    }


    /**
     * Devuelve un array con las actividades de una casa en un periodo.
     * Se modifican las fechas de inicio (si es anterior al periodo),
     *  para que empiece en el inicio del periodo
     * Se requiere del array $_SESSION['oPermActividades'] para saber si se tiene permisos para ver...
     *
     */
    public function actividadesDeUnaCasa(int $id_ubi, DateTimeLocal $oFini, DateTimeLocal $oFfin, int $cdc_sel = 0): array
    {
        $oIniPlanning = $oFini;
        $a = 0;
        $a_cdc = [];
        $aWhere = [];
        $aOperador = [];
        if (empty($id_ubi) || $id_ubi === 1) { // en estos casos sólo miro las actividades de cada sección.
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
            if (!($oF_ini_act instanceof DateTimeLocal)) {
                $a++;
                continue;
            }
            $h_ini = $oActividad->getH_ini();
            $oF_fin_act = $oActividad->getF_fin();
            if (!($oF_fin_act instanceof DateTimeLocal)) {
                $a++;
                continue;
            }
            $h_fin = $oActividad->getH_fin();
            $dl_org = $oActividad->getDl_org();
            $nom_activ = $oActividad->getNom_activ();
            $css = PlanningStyle::clase($id_tipo_activ, '', '', $oActividad->getStatus());

            $oTipoActiv = new $this->tiposActividades($id_tipo_activ);
            $ssfsv = $oTipoActiv->getSfsvText();

            //para el caso de que la actividad comience antes
            //del periodo de inicio obligo a que tome una hora de inicio
            //en el entorno de las primeras del día (a efectos del planning
            //ya es suficiente con la 1:16 de la madrugada)
            if ($oIniPlanning > $oF_ini_act) {
                $ini = $oFini->getFromLocal();
                $hini = "1:16";
            } else {
                $ini = (string)$oF_ini_act->getFromLocal();
                $hini = (string)$h_ini?->format('H:i');
            }
            $fi = (string)$oF_fin_act->getFromLocal();
            $hfi = (string)$h_fin?->format('H:i');

            // mirar permisos.
            $oPermSesion = $_SESSION['oPermActividades'] ?? null;
            if (!($oPermSesion instanceof PermisosActividades)) {
                $a++;
                continue;
            }
            $oPermSesion->setActividad($id_activ, (string) $id_tipo_activ, $dl_org);
            $oPermActiv = $oPermSesion->getPermisoActual('datos');

            if ($oPermActiv->have_perm_activ('ocupado') === false) {
                $a++;
                continue;
            } // no tiene permisos ni para ver.
            if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                $nom_curt = $ssfsv;
                $nom_llarg = "$ssfsv ($ini-$fi)";
            } else {
                $nom_curt = $oTipoActiv->getAsistentesText() . " " . $oTipoActiv->getActividadText();
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
        return [];
    }

    /**
     * retorna si hi ha una activitat coincident en dates de l'altre secció.
     */
    public function getCoincidencia(ActividadAll $oActividad): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTablaSelect();

        $iTolerancia = 1;
        $interval = "P$iTolerancia" . "D";
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $id = (string)$id_tipo_activ; // para convertir id_tipo_activ en un string.
        $seccion = ($id[0] === "1") ? 2 : 1;
        $oFini0 = $oActividad->getF_ini();
        $oFfin0 = $oActividad->getF_fin();
        if (!($oFini0 instanceof DateTimeLocal) || !($oFfin0 instanceof DateTimeLocal)) {
            return false;
        }
        $oFini1 = clone $oFini0;
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
        if ($stmt === false) {
            return false;
        }

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * retorna l'array amb el id_ubi de les activitats sel·leccionades
     *
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<int|null>
     */
    public function getUbis(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTablaSelect();
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondi[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi !== '') $sCondi = " WHERE " . $sCondi;
        $sLimit = $this->getLimitPaginador("$nom_tabla", $sCondi, $aWhere);
        $sOrdre = '';
        $ordreUbis = $aWhere['_ordre'] ?? null;
        if (is_string($ordreUbis) && $ordreUbis !== '') {
            $sOrdre = ' ORDER BY ' . $ordreUbis;
        }
        unset($aWhere['_ordre']);
        $sQry = "SELECT id_ubi FROM $nom_tabla " . $sCondi . " GROUP BY id_ubi" . $sOrdre;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aUbis = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades)) {
                continue;
            }
            $idUbi = $aDades['id_ubi'] ?? null;
            $aUbis[] = is_numeric($idUbi) ? (int) $idUbi : null;
        }
        return $aUbis;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayActividadesDeTipo(string $sid_tipo = '......', string $scondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTablaSelect();
        $sQuery = "SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE id_tipo_activ::text ~ '" . $sid_tipo . "' $scondicion
		   ORDER by f_ini";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0] ?? null;
            $val = $aClave[1] ?? '';
            if (is_int($clave) || is_string($clave)) {
                $aOpciones[$clave] = is_scalar($val) ? (string) $val : '';
            }
        }
        return $aOpciones;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayActividadesEstudios(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTablaSelect();

        $cond_nivel_stgr = "(nivel_stgr < 6 OR nivel_stgr=11)";
        $oConfig = $_SESSION['oConfig'] ?? null;
        $any_final = is_object($oConfig) && method_exists($oConfig, 'any_final_curs')
            ? (int) $oConfig->any_final_curs('est')
            : (int) date('Y');
        $any = $any_final - 2;
        $inicurs = \src\shared\domain\helpers\FuncTablasSupport::cursoEst("inicio", $any, "est")->format('Y-m-d');
        $scondicion = "AND f_ini > '$inicurs'";
        $sQuery = "SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE " . $cond_nivel_stgr . " $scondicion
		   ORDER by f_ini";
        $stmt = $this->prepareAndExecute($oDbl, $sQuery, [], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0] ?? null;
            $val = $aClave[1] ?? '';
            if (is_int($clave) || is_string($clave)) {
                $aOpciones[$clave] = is_scalar($val) ? (string) $val : '';
            }
        }
        return $aOpciones;
    }

    /**
     * retorna l'array de id d'Actividad
     *
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return array<string, int>
     */
    public function getArrayIdsWithKeyFini(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTablaSelect();

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
        $sLimit = $this->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        $sOrdre = '';
        $ordreIds = $aWhere['_ordre'] ?? null;
        if (is_string($ordreIds) && $ordreIds !== '') {
            $sOrdre = ' ORDER BY ' . $ordreIds;
        }
        unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla" . $sCondi . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $i = 0;
        $aListaId = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades)) {
                continue;
            }
            $i++;
            $fIni = $aDades['f_ini'] ?? '';
            $f_ini_iso = (is_scalar($fIni) ? (string) $fIni : '') . '#' . $i;
            $idActiv = $aDades['id_activ'] ?? 0;
            $aListaId[$f_ini_iso] = is_numeric($idActiv) ? (int) $idActiv : 0;
        }
        return $aListaId;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ActividadAll>
     */
    public function getActividades(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTablaSelect();
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
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
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
        /** @var list<ActividadAll> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            // para las fechas del postgres (texto iso)
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['h_ini'] = (new ConverterDate('time', $aDatos['h_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $aDatos['h_fin'] = (new ConverterDate('time', $aDatos['h_fin']))->fromPg();
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = ActividadAll::fromArray($normalized);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadAll $ActividadAll, bool $registrarCambios = true): bool
    {
        $id_activ = $ActividadAll->getId_activ();
        $datosActuales = [];
        if ($registrarCambios) {
            $raw = $this->datosById($id_activ);
            $datosActuales = is_array($raw) ? $raw : [];
        }

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ";
        $success = $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($registrarCambios && $success && $datosActuales !== []) {
            $this->dispatchCambioActividad('DELETE', $ActividadAll, [], $datosActuales);
        }

        return $success;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadAll $ActividadAll, bool $registrarCambios = true): bool
    {
        $id_activ = $ActividadAll->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ);

        $datosActuales = [];
        if ($registrarCambios && !$bInsert) {
            $raw = $this->datosById($id_activ);
            $datosActuales = is_array($raw) ? $raw : [];
        }

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
        if ($stmt === false) {
            return false;
        }
        $success = $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);

        if ($registrarCambios && $success) {
            $datosNuevos = $this->datosById($id_activ);
            if (is_array($datosNuevos)) {
                if ($bInsert) {
                    $this->dispatchCambioActividad('INSERT', $ActividadAll, $datosNuevos, []);
                } else {
                    $this->dispatchCambioActividad('UPDATE', $ActividadAll, $datosNuevos, $datosActuales);
                }
            }
        }

        return $success;
    }

    protected function getObjetoCambio(): string
    {
        return match ($this->getNomTabla()) {
            'a_actividades_dl' => 'ActividadDl',
            'a_actividades_ex' => 'ActividadEx',
            default => 'Actividad',
        };
    }

    /**
     * @param array<string, mixed> $datosNuevos
     * @param array<string, mixed> $datosActuales
     */
    private function dispatchCambioActividad(
        string $tipoCambio,
        ActividadAll $actividad,
        array $datosNuevos,
        array $datosActuales,
    ): void {
        /** @var EventBusInterface $eventBus */
        $eventBus = DependencyResolver::get(EventBusInterface::class);
        $eventBus->dispatch(new EntidadModificada(
            objeto: $this->getObjetoCambio(),
            tipoCambio: $tipoCambio,
            idActiv: $actividad->getId_activ(),
            datosNuevos: $datosNuevos,
            datosActuales: $datosActuales,
        ));
    }

    private function isNew(int $id_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
    public function datosById(int $id_activ): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTablaSelect();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
        $aDatos['h_ini'] = (new ConverterDate('time', $aDatos['h_ini']))->fromPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
        $aDatos['h_fin'] = (new ConverterDate('time', $aDatos['h_fin']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ): ?ActividadAll
    {
        $aDatos = $this->datosById($id_activ);
        if ($aDatos === false) {
            return null;
        }
        return ActividadAll::fromArray($aDatos);
    }

    /**
     * @param array<string, mixed> $aWhere
     */
    private function getLimitPaginador(string $nomTabla, string $sCondi, array $aWhere): string
    {
        $oGestor = $GLOBALS['oGestorSessioDelegación'] ?? null;
        if (is_object($oGestor) && method_exists($oGestor, 'getLimitPaginador')) {
            return (string) $oGestor->getLimitPaginador($nomTabla, $sCondi, $aWhere);
        }

        return '';
    }
}