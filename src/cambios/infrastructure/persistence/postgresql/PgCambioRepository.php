<?php

namespace src\cambios\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\ConverterJson;
use src\shared\infrastructure\persistence\postgresql\Set;
use DateInterval;
use DateTime;
use JsonException;
use PDO;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla av_cambios_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
class PgCambioRepository extends ClaseRepository implements CambioRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        $this->setoDbl_select(GlobalPdo::get('oDBPC_Select'));
        $this->setNomTabla('av_cambios');
    }

    public function getNomActivEliminada(int $iId): string
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ=$iId AND id_tipo_cambio=3");
        if ($qRs === false) {
            $sClauError = 'ActividadCambio.NomActivEliminada';
            /** @var \src\shared\infrastructure\logging\GestorErrores $oGestorErrores */
            $oGestorErrores = $_SESSION['oGestorErrores'];
            $oGestorErrores->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
        }
        $aDades = $qRs->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDades)) {
            return _('Actividad Eliminada');
        }
        $valorOld = $aDades['valor_old'] ?? '';
        $valorOldStr = is_string($valorOld) || is_numeric($valorOld) ? (string) $valorOld : '';

        return $valorOldStr . "(" . _('Eliminado') . ")";
    }

    /**
     *
     * Cambios: dl y public
     *   Si sólo hago los de dl, al final en public quedarán los de las dl que no tienen el módulo de cambios.
     *   Hay que borrar todo desde public. El primero que borre puede tener los ids para eliminar en las otras tablas,
     *   pero los que lo hagan a continuación no van a saber lo que se ha borrado.
     *
     * cambios_anotados(dl)
     *   Por lo dicho arriba, hay que borrar con un LEFT JOIN con los ids que no existan
     *
     * cambios_usuario(dl)
     *   idem.
     *
     * @param string $str_interval
     */
    public function borrarCambios(string $str_interval = 'P1Y'):void
    {
        $this->borrarCambiosP($str_interval);
        $this->borrarCambiosAnotados();
        $this->borrarCambiosUsuario();
    }

    /**
     * Elimina de la tabla usuario, los registros de cambios que ya se han eliminado
     */
    private function borrarCambiosUsuario(): bool
    {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios');

        $sQry = "DELETE FROM av_cambios_usuario u USING av_cambios_usuario a
                LEFT JOIN public.av_cambios c
                 ON (a.id_schema_cambio = c.id_schema AND a.id_item_cambio = c.id_item_cambio)
                LEFT JOIN av_cambios_dl d
                 ON (a.id_schema_cambio = d.id_schema AND a.id_item_cambio = d.id_item_cambio)
                WHERE u.id_item = a.id_item
                AND c.id_item_cambio IS NULL
                AND d.id_item_cambio IS NULL
                ";

        return $this->pdoExec($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Elimina de la tabla anotados, los registros de cambios que ya se han eliminado
     */
    private function borrarCambiosAnotados(): bool
    {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios');

        if (getenv('UBICACION') === 'sv') {
            $nom_tabla_anotados = 'av_cambios_anotados_dl';
        } else {
            $nom_tabla_anotados = 'av_cambios_anotados_dl_sf';
        }

        $sQry = "DELETE FROM $nom_tabla_anotados a2 USING $nom_tabla_anotados a
                LEFT JOIN public.av_cambios c
                 ON (a.id_schema_cambio = c.id_schema AND a.id_item_cambio = c.id_item_cambio)
                LEFT JOIN av_cambios_dl d
                 ON (a.id_schema_cambio = d.id_schema AND a.id_item_cambio = d.id_item_cambio)
                WHERE a2.id_item = a.id_item
                AND c.id_item_cambio IS NULL
                AND d.id_item_cambio IS NULL
                ";

        return $this->pdoExec($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Elimina els apunts amb un timestamp anterior en un interval
     */
    private function borrarCambiosP(string $str_interval = 'P1Y'): bool
    {
        $oDbl = GlobalPdo::get('oDBC');
        $nom_tabla = $this->getNomTabla();

        $interval = new DateInterval($str_interval);
        $oDateTime = new DateTime();
        $timestamp = $oDateTime->sub($interval)->format('Y-m-d 00:00:00');

        $sQry = "DELETE FROM public.$nom_tabla
                WHERE timestamp_cambio < '$timestamp'
                ";

        return $this->pdoExec($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * retorna l'array d'objectes de tipus Cambio
     * Que no s'hagin apuntat a la dl.
     *
     * @return list<Cambio>
     */
    public function getCambiosNuevos(): array
    {
        $oDbl = GlobalPdo::get('oDBC');

        if (getenv('UBICACION') === 'sv') {
            $nom_tabla_anotados = 'av_cambios_anotados_dl';
        } else {
            $nom_tabla_anotados = 'av_cambios_anotados_dl_sf';
        }

        // Unir los anotados en servidor 1 y en servidor 2:
        //select * from "H-dlb".av_cambios_anotados_dl UNION SELECT * from "H-dlb".av_cambios_anotados_dl_sf

        // Cuando av_cambios_anotados no tiene la fila, No podemos saber si es un cambio de la dl o no.
        // Vamos a hacer dos consultas separadas y unimos.

        $oCambioSet = new Set();
        /** @var array<string, true> $cambiosVistos */
        $cambiosVistos = [];
        // Cambios Dl (av_cambios_dl)
        $sQry = "SELECT c.id_schema, c.id_item_cambio, c.id_tipo_cambio, c.id_activ, c.id_tipo_activ,
                c.json_fases_sv, c.json_fases_sf, c.id_status, c.dl_org,
                c.objeto, c.propiedad, c.valor_old, c.valor_new, c.quien_cambia, c.sfsv_quien_cambia, c.timestamp_cambio
                FROM av_cambios_dl c LEFT JOIN $nom_tabla_anotados a
                ON (c.id_schema = a.id_schema_cambio AND c.id_item_cambio=a.id_item_cambio)
                WHERE a.anotado IS NULL OR a.anotado = 'f'
                ORDER BY dl_org,id_tipo_activ,timestamp_cambio
                ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        if ($stmt !== false) {
            foreach ($stmt as $aDatos) {
                if (!is_array($aDatos)) {
                    continue;
                }
                $oCambio = Cambio::fromArray($this->hydrateCambioRowFromPg($aDatos));
                $this->addCambioNuevoAlSet($oCambio, $oCambioSet, $cambiosVistos);
            }
        }

        // Cambios de otras dl visibles en public.av_cambios (herencia) pero aún no copiados en av_cambios_dl local.
        $sQry = "SELECT c.id_schema, c.id_item_cambio, c.id_tipo_cambio, c.id_activ, c.id_tipo_activ,
                c.json_fases_sv, c.json_fases_sf, c.id_status, c.dl_org,
                c.objeto, c.propiedad, c.valor_old, c.valor_new, c.quien_cambia, c.sfsv_quien_cambia, c.timestamp_cambio
                FROM public.av_cambios c LEFT JOIN $nom_tabla_anotados a
                ON (c.id_schema = a.id_schema_cambio AND c.id_item_cambio=a.id_item_cambio)
                LEFT JOIN av_cambios_dl local
                ON (c.id_schema = local.id_schema AND c.id_item_cambio = local.id_item_cambio)
                WHERE (a.anotado IS NULL OR a.anotado = 'f')
                AND local.id_item_cambio IS NULL
                ORDER BY dl_org,id_tipo_activ,timestamp_cambio
                ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        if ($stmt !== false) {
            foreach ($stmt as $aDatos) {
                if (!is_array($aDatos)) {
                    continue;
                }
                $oCambio = Cambio::fromArray($this->hydrateCambioRowFromPg($aDatos));
                $this->addCambioNuevoAlSet($oCambio, $oCambioSet, $cambiosVistos);
            }
        }

        /** @var list<Cambio> $result */

        $result = array_values($oCambioSet->getTot());

        return $result;
    }

    /**
     * @param array<string, true> $cambiosVistos
     */
    private function addCambioNuevoAlSet(Cambio $oCambio, Set $oCambioSet, array &$cambiosVistos): void
    {
        $key = $oCambio->getId_schema() . '_' . $oCambio->getId_item_cambio();
        if (isset($cambiosVistos[$key])) {
            return;
        }
        $cambiosVistos[$key] = true;
        $oCambioSet->add($oCambio);
    }


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Cambio>
     * @throws JsonException
     */
    public function getCambios(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CambioDlSet = new Set();
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
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos = $this->hydrateCambioRowFromPg($aDatos);
            $CambioDl = Cambio::fromArray($aDatos);
            $CambioDlSet->add($CambioDl);
        }
        /** @var list<Cambio> $result */
        $result = array_values($CambioDlSet->getTot());
        return $result;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cambio $Cambio): bool
    {
        $id_item_cambio = $Cambio->getId_item_cambio();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item_cambio = $id_item_cambio";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     * @throws JsonException
     */
    public function Guardar(Cambio $Cambio): bool
    {
        $id_item_cambio = $Cambio->getId_item_cambio();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item_cambio);

        $aDatos = $Cambio->toArrayForDatabase([
            'timestamp_cambio' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
            'json_fases_sv' => fn($v) => (new ConverterJson($v, false))->toPg(false),
            'json_fases_sf' => fn($v) => (new ConverterJson($v, false))->toPg(false),
        ]);

        /*
        $aDatos = [];
        $aDatos['id_tipo_cambio'] = $Cambio->getTipoCambioVo()->value();
        $aDatos['id_activ'] = $Cambio->getId_activ();
        $aDatos['id_tipo_activ'] = $Cambio->getIdTipoActivVo()->value();
        $aDatos['id_status'] = $Cambio->getId_status();
        $aDatos['dl_org'] = $Cambio->getDl_org();
        $aDatos['objeto'] = $Cambio->getObjeto();
        $aDatos['propiedad'] = $Cambio->getPropiedad();
        $aDatos['valor_old'] = $Cambio->getValor_old();
        $aDatos['valor_new'] = $Cambio->getValor_new();
        $aDatos['quien_cambia'] = $Cambio->getQuien_cambia();
        $aDatos['sfsv_quien_cambia'] = $Cambio->getSfsv_quien_cambia();
        // para las fechas
        $aDatos['timestamp_cambio'] = (new ConverterDate('timestamp', $Cambio->getTimestamp_cambio()))->toPg();
        // para los json
        $aDatos['json_fases_sv'] = (new ConverterJson($Cambio->getJson_fases_sv(),false))->toPg(false);
        $aDatos['json_fases_sf'] = (new ConverterJson($Cambio->getJson_fases_sf(), false))->toPg(false);
        array_walk($aDatos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item_cambio']);
            $update = "
					id_tipo_cambio           = :id_tipo_cambio,
					id_activ                 = :id_activ,
					id_tipo_activ            = :id_tipo_activ,
					json_fases_sv            = :json_fases_sv,
					json_fases_sf            = :json_fases_sf,
					id_status                = :id_status,
					dl_org                   = :dl_org,
					objeto                   = :objeto,
					propiedad                = :propiedad,
					valor_old                = :valor_old,
					valor_new                = :valor_new,
					quien_cambia             = :quien_cambia,
					sfsv_quien_cambia        = :sfsv_quien_cambia,
					timestamp_cambio         = :timestamp_cambio";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item_cambio = $id_item_cambio";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            /* uso el id_schema 3000, que no debería corresponder a ningun esquema (en todo caso a 'public')
             * Es para el caso de las dl que no tienen instalado el módulo de 'cambios'. Para distinguir los cambios
             * debo usar la dl_org. No uso el id_schema correspondiente, porque si más tarde instalan el módulo
             * 'cambios', puede haber conflicto con el id_item_cambio.
             */
            $mi_esquema = 3000;
            $campos = "(id_schema,id_item_cambio,id_tipo_cambio,id_activ,id_tipo_activ,json_fases_sv,json_fases_sf,id_status,dl_org,objeto,propiedad,valor_old,valor_new,quien_cambia,sfsv_quien_cambia,timestamp_cambio)";
            $valores = "($mi_esquema,:id_item_cambio,:id_tipo_cambio,:id_activ,:id_tipo_activ,:json_fases_sv,:json_fases_sf,:id_status,:dl_org,:objeto,:propiedad,:valor_old,:valor_new,:quien_cambia,:sfsv_quien_cambia,:timestamp_cambio)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item_cambio): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_cambio = $id_item_cambio";
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
     * @throws JsonException
     */
    public function datosById(int $id_item_cambio): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_cambio = $id_item_cambio";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        return $this->hydrateCambioRowFromPg($aDatos);
    }

    /**
     * Normaliza una fila PostgreSQL de `av_cambios*` antes de {@see Cambio::fromArray()}.
     *
     * @param array<string, mixed> $aDatos
     * @return array<string, mixed>
     */
    private function hydrateCambioRowFromPg(array $aDatos): array
    {
        $aDatos = $this->normalizeAssocRow($aDatos);
        $aDatos['timestamp_cambio'] = (new ConverterDate('timestamp', $aDatos['timestamp_cambio']))->fromPg();
        $aDatos['json_fases_sv'] = (new ConverterJson($this->jsonForConverter($aDatos['json_fases_sv']), true))->fromPg();
        $aDatos['json_fases_sf'] = (new ConverterJson($this->jsonForConverter($aDatos['json_fases_sf']), true))->fromPg();

        return $aDatos;
    }

    /**
     * @return array<int|string, mixed>|\stdClass|string|null
     */
    private function jsonForConverter(mixed $value): array|\stdClass|string|null
    {
        if (is_string($value) || is_array($value) || $value instanceof \stdClass) {
            return $value;
        }

        return null;
    }

    /**
     * @throws JsonException
     */
    public function findById(int $id_item_cambio): ?Cambio
    {
        $aDatos = $this->datosById($id_item_cambio);
        if ($aDatos === false) {
            return null;
        }
        return Cambio::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('av_cambios_id_item_cambio_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}