<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use RuntimeException;
use src\devel_db_admin\infrastructure\DBAlterSchema;
use src\shared\config\ServerConf;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBRol;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Throwable;

/**
 * Renombrar esquema region-dl (comun/sv/sv-e/sf + defaults en tablas).
 * En hosts *.docker se omiten réplicas (*_select) como en {@see CrearEsquema}.
 *
 * Idempotente por bloque (comun, comun_select, sv, sv-e, sv-e_select, sf):
 * todas las operaciones DDL usan la conexión `importar` (superusuario, igual que {@see CrearEsquema}),
 * y antes de cada ALTER SCHEMA / ALTER ROLE / actualización de `.inc` se comprueba si ese paso ya está hecho.
 * Esto permite reanudar un renombre interrumpido a medias (p. ej. caída entre `public` y `public_select`)
 * sin tener que pasar antes por «Corregir».
 */
final class RenombrarEsquema
{
    public function __construct(
        private readonly object $container,
    ) {
    }

    /**
     * @return array{avisos: list<string>}|array{error: string, avisos: list<string>}
     */
    public function ejecutar(
        string $esquemaOrigenCampo,
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): array {
        $ctx = RenombrarEsquemaVerificacionContexto::desdeEntrada($esquemaOrigenCampo, $region, $dl, $comun, $sv, $sf);
        if (is_array($ctx)) {
            return ['avisos' => [$ctx['resumen']]];
        }

        $esquema_old = $ctx->esquemaOld;
        $esquema_oldv = $ctx->esquemaOldv;
        $esquema_oldf = $ctx->esquemaOldf;
        $region = $ctx->region;
        $dl = $ctx->dl;

        $esquema = $ctx->esquemaNew;
        $esquemav = $ctx->esquemaNewv;
        $esquemaf = $ctx->esquemaNewf;

        $pwdErr = $this->validarFicherosPasswordAntesDeRenombre($ctx);
        if ($pwdErr !== null) {
            return $pwdErr;
        }

        $isDocker = (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);
        $avisos = [];

        $oDBRol = new DBRol();

        //  USUARIOS Y CAMBIO NOMBRE ESQUEMA  ///////////////////////////////////

        // Hay que pasar como parámetro el nombre de la database, que corresponde al archivo database.inc
        // donde están los passwords. En este caso en importar.inc, tenemos al superadmin.
        $oConfigDB = new ConfigDB('importar');
        //coge los valores de public: 1.la database comun; 2.nombre superusuario; 3.pasword superusuario;

        $DbSchemaRepository = $this->container->get(DbSchemaRepositoryInterface::class);

        $oConfigDBComun = new ConfigDB('comun');
        $oConfigDBSv = new ConfigDB('sv');
        $oConfigDBSve = new ConfigDB('sv-e');
        $oConfigDBSf = $sf !== 0 ? new ConfigDB('sf') : null;

        //************** comun ****************************************
        $oConComun = $this->pdoDesdeImportar($oConfigDB, 'public');
        $this->renombrarBloqueRolEsquema(
            $oDBRol,
            $oConComun,
            $esquema_old,
            $esquema,
            $this->leerPasswordEsquema($oConfigDBComun, $esquema_old, $esquema),
        );
        $this->renombrarClaveInc($oConfigDBComun, 'comun', $esquema_old, $esquema);
        $DbSchemaRepository->cambiarNombre($esquema_old, $esquema, 'comun');

        /////////  para comun en interior (select) — omitir en docker (sin réplica)
        if (!$isDocker) {
            $oConComunSel = $this->pdoDesdeImportar($oConfigDB, 'public_select');
            $this->renombrarBloqueRolEsquema(
                $oDBRol,
                $oConComunSel,
                $esquema_old,
                $esquema,
                $this->leerPasswordEsquema($oConfigDBComun, $esquema_old, $esquema),
            );
            $this->renombrarClaveInc($oConfigDBComun, 'comun_select', $esquema_old, $esquema);
        }

        // *********************  sv  *********************************
        $oConSv = $this->pdoDesdeImportar($oConfigDB, 'publicv');
        $this->renombrarBloqueRolEsquema(
            $oDBRol,
            $oConSv,
            $esquema_oldv,
            $esquemav,
            $this->leerPasswordEsquema($oConfigDBSv, $esquema_oldv, $esquemav),
        );
        $this->renombrarClaveInc($oConfigDBSv, 'sv', $esquema_oldv, $esquemav);
        $DbSchemaRepository->cambiarNombre($esquema_old, $esquema, 'sv');

        // *********************  sv-e  ********************************
        // En sv-e (misma instancia que sv) solo renombramos el ESQUEMA; el ROL ya se renombró en el bloque sv.
        $oConSve = $this->pdoDesdeImportar($oConfigDB, 'publicv-e');
        $this->renombrarBloqueSoloEsquema(
            $oDBRol,
            $oConSve,
            $esquema_oldv,
            $esquemav,
            $this->leerPasswordEsquema($oConfigDBSve, $esquema_oldv, $esquemav),
        );
        $this->renombrarClaveInc($oConfigDBSve, 'sv-e', $esquema_oldv, $esquemav);
        $DbSchemaRepository->cambiarNombre($esquema_old, $esquema, 'sv-e');

        //////////// sv-e para db interior (select) — omitir lista réplica en docker
        if (!$isDocker) {
            $this->renombrarClaveInc($oConfigDBSve, 'sv-e_select', $esquema_oldv, $esquemav);
        }

        // *********************  sf  *********************************
        if ($sf !== 0 && $oConfigDBSf !== null) {
            $pwdSf = $this->leerPasswordEsquema($oConfigDBSf, $esquema_oldf, $esquemaf);
            $this->renombrarClaveInc($oConfigDBSf, 'sf', $esquema_oldf, $esquemaf);
            $this->renombrarClaveInc($oConfigDBSf, 'sf-e', $esquema_oldf, $esquemaf);

            if ($this->delegacionSfTieneEsquemaEnPostgres($oConfigDB, $esquema_oldf, $esquemaf)) {
                $oConSf = $this->pdoDesdeImportar($oConfigDB, 'publicf');
                $this->renombrarBloqueRolEsquema(
                    $oDBRol,
                    $oConSf,
                    $esquema_oldf,
                    $esquemaf,
                    $pwdSf,
                );
                $DbSchemaRepository->cambiarNombre($esquema_old, $esquema, 'sf');
            } elseif ($this->delegacionSfTieneRolEnPostgres($oConfigDB, $esquema_oldf, $esquemaf)) {
                $oConSf = $this->pdoDesdeImportar($oConfigDB, 'publicf');
                $this->renombrarSoloRolDelegacionSf($oDBRol, $oConSf, $esquema_oldf, $esquemaf, $pwdSf);
                $avisos[] = sprintf(
                    _('Aviso: en sf solo existía el rol «%1$s»/«%2$s», no el esquema (no se marcó sf al «crear esquema»). Se renombró el rol y las claves .inc; no se tocó la BD sf-e.'),
                    $esquema_oldf,
                    $esquemaf,
                );
            } else {
                $avisos[] = sprintf(
                    _('Aviso: sf marcado en el formulario pero no hay esquema ni rol «%1$s»/«%2$s» en PostgreSQL; solo se actualizaron los .inc si existían.'),
                    $esquema_oldf,
                    $esquemaf,
                );
            }
        }

        // ESQUEMAS: CAMBIOS EN TABLAS ////////////////////////////////////////
        
        $RegionNew = $region;
        $DlNew = $dl;
        
        // comun
        if ($comun !== 0) {
            // Valores Default:
            $aDefaults = RenombrarEsquemaDefaultsCatalog::comun($esquema, $RegionNew, $DlNew);
        
            // datos
            // REGEXP_REPLACE(source, pattern, replacement_string,[, flags])
        
            $region_old = strtok($esquema_old, '-');
            $dl_old = strtok('-');
        
            $aDatos = [
                ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
                ['tabla' => 'av_cambios_dl', 'campo' => 'dl_org', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
                ['tabla' => 'cp_sacd', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'cu_centros_dl', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'cu_centros_dl', 'campo' => 'region', 'pattern' => "\m$region_old\M", 'replacement' => "$RegionNew"],
                ['tabla' => 'cu_centros_dlf', 'campo' => 'dl', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
                ['tabla' => 'cu_centros_dlf', 'campo' => 'region', 'pattern' => "\m$region_old\M", 'replacement' => "$RegionNew"],
            ];
        
            // comun normal
            $oConfigDB = new ConfigDB('importar'); //de la database comun
            $config = $oConfigDB->getEsquema('public'); //de la database comun
        
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();
        
            $oAlterSchema = new DBAlterSchema();
            $oAlterSchema->setDbConexion($oDevelPC);
            $oAlterSchema->setSchema($esquema);
        
            $oAlterSchema->setDefaults($aDefaults);
            $oAlterSchema->updateDatosRegexp($aDatos);

            // comun_select (servidor interno) — omitir en docker
            if (!$isDocker) {
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('public_select'); //de la database comun

                $oConexion = new DBConnection($config);
                $oDevelPC = $oConexion->getPDO();

                $oAlterSchema = new DBAlterSchema();
                $oAlterSchema->setDbConexion($oDevelPC);
                $oAlterSchema->setSchema($esquema);

                $oAlterSchema->setDefaults($aDefaults);
                // No hace falta cambiar los datos, ya se sincroniza
                //$oAlterSchema->updateDatosRegexp($aDatos);
            }
        }
        
        // sv
        if ($sv !== 0) {
            $oConfigDB = new ConfigDB('importar'); //de la database sv
            $config = $oConfigDB->getEsquema('publicv');
        
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();
        
            $oAlterSchema = new DBAlterSchema();
            $oAlterSchema->setDbConexion($oDevelPC);
            $oAlterSchema->setSchema($esquemav);
        
            // Valores Default:
            $aDefaults = RenombrarEsquemaDefaultsCatalog::sv($esquemav, $RegionNew, $DlNew);
            $oAlterSchema->setDefaults($aDefaults);
        
            // datos
            // REGEXP_REPLACE(source, pattern, replacement_string,[, flags])
        
            $region_old = strtok($esquema_old, '-');
            $dl_old = strtok('-');
        
            $aDatos = [
                ['tabla' => 'p_agregados', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'p_de_paso_out', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'p_numerarios', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'p_sssc', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'p_supernumerarios', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'u_centros_dl', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'u_centros_dl', 'campo' => 'region', 'pattern' => "\m$region_old\M", 'replacement' => "$RegionNew"],
                ['tabla' => 'd_traslados', 'campo' => 'ctr_origen', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'd_traslados', 'campo' => 'ctr_destino', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'da_plazas_dl', 'campo' => 'dl_tabla', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
            ];
        
            $oAlterSchema->updateDatosRegexp($aDatos);
        
            // borrar
            $aDatos = [
                ['tabla' => 'u_centros_dl', 'campo' => 'id_zona'],
            ];
            $oAlterSchema->setNullDatos($aDatos);
        
            // Todos los esquemas:
            $aDatos = [
                ['tabla' => 'global.d_traslados', 'campo' => 'ctr_origen', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'global.d_traslados', 'campo' => 'ctr_destino', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
            ];
            $oAlterSchema->updateDatosRegexpTodos($aDatos);
        
            /*
            * da_plazas_dl       
            cedidas {"dlb": 2, "dlp": 3, "dlv": 1, "dlmE": 1, "dlmO": 1, "dlst": 1}
            */
            $oAlterSchema->updateCedidasAll($dl_old, $DlNew);
        
            ////////////// Esquema sv-e
            // Valores Default:
            $aDefaults = RenombrarEsquemaDefaultsCatalog::svE($esquemav, $DlNew);
        
            // datos
            // Todos los esquemas:
            $aDatos = [
                ['tabla' => 'global.d_asistentes_dl', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
                ['tabla' => 'publicv.d_asistentes_de_paso', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
            ];
        
            ///// sv-e normal
            $oConfigDB = new ConfigDB('importar'); //de la database sv
            $config = $oConfigDB->getEsquema('publicv-e');
        
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();
        
            $oAlterSchema = new DBAlterSchema();
            $oAlterSchema->setDbConexion($oDevelPC);
            $oAlterSchema->setSchema($esquemav);
        
            $oAlterSchema->setDefaults($aDefaults);
            $oAlterSchema->updateDatosRegexpTodos($aDatos);
            $oAlterSchema->updatePropietarioAll($dl_old, $DlNew);

            ///// sv-e_select (servidor interno) — omitir en docker
            if (!$isDocker) {
                $oConfigDB = new ConfigDB('importar'); //de la database sv
                $config = $oConfigDB->getEsquema('publicv-e_select');

                $oConexion = new DBConnection($config);
                $oDevelPC = $oConexion->getPDO();

                $oAlterSchema = new DBAlterSchema();
                $oAlterSchema->setDbConexion($oDevelPC);
                $oAlterSchema->setSchema($esquemav);

                $oAlterSchema->setDefaults($aDefaults);
                // No debería hacer falta. Se sincroniza
                //$oAlterSchema->updateDatosRegexpTodos($aDatos);
                //$oAlterSchema->updatePropietarioAll($dl_old, $DlNew);
            }
        }

        return ['avisos' => array_merge($avisos, $oDBRol->consumirAvisosRenameRol())];
    }

    /**
     * El desplegable de origen lista esquemas vistos en PostgreSQL; el renombre necesita
     * el password en algún `.inc` para reasignarlo tras `ALTER ROLE RENAME` (MD5 se invalida).
     * Aceptamos clave vieja o nueva: si en una BD el rename ya pasó por el `.inc`, la clave nueva
     * tiene el mismo password y permite reanudar el renombre interrumpido.
     *
     * @return array{error: string, avisos: list<string>}|null
     */
    private function validarFicherosPasswordAntesDeRenombre(RenombrarEsquemaVerificacionContexto $ctx): ?array
    {
        $intentos = [
            ['db' => 'comun', 'old' => $ctx->esquemaOld, 'new' => $ctx->esquemaNew],
            ['db' => 'sv', 'old' => $ctx->esquemaOldv, 'new' => $ctx->esquemaNewv],
            ['db' => 'sv-e', 'old' => $ctx->esquemaOldv, 'new' => $ctx->esquemaNewv],
        ];
        if ($ctx->sf !== 0 && $this->delegacionSfAplicaEnRenombre($ctx)) {
            $intentos[] = ['db' => 'sf', 'old' => $ctx->esquemaOldf, 'new' => $ctx->esquemaNewf];
        }
        foreach ($intentos as $row) {
            $cfg = new ConfigDB($row['db']);
            if ($this->leerPasswordEsquema($cfg, $row['old'], $row['new']) === null) {
                return [
                    'error' => sprintf(
                        _('El esquema «%1$s» no tiene entrada de conexión en «%2$s.inc» (ni con el nombre antiguo ni con el nuevo «%3$s»). El listado de origen sale de PostgreSQL; hace falta la misma clave en el fichero de passwords (p. ej. tras «Crear esquema») para poder renombrar.'),
                        $row['old'],
                        $row['db'],
                        $row['new'],
                    ),
                    'avisos' => [],
                ];
            }
        }

        return null;
    }

    private function pdoDesdeImportar(ConfigDB $importar, string $esquema): PDO
    {
        $config = $importar->getEsquema($esquema);

        return (new DBConnection($config))->getPDO();
    }

    /**
     * Devuelve el password del esquema mirando primero la clave vieja en el `.inc` y, si no está,
     * la clave nueva (un rename a medias deja el password ya bajo el nombre nuevo en ese `.inc`).
     */
    private function leerPasswordEsquema(ConfigDB $cfg, string $esquemaOld, string $esquemaNew): ?string
    {
        foreach ([$esquemaOld, $esquemaNew] as $clave) {
            if ($clave === '') {
                continue;
            }
            try {
                $row = $cfg->getEsquema($clave);
            } catch (RuntimeException) {
                continue;
            }
            if (isset($row['password']) && is_string($row['password']) && $row['password'] !== '') {
                return $row['password'];
            }
        }

        return null;
    }

    /**
     * Renombra schema + rol en la BD apuntada por $pdo, saltando lo que ya esté hecho.
     * Si el rol nuevo ya existe, además reasegura propietarios/privilegios.
     */
    private function renombrarBloqueRolEsquema(
        DBRol $oDBRol,
        PDO $pdo,
        string $esquemaOld,
        string $esquemaNew,
        ?string $pwd,
    ): void {
        $oDBRol->setDbConexion($pdo);
        $oDBRol->setUser($esquemaNew);
        if ($pwd !== null) {
            $oDBRol->setPwd($pwd);
        }

        $this->asegurarRolDestinoRenombre($oDBRol, $pdo, $esquemaOld, $esquemaNew, $pwd);

        if ($this->existeEsquema($pdo, $esquemaOld) && !$this->existeEsquema($pdo, $esquemaNew)) {
            $oDBRol->renombrarSchema($esquemaOld);
        }

        if ($this->existeRol($pdo, $esquemaNew) && $this->existeEsquema($pdo, $esquemaNew)) {
            $oDBRol->repararEsquemaPostRenombre($esquemaNew);
        }
    }

    /**
     * Variante para sv-e (mismo cluster que sv): el rol suele renombrarse en el bloque sv;
     * aquí se asegura el rol destino, se renombra el esquema en esta BD y se repara solo si el rol existe.
     */
    private function renombrarBloqueSoloEsquema(
        DBRol $oDBRol,
        PDO $pdo,
        string $esquemaOld,
        string $esquemaNew,
        ?string $pwd,
    ): void {
        $oDBRol->setDbConexion($pdo);
        $oDBRol->setUser($esquemaNew);
        if ($pwd !== null) {
            $oDBRol->setPwd($pwd);
        }

        $this->asegurarRolDestinoRenombre($oDBRol, $pdo, $esquemaOld, $esquemaNew, $pwd);

        if ($this->existeEsquema($pdo, $esquemaOld) && !$this->existeEsquema($pdo, $esquemaNew)) {
            $oDBRol->renombrarSchema($esquemaOld);
        }

        if ($this->existeRol($pdo, $esquemaNew) && $this->existeEsquema($pdo, $esquemaNew)) {
            $oDBRol->repararEsquemaPostRenombre($esquemaNew);
        }
    }

    /**
     * Garantiza el rol destino antes de ALTER SCHEMA … OWNER (evita «no existe el rol» tras un rename a medias).
     */
    private function delegacionSfAplicaEnRenombre(RenombrarEsquemaVerificacionContexto $ctx): bool
    {
        if ($this->incTieneClave('sf', $ctx->esquemaOldf) || $this->incTieneClave('sf', $ctx->esquemaNewf)) {
            return true;
        }

        return $this->delegacionSfTieneRolEnPostgres(
            new ConfigDB('importar'),
            $ctx->esquemaOldf,
            $ctx->esquemaNewf,
        ) || $this->delegacionSfTieneEsquemaEnPostgres(
            new ConfigDB('importar'),
            $ctx->esquemaOldf,
            $ctx->esquemaNewf,
        );
    }

    private function delegacionSfTieneEsquemaEnPostgres(
        ConfigDB $oConfigDB,
        string $esquemaOldf,
        string $esquemaNewf,
    ): bool {
        try {
            $pdo = $this->pdoDesdeImportar($oConfigDB, 'publicf');

            return $this->existeEsquema($pdo, $esquemaOldf) || $this->existeEsquema($pdo, $esquemaNewf);
        } catch (Throwable) {
            return false;
        }
    }

    private function delegacionSfTieneRolEnPostgres(
        ConfigDB $oConfigDB,
        string $esquemaOldf,
        string $esquemaNewf,
    ): bool {
        try {
            $pdo = $this->pdoDesdeImportar($oConfigDB, 'publicf');

            return $this->existeRol($pdo, $esquemaOldf) || $this->existeRol($pdo, $esquemaNewf);
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Rol «…f» creado en «crear usuarios» sin esquema sf (crear esquema sin checkbox sf).
     */
    private function renombrarSoloRolDelegacionSf(
        DBRol $oDBRol,
        PDO $pdo,
        string $esquemaOldf,
        string $esquemaNewf,
        ?string $pwd,
    ): void {
        $oDBRol->setDbConexion($pdo);
        $oDBRol->setUser($esquemaNewf);
        if ($pwd !== null) {
            $oDBRol->setPwd($pwd);
        }

        $this->asegurarRolDestinoRenombre($oDBRol, $pdo, $esquemaOldf, $esquemaNewf, $pwd);
    }

    private function asegurarRolDestinoRenombre(
        DBRol $oDBRol,
        PDO $pdo,
        string $esquemaOld,
        string $esquemaNew,
        ?string $pwd,
    ): void {
        if ($this->existeRol($pdo, $esquemaNew)) {
            return;
        }

        if ($this->existeRol($pdo, $esquemaOld)) {
            $oDBRol->renombrarUsuario($esquemaOld);

            return;
        }

        if ($pwd !== null && $pwd !== '') {
            $oDBRol->crearUsuario();
        }
    }

    private function renombrarClaveInc(ConfigDB $cfg, string $ficheroBase, string $claveOld, string $claveNew): void
    {
        if (!$this->incTieneClave($ficheroBase, $claveOld)) {
            return;
        }
        if ($this->incTieneClave($ficheroBase, $claveNew)) {
            return;
        }
        $cfg->renombrarListaEsquema($ficheroBase, $claveOld, $claveNew);
    }

    private function incTieneClave(string $ficheroBase, string $clave): bool
    {
        if ($clave === '') {
            return false;
        }
        try {
            (new ConfigDB($ficheroBase))->getEsquema($clave);

            return true;
        } catch (RuntimeException) {
            return false;
        } catch (Throwable) {
            return false;
        }
    }

    private function existeEsquema(PDO $pdo, string $nombre): bool
    {
        if ($nombre === '') {
            return false;
        }
        try {
            $st = $pdo->prepare('SELECT 1 FROM pg_namespace WHERE nspname = :n LIMIT 1');
            $st->execute(['n' => $nombre]);

            return (bool) $st->fetchColumn();
        } catch (Throwable) {
            return false;
        }
    }

    private function existeRol(PDO $pdo, string $rol): bool
    {
        if ($rol === '') {
            return false;
        }
        try {
            $st = $pdo->prepare('SELECT 1 FROM pg_roles WHERE rolname = :r LIMIT 1');
            $st->execute(['r' => $rol]);

            return (bool) $st->fetchColumn();
        } catch (Throwable) {
            return false;
        }
    }
}
