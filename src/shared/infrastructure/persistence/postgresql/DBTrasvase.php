<?php

namespace src\shared\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;
use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\domain\entity\DBAbstract;
use src\utils_database\domain\entity\MapId;
use src\utils_database\domain\GenerateIdGlobal;
use src\utils_database\domain\value_objects\MapIdDl;
use src\utils_database\domain\value_objects\MapIdResto;
use function src\shared\domain\helpers\is_true;

class DBTrasvase extends DBAbstract
{

    private string $sdbname;
    private string $sregion;
    private string $sdir;
    private string $sdl;
    private string $sEsquema;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private \PDO $oDbResto;
    private string $serror;

    /** @var list<string> */
    private array $avisosConexion = [];

    function __construct()
    {
        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        if (!empty($esquema_sfsv)) {
            $this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        }
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setDbName($dbname)
    {
        $this->sdbname = $dbname;

        $oDbl = $this->getConexionPDO();
        $this->setoDbl($oDbl);
    }

    public function getDbName()
    {
        return $this->sdbname;
    }

    private function nombreBaseConfigDb(): string
    {
        return match ($this->getDbName()) {
            'comun' => 'comun',
            'sv', 'sv-e' => 'sv',
            'sf', 'sf-e' => 'sf',
            default => 'importar',
        };
    }

    private function getConfigConexion($esquema = '')
    {
        if (empty($esquema)) {
            $esquema = $this->getEsquema();
        }

        $config = $this->resolverConfigEsquema($esquema);
        if ($config !== null) {
            return $config;
        }

        $base = $this->nombreBaseConfigDb();
        $this->avisosConexion[] = ConfigDB::mensajeAvisoEsquemaConexionFaltante(
            $base,
            $esquema,
            ' ' . _('La operación continúa con el usuario de importar.'),
        );

        $oConfigImportar = new ConfigDB('importar');
        $configImportar = $oConfigImportar->getEsquema($this->claveEsquemaImportarParaDb());
        $configImportar['schema'] = $esquema;

        return $configImportar;
    }

    /**
     * @return list<string>
     */
    private function basesConfigParaEsquemaDl(string $esquema): array
    {
        $principal = $this->nombreBaseConfigDb();
        $bases = [$principal];

        if ($principal === 'sf' && preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+f$/', $esquema) === 1) {
            $bases[] = 'sf-e';
        }
        if ($principal === 'sv' && preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+v$/', $esquema) === 1) {
            $bases[] = 'sv-e';
        }

        $bases[] = 'importar';

        return array_values(array_unique($bases));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolverConfigEsquema(string $esquema): ?array
    {
        foreach ($this->basesConfigParaEsquemaDl($esquema) as $base) {
            $oConfigDB = new ConfigDB($base);
            if ($oConfigDB->tieneEsquema($esquema)) {
                return $oConfigDB->getEsquema($esquema);
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public function consumirAvisosConexion(): array
    {
        $avisos = $this->avisosConexion;
        $this->avisosConexion = [];

        return $avisos;
    }

    private function getConexionPDO($esquema = '')
    {
        $config = $this->getConfigConexion($esquema);

        try {
            return (new DBConnection($config))->getPDO();
        } catch (\PDOException $e) {
            if (!$this->esErrorRolInexistente($e)) {
                throw $e;
            }

            $rol = (string) ($config['user'] ?? '');
            $schema = (string) ($config['schema'] ?? $esquema);
            $this->avisosConexion[] = sprintf(
                _('El rol «%1$s» no existe; la operación continúa con el usuario de importar (esquema «%2$s»).'),
                $rol,
                $schema,
            );

            $oConfigDB = new ConfigDB('importar');
            $configImportar = $oConfigDB->getEsquema($this->claveEsquemaImportarParaDb());
            $configImportar['schema'] = $config['schema'];

            return (new DBConnection($configImportar))->getPDO();
        }
    }

    private function esErrorRolInexistente(\PDOException $e): bool
    {
        $msg = $e->getMessage();

        return str_contains($msg, 'does not exist')
            && (str_contains($msg, 'role') || preg_match('/FATAL:\s+role\s+/i', $msg) === 1);
    }

    private function claveEsquemaImportarParaDb(): string
    {
        return match ($this->getDbName()) {
            'sv' => 'publicv',
            'sv-e' => 'publicv-e',
            'sf', 'sf-e' => 'publicf',
            default => 'public',
        };
    }

    private function regclassTabla(\PDO $oDbl, string $esquema, string $tabla): ?string
    {
        $esquema = str_replace('"', '', $esquema);
        $tabla = str_replace('"', '', $tabla);
        $st = $oDbl->prepare('SELECT to_regclass(:qualified) AS tabla');
        $st->execute(['qualified' => '"' . $esquema . '".' . $tabla]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if ($row === false || empty($row['tabla'])) {
            return null;
        }

        return (string) $row['tabla'];
    }

    private function esquemaExiste(\PDO $oDbl, string $esquema): bool
    {
        $esquema = str_replace('"', '', $esquema);
        $st = $oDbl->prepare('SELECT 1 FROM pg_namespace WHERE nspname = :nsp LIMIT 1');
        $st->execute(['nsp' => $esquema]);

        return (bool) $st->fetchColumn();
    }

    private function avisoOmiteTrasladoDl2resto(string $esquema, string $tabla, string $bloque): void
    {
        $this->avisosConexion[] = sprintf(
            _('No existe «%1$s.%2$s»; se omite el traslado %3$s a resto.'),
            $esquema,
            $tabla,
            $bloque,
        );
    }

    /**
     * @return bool true si la tabla indicadora existe y debe ejecutarse el traslado
     */
    private function debeTrasladarDl2resto(\PDO $oDbl, string $esquema, string $tablaIndicadora, string $bloque): bool
    {
        if (!$this->esquemaExiste($oDbl, $esquema)) {
            $this->avisosConexion[] = sprintf(
                _('No existe el esquema «%1$s»; se omite el traslado %2$s a resto.'),
                $esquema,
                $bloque,
            );

            return false;
        }

        if ($this->regclassTabla($oDbl, $esquema, $tablaIndicadora) !== null) {
            return true;
        }

        $this->avisoOmiteTrasladoDl2resto($esquema, $tablaIndicadora, $bloque);

        return false;
    }


    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    private function setoDbl($oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    private function getoDbl(): PDO
    {
        return $this->oDbl;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbResto
     */
    private function getoDbResto()
    {
        if (empty($this->oDbResto)) {
            $this->oDbResto = $this->getConexionPDO('resto');
        }
        return $this->oDbResto;
    }


    public function getDir()
    {
        $this->sdir = empty($this->sdir) ? ConfigGlobal::$directorio . '/log/db' : $this->sdir;
        return $this->sdir;
    }

    public function setDir($dir)
    {
        $this->sdir = $dir;
    }

    public function getRegion()
    {
        return $this->sregion;
    }

    public function setRegion($region)
    {
        $this->sregion = $region;
    }

    public function getDl()
    {
        return $this->sdl;
    }

    public function setDl($dl)
    {
        $this->sdl = $dl;
    }

    /**
     * IDs de ubicación en resto (centros/casas ex) pueden ser negativos; MapIdResto los admite.
     */
    private function idUbicacionAEntero(mixed $id, string $contexto = ''): int
    {
        if (is_int($id)) {
            $entero = $id;
        } elseif (is_string($id) && preg_match('/^-?\d+$/', trim($id)) === 1) {
            $entero = (int) trim($id);
        } else {
            throw new \InvalidArgumentException(sprintf(
                _('Identificador de ubicación no válido%1$s: %2$s'),
                $contexto !== '' ? " ($contexto)" : '',
                is_scalar($id) ? (string) $id : get_debug_type($id),
            ));
        }
        if ($entero === 0) {
            throw new \InvalidArgumentException(sprintf(
                _('Identificador de ubicación no válido%1$s: %2$s'),
                $contexto !== '' ? " ($contexto)" : '',
                (string) $entero,
            ));
        }

        return $entero;
    }

    /**
     * map_id vive en comun (esquema región–dl sin sufijo v/f), no en sv/sf.
     */
    private function getoDblComunDl(): PDO
    {
        $esquema = $this->getRegion() . '-' . $this->getDl();
        $dbNameAnterior = $this->getDbName();
        $this->sdbname = 'comun';
        try {
            $config = $this->getConfigConexion($esquema);

            return (new DBConnection($config))->getPDO();
        } finally {
            $this->sdbname = $dbNameAnterior;
        }
    }

    /**
     * Usuario importar (postgres) en comun; devel_db_admin opera esquemas recién creados.
     */
    private function getoDblComunDlAdministrador(): PDO
    {
        $esquema = $this->getRegion() . '-' . $this->getDl();
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('public');
        $config['schema'] = $esquema;

        return (new DBConnection($config))->getPDO();
    }

    private function identificadorPg(string $ident): string
    {
        return '"' . str_replace('"', '""', $ident) . '"';
    }

    private function asegurarTablaMapId(PDO $oDbl, string $esquemaDestino): void
    {
        $esquemaDestino = str_replace('"', '', $esquemaDestino);
        if (!$this->esquemaExiste($oDbl, $esquemaDestino)) {
            return;
        }

        if ($this->regclassTabla($oDbl, $esquemaDestino, 'map_id') !== null) {
            return;
        }

        if ($this->regclassTabla($oDbl, 'resto', 'map_id') !== null) {
            $oDbl->exec(sprintf(
                'CREATE TABLE %s.map_id (LIKE resto.map_id INCLUDING ALL)',
                $this->identificadorPg($esquemaDestino),
            ));

            return;
        }

        throw new \RuntimeException(sprintf(
            _('La tabla map_id no existe en el esquema «%s» y no hay plantilla en «resto». Vuelva a ejecutar «crear esquema» (comun) con un esquema de referencia que la incluya.'),
            $esquemaDestino,
        ));
    }

    /**
     * Tras pg_dump o CREATE … LIKE el dueño puede no ser el rol del esquema DL.
     */
    private function sincronizarPermisosMapIdRolDl(PDO $oDblAdmin, string $esquemaDestino): void
    {
        if ($this->regclassTabla($oDblAdmin, $esquemaDestino, 'map_id') === null) {
            return;
        }

        $qEsquema = $this->identificadorPg(str_replace('"', '', $esquemaDestino));
        $stmts = [
            "ALTER TABLE {$qEsquema}.map_id OWNER TO {$qEsquema}",
            "GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE {$qEsquema}.map_id TO {$qEsquema}",
        ];
        foreach ($stmts as $sql) {
            try {
                $oDblAdmin->exec($sql);
            } catch (\PDOException) {
                // Si ya está alineado (p. ej. mismo dueño), no bloquear el trasvase.
            }
        }
    }

    private function configurarMapIdRepository(MapIdRepositoryInterface $mapIdRepository): void
    {
        $esquemaComun = $this->esquemaBaseDl();
        $oDblOrigen = $this->getoDblComunDlAdministrador();
        if (!$this->esquemaExiste($oDblOrigen, $esquemaComun)) {
            $this->avisosConexion[] = sprintf(
                _('No existe el esquema «%s»; se omiten operaciones con map_id.'),
                $esquemaComun,
            );

            return;
        }

        $this->asegurarMapIdEnConexionComun($oDblOrigen, $esquemaComun);

        $oDblReplica = $this->getoDblComunDlAdministradorReplica();
        if ($oDblReplica !== null) {
            $this->asegurarMapIdEnConexionComun($oDblReplica, $esquemaComun);
            $this->refrescarSuscripcionComunTrasMapId();
        }

        $mapIdRepository->setoDbl($oDblOrigen);
        $mapIdRepository->setoDbl_Select($oDblReplica ?? $oDblOrigen);
    }

    private function asegurarMapIdEnConexionComun(PDO $oDblAdmin, string $esquemaComun): void
    {
        if (!$this->esquemaExiste($oDblAdmin, $esquemaComun)) {
            return;
        }

        $this->asegurarTablaMapId($oDblAdmin, $esquemaComun);
        $this->sincronizarPermisosMapIdRolDl($oDblAdmin, $esquemaComun);
    }

    /**
     * Réplica interior (comun_select): misma tabla map_id que en origen para la suscripción lógica.
     */
    private function getoDblComunDlAdministradorReplica(): ?PDO
    {
        if (preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR)) {
            return null;
        }

        $esquema = $this->esquemaBaseDl();
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getConexionMantenimiento('public_select');
        $config['schema'] = $esquema;

        return (new DBConnection($config))->getPDO();
    }

    private function refrescarSuscripcionComunTrasMapId(): void
    {
        try {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getConexionMantenimiento('public_select');
            $host = (string) ($config['host'] ?? '');
            $dsn = (new DBConnection($config))->getURI();
            $logFile = ConfigGlobal::$directorio . '/log/db/map_id.refresh_sub.sql';
            $aviso = (new DBRefresh())->refreshSubscription($host, 'comun', $dsn, $logFile);
            if ($aviso !== null) {
                $this->avisosConexion[] = $aviso;
            }
        } catch (\Throwable $e) {
            $this->avisosConexion[] = sprintf(
                _('Aviso: no se pudo refrescar la suscripción tras crear map_id en réplica: %s'),
                $e->getMessage(),
            );
        }
    }

    private function esquemaBaseDl(): string
    {
        return $this->getRegion() . '-' . $this->getDl();
    }

    /**
     * IDs de dirección DL: prefijo db_idschema + índice de tabla, no el nextval en bruto.
     */
    private function nuevoIdDireccionDl(string $tabla, int $idAutoSecuencia): int
    {
        return GenerateIdGlobal::generateIdGlobal(
            $this->esquemaBaseDl(),
            $tabla,
            $idAutoSecuencia,
        );
    }

    /**
     * Los GRANT de {@see DBAbstract::addPermisoGlobal} / addPermisoRole deben ir al esquema
     * que se opera (p. ej. eliminar otro DL desde devel_db_admin), no al de la sesión web.
     */
    public function usarRolesDelEsquemaObjetivo(): void
    {
        $base = $this->getRegion() . '-' . $this->getDl();
        $this->esquema = $base;
        $this->role = '"' . $base . '"';

        $dbName = isset($this->sdbname) ? $this->getDbName() : '';
        $suffix = match ($dbName) {
            'sv', 'sv-e' => 'v',
            'sf', 'sf-e' => 'f',
            default => '',
        };
        $this->role_vf = $suffix === '' ? $this->role : '"' . $base . $suffix . '"';
    }

    /**
     * Revoca permisos concedidos en dl2resto (orbix + esquema resto*). Requiere setRegion/setDl/setDbName y {@see usarRolesDelEsquemaObjetivo}.
     *
     * @param 'comun'|'sfsv' $permisoDb mismo valor que en {@see addPermisoGlobal}
     */
    public function revocarPermisosDl2resto(string $permisoDb): void
    {
        if ($permisoDb === 'comun') {
            $esquema = $this->getRegion() . '-' . $this->getDl();
            $this->delPermisoRole('comun', $esquema);
            $this->delPermisoGlobal('comun');

            return;
        }

        if ($permisoDb === 'sfsv') {
            $this->delPermisoRole('sfsv', $this->getEsquema());
            $this->delPermisoGlobal('sfsv');

            return;
        }

        throw new \InvalidArgumentException(sprintf(_('Tipo de permiso no válido: %s'), $permisoDb));
    }

    public function getEsquema()
    {
        switch ($this->getDbName()) {
            case 'sv':
            case 'sv-e':
                $seccion = 'v';
                break;
            case 'sf':
                $seccion = 'f';
                break;
            case 'comun':
                $seccion = '';
                break;
        }
        $this->sEsquema = $this->getRegion() . '-' . $this->getDl() . $seccion;
        return $this->sEsquema;
    }

    public function getResto()
    {
        switch ($this->getDbName()) {
            case 'sv':
            case 'sv-e':
                $seccion = 'v';
                break;
            case 'sf':
                $seccion = 'f';
                break;
            case 'comun':
                $seccion = '';
                break;
        }
        $this->sEsquema = 'resto' . $seccion;
        return $this->sEsquema;
    }

    /**
     * Fija las secuencias de un esquema
     * Busca todas las secuencias del esquema New, busca su valor máximo y cambia la secuencia a este valor
     *
     */
    public function fix_seq()
    {
        $oDbl = $this->getoDbl();
        $esquema = $this->getEsquema();
        // buscar todas las secuencias del esquema y crear la instruccion sql para poner el valor MAX.
        // Guardo las instrucciones en un fichero <== No se puede si no soy superusuario: Lo hago una por una:
        $sql = "SELECT  'SELECT SETVAL(' ||quote_literal(quote_ident(PGT.schemaname)|| '.'||quote_ident(S.relname))|| ', MAX(' ||quote_ident(C.attname)|| ') ) FROM ' ||quote_ident(PGT.schemaname)|| '.'||quote_ident(T.relname)|| ';'
			FROM pg_class AS S, pg_depend AS D, pg_class AS T, pg_attribute AS C, pg_tables AS PGT
			WHERE S.relkind = 'S'
				AND S.oid = D.objid
				AND D.refobjid = T.oid
				AND D.refobjid = C.attrelid
				AND D.refobjsubid = C.attnum
				AND T.relname = PGT.tablename
			AND PGT.schemaname='$esquema'
			ORDER BY S.relname
			";
        foreach ($oDbl->query($sql) as $row) {
            $oDbl->query($row[0]);
        }
    }

    // COMUN
    //-------------- Actividades ----------------------
    public function actividades($que)
    {
        // Conexión DB comun
        $oDbl = $this->getoDbl();

        $region = $this->getRegion();
        $dl = $this->getDl();

        switch ($que) {
            case 'resto2dl':
                // via objetos, para no dar permisos especiales a las tablas:
                if ($dl === 'cr') {
                    $dl_org = $region;
                } else {
                    $dl_org = $dl;
                }
                $ActividadExRepository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
                $cActividades = $ActividadExRepository->getActividades(['dl_org' => $dl_org]);
                $error = '';
                if (!empty($cActividades)) {
                    $MapIdRepository = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
                    $this->configurarMapIdRepository($MapIdRepository);
                    $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
                    foreach ($cActividades as $oActividad) {
                        //TODO: $oActividadDl->setNoGenerarProceso(TRUE);
                        if ($ActividadDlRepository->Guardar($oActividad, false) === false) { // Pongo el param registrarCambios=false para que no anote cambios.
                            $error .= '<br>' . _("no se ha guardado la actividad");
                            exit($error);
                        }
                        //borrar la origen:
                        $ActividadExRepository->Eliminar($oActividad);
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            case 'dl2resto':
                if (!$this->debeTrasladarDl2resto($oDbl, $this->getEsquema(), 'a_actividades_dl', 'actividades')) {
                    return true;
                }

                $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
                $ActividadDlRepository->setoDbl($oDbl);
                $cActividades = $ActividadDlRepository->getActividades(['dl_org' => $dl]);
                $error = '';
                if (!empty($cActividades)) {
                    $ActividadExRepository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
                    foreach ($cActividades as $oActividad) {
                        // TODO: $oActividadEx->setNoGenerarProceso(TRUE);

                        if ($ActividadExRepository->Guadar($oActividad, false) === false) { // Pongo el param registrarCambios=false para que no anote cambios.
                            $error .= '<br>' . _("no se ha guardado la actividad");
                            exit($error);
                        }
                        //borrar la origen:
                        $ActividadDlRepository->Eliminar($oActividad);
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            default:
                return false;
        }
    }

//---------------- CDC --------------------
//---------------- Direcciones CDC --------------------
//---------------- Teleco CDC --------------------
    public function cdc($que)
    {
        // Conexión DB comun
        $oDbl = $this->getoDbl();

        $esquema = $this->getEsquema();
        $dl = $this->getDl();
        $region = $this->getRegion();
        $tipoUbicacion = substr($dl, 0, 2); // puede ser: cr => comisión, dl => delegación, ci => centro interregional.

        $MapIdRepository = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
        switch ($que) {
            case 'resto2dl':
                $this->configurarMapIdRepository($MapIdRepository);
                if ($tipoUbicacion === 'cr') { //no hay delegaciones.
                    $aWhere = ['dl' => '', 'region' => $region];
                    $aOperador = ['dl' => 'IS NULL'];
                } else {
                    $aWhere = ['dl' => $dl, 'region' => $region];
                    $aOperador = [];
                }
                $CasaExRepository = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
                $cCasasEx = $CasaExRepository->getCasas($aWhere, $aOperador);
                $error = '';
                $CasaDlRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
                $RelacionCasaDlDireccion = $GLOBALS['container']->get(RelacionCasaDlDireccionRepositoryInterface::class);
                $RelacionCasaExDireccion = $GLOBALS['container']->get(RelacionCasaExDireccionRepositoryInterface::class);
                foreach ($cCasasEx as $oCasaEx) {
                    $aDades = $oCasaEx->toArrayForDatabase();
                    $oCasaDl = Casa::fromArray($aDades);
                    // actualizar el tipo_ubi.
                    $oCasaDl->setTipo_casa('cdcdl');
                    $CasaDlRepository->setoDbl($oDbl);
                    $newIdCasa = $CasaDlRepository->getNewId();
                    $oCasaDl->setId_ubi($newIdCasa);
                    if ($CasaDlRepository->Guardar($oCasaDl) === FALSE) {
                        $error .= '<br>' . _("no se ha guardado la casa");
                    } else {
                        $id_ubi_old = $this->idUbicacionAEntero($aDades['id_ubi'] ?? null, 'id_ubi');
                        $oMapId = $MapIdRepository->findById('Casa', $id_ubi_old);
                        if ($oMapId === null) {
                            $oMapId = new MapId();
                            $oMapId->setObjeto('Casa');
                            $oMapId->setIdRestoVo($id_ubi_old);
                        }
                        $oMapId->setIdDlVo($this->idUbicacionAEntero($newIdCasa, 'id_dl'));
                        $MapIdRepository->Guardar($oMapId);
                        // Buscar la dirección
                        $aIdDirecciones = $RelacionCasaExDireccion->getDireccionesPorUbi($id_ubi_old);
                        $DireccionCasaDlRepository = $GLOBALS['container']->get(DireccionCasaDlRepositoryInterface::class);
                        $DireccionCasaDlRepository->setoDbl($oDbl);
                        $RelacionCasaDlDireccion->setoDbl($oDbl);
                        $DireccionCasaExRepository = $GLOBALS['container']->get(DireccionCasaExRepositoryInterface::class);
                        foreach ($aIdDirecciones as $aDireccion) {
                            $id_direccion_old = $aDireccion['id_direccion'];
                            $principal = $aDireccion['principal'];
                            $oDireccionEx = $DireccionCasaExRepository->findById($id_direccion_old);
                            if ($oDireccionEx === null) {
                                continue;
                            }
                            $newIdDireccion = $this->nuevoIdDireccionDl(
                                'u_dir_cdc_dl',
                                $DireccionCasaDlRepository->getNewId(),
                            );
                            $oDireccionDl = clone $oDireccionEx;
                            $oDireccionDl->setId_direccion($newIdDireccion);
                            $DireccionCasaDlRepository->Guardar($oDireccionDl);
                            // Map
                            $oMapId = $MapIdRepository->findById('Direccion', $id_direccion_old);
                            if ($oMapId === null) {
                                $oMapId = new MapId();
                                $oMapId->setObjeto('Direccion');
                                $oMapId->setIdRestoVo($id_direccion_old);
                            }
                            $oMapId->setIdDlVo($this->idUbicacionAEntero($newIdDireccion, 'id_dl'));
                            $MapIdRepository->Guardar($oMapId);
                            // cross Direccion
                            $RelacionCasaDlDireccion->asociarDireccion($newIdCasa, $newIdDireccion, is_true($principal));
                            // Eliminar el cross y la direccion
                            $DireccionCasaExRepository->Eliminar($oDireccionEx);
                            // delete cross (deberia borrarse sólo; por el foreign key).
                            $RelacionCasaExDireccion->desasociarDireccion($id_ubi_old, $id_direccion_old);
                        }
                        // Buscar las telecos
                        $TelecoCdcExRepository = $GLOBALS['container']->get(TelecoCdcExRepositoryInterface::class);
                        $cTelecos = $TelecoCdcExRepository->getTelecos(['id_ubi' => $id_ubi_old]);
                        $TelecoCdcDlRepository = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
                        foreach ($cTelecos as $oTelecoCdcEx) {
                            $newId = $TelecoCdcDlRepository->getNewId();
                            $oTelecoCdcDl = clone $oTelecoCdcEx;
                            $oTelecoCdcDl->setId_teleco($newId);
                            if ($TelecoCdcDlRepository->Guardar($oTelecoCdcDl) === FALSE) {
                                $error .= '<br>' . _("no se ha guardado la teleco de la casa");
                            } else {
                                // Eliminar la teleco
                                $TelecoCdcExRepository->Eliminar($oTelecoCdcEx);
                            }
                        }
                        //borrar la origen:
                        $CasaExRepository->Eliminar($oCasaEx);
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            case 'dl2resto':
                if (!$this->debeTrasladarDl2resto($oDbl, $esquema, 'u_cdc_dl', 'CDC')) {
                    return true;
                }

                $this->addPermisoGlobal('comun');
                $this->addPermisoRole('comun', $esquema);
                $a_sql = [];
                //cdc
                $a_sql[] = "INSERT INTO resto.u_cdc_ex SELECT * FROM \"$esquema\".u_cdc_dl ;";
                // primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
                $a_sql[] = "INSERT INTO resto.u_dir_cdc_ex SELECT * FROM  \"$esquema\".u_dir_cdc_dl ;";
                $a_sql[] = "INSERT INTO  resto.u_cross_cdc_ex_dir  SELECT * FROM \"$esquema\".u_cross_cdc_dl_dir ;";
                // delete cdc
                $a_sql[] = "TRUNCATE \"$esquema\".u_cdc_dl RESTART IDENTITY CASCADE;";
                // delete dir
                $a_sql[] = "TRUNCATE \"$esquema\".u_dir_cdc_dl RESTART IDENTITY CASCADE;";
                // delete cross (debería borrarse sólo; por el foreign key).
                $this->executeSql($a_sql);
                $this->delPermisoRole('comun', $esquema);
                $this->delPermisoGlobal('comun');
                break;
            default:
                return false;
        }
    }

// SV o SF
//---------------- Ctr --------------------
//---------------- Direcciones Ctr --------------------
//---------------- Teleco Ctr --------------------
    public function ctr($que)
    {
        // Conexión DB SV/SF
        $oDbl = $this->getoDbl();
        $esquema = $this->getEsquema();
        $resto = $this->getResto();
        $oDblComun = $this->getoDblComunDl();

        $dl = $this->getDl();
        $region = $this->getRegion();
        $tipoUbicacion = substr($dl, 0, 2); // puede ser: cr => cominsión, dl => delegacion, ci => centro interregional.

        $MapIdRepository = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
        switch ($que) {
            case 'resto2dl':
                $this->configurarMapIdRepository($MapIdRepository);
                if ($tipoUbicacion === 'cr') { //no hay delegaciones.
                    $aWhere = ['dl' => '', 'region' => $region];
                    $aOperador = ['dl' => 'IS NULL'];
                } else {
                    $aWhere = ['dl' => $dl, 'region' => $region];
                    $aOperador = [];
                }
                $CentroExRepository = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
                $cCentroEx = $CentroExRepository->getCentros($aWhere, $aOperador);
                $error = '';
                $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $CentroEllosRepository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
                $RelacionCentroDlDireccion = $GLOBALS['container']->get(RelacionCentroDlDireccionRepositoryInterface::class);
                $RelacionCentroExDireccion = $GLOBALS['container']->get(RelacionCentroExDireccionRepositoryInterface::class);
                foreach ($cCentroEx as $oCentroEx) {
                    $aDades = $oCentroEx->toArrayForDatabase();
                    // actualizar el tipo_ubi.
                    $aDades['tipo_ubi'] = 'ctrdl';
                    // Ahora uso la nomenclatura para dl tipo 'crA'
                    $aDades['dl'] = $dl;
                    $oCentroDl = CentroDl::fromArray($aDades);
                    $CentroDlRepository->setoDbl($oDbl);
                    $newIdCentro = $CentroDlRepository->getNewId();
                    $oCentroDl->setId_ubi($newIdCentro);
                    if ($CentroDlRepository->Guardar($oCentroDl) === FALSE) {
                        $error .= '<br>' . _("no se ha guardado el centro");
                    } else {
                        // Al hacer INSERT se genera un id_ubi nuevo. Para conservar el original:
                        $id_ubi_old = $this->idUbicacionAEntero($aDades['id_ubi'] ?? null, 'id_ubi');
                        $oMapId = $MapIdRepository->findById('Centro', $id_ubi_old);
                        if ($oMapId === null) {
                            $oMapId = new MapId();
                            $oMapId->setObjeto('Centro');
                            $oMapId->setIdRestoVo($id_ubi_old);
                        }
                        $oMapId->setIdDlVo($this->idUbicacionAEntero($newIdCentro, 'id_dl'));
                        $MapIdRepository->Guardar($oMapId);
                        // Además hay que añadirlo a la copia en DB comun:
                        // para la sf (comienza por 2).
                        if (substr($newIdCentro, 0, 1) == 2) {
                            $oCentroEllas = new CentroEllas();
                            $oCentroEllas->setId_ubi($newIdCentro);
                            $oCentroEllas->setAllAttributes($aDades, TRUE);
                            $CentroEllasRepository->setoDbl($oDblComun);
                            $CentroEllasRepository->Guardar($oCentroEllas);
                        } else {
                            $oCentroEllos = new CentroEllos();
                            $oCentroEllos->setId_ubi($newIdCentro);
                            $oCentroEllos->setAllAttributes($aDades);
                            $CentroEllosRepository->setoDbl($oDblComun);
                            $CentroEllosRepository->Guardar($oCentroEllos);
                        }
                        // Buscar la dirección
                        $aIdDirecciones = $RelacionCentroExDireccion->getDireccionesPorUbi($id_ubi_old);
                        $DireccionCentroDlRepository = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
                        $DireccionCentroDlRepository->setoDbl($oDbl);
                        $RelacionCentroDlDireccion->setoDbl($oDbl);
                        $DireccionCentroExRepository = $GLOBALS['container']->get(DireccionCentroExRepositoryInterface::class);
                        foreach ($aIdDirecciones as $aIdDireccion) {
                            $id_direccion_old = $aIdDireccion ['id_direccion'];
                            $propietario = $aIdDireccion ['propietario'];
                            $principal = $aIdDireccion['principal'];
                            $oDireccionCentroEx = $DireccionCentroExRepository->findById($id_direccion_old);
                            if ($oDireccionCentroEx === null) {
                                continue;
                            }
                            $newIdDireccionCentro = $this->nuevoIdDireccionDl(
                                'u_dir_ctr_dl',
                                $DireccionCentroDlRepository->getNewId(),
                            );
                            $oDireccionCentroDl = clone $oDireccionCentroEx;
                            $oDireccionCentroDl->setId_direccion($newIdDireccionCentro);
                            $DireccionCentroDlRepository->Guardar($oDireccionCentroDl);
                            // Map
                            $oMapId = $MapIdRepository->findById('Direccion', $id_direccion_old);
                            if ($oMapId === null) {
                                $oMapId = new MapId();
                                $oMapId->setObjeto('Direccion');
                                $oMapId->setIdRestoVo($id_direccion_old);
                            }
                            $oMapId->setIdDlVo($this->idUbicacionAEntero($newIdDireccionCentro, 'id_dl'));
                            $MapIdRepository->Guardar($oMapId);
                            // cross Direccion
                            $RelacionCentroDlDireccion->asociarDireccion($newIdCentro, $newIdDireccionCentro, is_true($principal));
                            $RelacionCentroDlDireccion->updatePropietario($newIdCentro, $newIdDireccionCentro, is_true($propietario));
                            // Eliminar el cross y la dirección
                            $DireccionCentroExRepository->Eliminar($oDireccionCentroEx);
                            // delete cross (debería borrarse sólo; por el foreign key).
                            $RelacionCentroExDireccion->desasociarDireccion($id_ubi_old, $id_direccion_old);
                        }
                        // Buscar las telecos
                        $TelecoCtrExRepository = $GLOBALS['container']->get(TelecoCtrExRepositoryInterface::class);
                        $cTelecos = $TelecoCtrExRepository->getTelecos(['id_ubi' => $id_ubi_old]);
                        $TelecoCtrDlRepository = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
                        foreach ($cTelecos as $oTelecoCtrEx) {
                            $newId = $TelecoCtrDlRepository->getNewId();
                            $oTelecoCtrDl = clone $oTelecoCtrEx;
                            $oTelecoCtrDl->setId_teleco($newId);
                            if ($TelecoCtrDlRepository->Guardar($oTelecoCtrDl) === FALSE) {
                                $error .= '<br>' . _("no se ha guardado la teleco el ctr");
                            } else {
                                // Eliminar la teleco
                                $TelecoCtrExRepository->Eliminar($oTelecoCtrEx);
                            }
                        }
                        //borrar la origen:
                        $CentroExRepository->Eliminar($oCentroEx);
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            case 'dl2resto':
                if (!$this->debeTrasladarDl2resto($oDbl, $esquema, 'u_centros_dl', 'CTR')) {
                    return true;
                }

                // actualizar el tipo_ubi.
                $sql = "UPDATE \"$esquema\".u_centros_dl SET tipo_ubi='ctrex'";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBTrasvase.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }

                $this->addPermisoGlobal('sfsv');
                $this->addPermisoRole('sfsv', $esquema);

                $sql = "INSERT INTO \"$resto\".u_centros_ex SELECT tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,active,f_active,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre,id_auto FROM \"$esquema\".u_centros_dl";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBEliminar.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    $this->delPermisoRole('sfsv', $esquema);
                    $this->delPermisoGlobal('sfsv');
                    return false;
                } else {
                    // primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
                    $sql = "INSERT INTO \"$resto\".u_dir_ctr_ex SELECT * FROM  \"$esquema\".u_dir_ctr_dl";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    $sql = "INSERT INTO \"$resto\".u_cross_ctr_ex_dir SELECT * FROM \"$esquema\".u_cross_ctr_dl_dir ";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    // delete ctr
                    $sql = "TRUNCATE \"$esquema\".u_centros_dl RESTART IDENTITY CASCADE";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    // delete dir
                    $sql = "TRUNCATE \"$esquema\".u_dir_ctr_dl RESTART IDENTITY CASCADE";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    // delete cross (debería borrarse sólo; por el foreign key).
                }
                $this->delPermisoRole('sfsv', $esquema);
                $this->delPermisoGlobal('sfsv');
                break;
            default:
                return false;
        }
    }

}