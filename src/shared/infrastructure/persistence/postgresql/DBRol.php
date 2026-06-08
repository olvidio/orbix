<?php

namespace src\shared\infrastructure\persistence\postgresql;

use PDO;
use PDOException;
use PDOStatement;
use src\shared\config\ServerConf;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

class DBRol
{
    /** @var list<string> Avisos no fatales (p. ej. rol destino eliminado antes de renombrar). */
    private array $avisosRenameRol = [];
    protected ?PDO $oDbl = null;
    protected string $sPwd = '';
    protected string $sUser = '';
    protected string $sOptions = '';

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     */
    function __construct()
    {
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setDbConexion(PDO $oDbl): void
    {
        $this->setoDbl($oDbl);
    }

    protected function setoDbl(PDO $oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    protected function getoDbl(): PDO
    {
        if ($this->oDbl === null) {
            throw new \RuntimeException('DBRol: conexión PDO no inicializada');
        }

        return $this->oDbl;
    }

    public function setUser(string $user): void
    {
        $this->sUser = $user;
    }

    public function setPwd(string $password): void
    {
        //$password_encoded = urlencode ($password);
        $this->sPwd = $password;
    }

    public function setOptions(string $options): void
    {
        $this->sOptions = $options;
    }


    // usuarios:
    public function addGrupo(string $grupo): bool
    {
        $oDbl = $this->getoDbl();
        $sql = "GRANT \"$grupo\" TO \"$this->sUser\"";
        //$sql = "GRANT \"$grupo\" TO \"$this->sUser\" ";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.addGrupo.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.addGrupo.execute';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }

        return true;
    }

    public function delGrupo(string $grupo): bool
    {
        $oDbl = $this->getoDbl();
        $sql = "REVOKE \"$grupo\" FROM \"$this->sUser\"";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.delGrupo.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.delGrupo.execute';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }

        return true;
    }

    public function crearSchema(): bool
    {
        $oDbl = $this->getoDbl();
        $sql = "CREATE SCHEMA IF NOT EXISTS \"$this->sUser\" AUTHORIZATION \"$this->sUser\";";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.crearSchema.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.crearSchema.execute';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }

        return true;
    }

    public function renombrarSchema(string $esquema_old): bool
    {
        $oDbl = $this->getoDbl();
        $sql = "ALTER SCHEMA \"$esquema_old\" RENAME TO \"$this->sUser\";";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.crearSchema.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.crearSchema.execute';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }

        return true;
    }

    /**
     * Tras RENAME del esquema y del rol, el propietario del esquema puede no coincidir con el nombre del rol; Orbix espera que coincidan (como en CREATE SCHEMA … AUTHORIZATION).
     */
    public function asegurarPropietarioEsquema(string $esquemaNombre): bool
    {
        if ($esquemaNombre === '' || preg_match('/[^A-Za-z0-9._-]/', $esquemaNombre)) {
            return false;
        }
        $oDbl = $this->getoDbl();
        $q = '"' . str_replace('"', '""', $esquemaNombre) . '"';
        $sql = "ALTER SCHEMA {$q} OWNER TO {$q}";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.asegurarPropietarioEsquema.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);

            return false;
        }
        try {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.asegurarPropietarioEsquema.execute';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);

                return false;
            }
        } catch (PDOException $e) {
            $sClauError = 'DBRol.asegurarPropietarioEsquema.execute';
            $sClauError .= ' ' . ($e->errorInfo[2] ?? $e->getMessage());
            $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);

            return false;
        }

        return true;
    }

    /**
     * Tras renombrar esquema/rol: dueño del esquema = rol homónimo, mismos dueños en objetos del esquema y GRANT de respaldo (evita «permission denied» en aux_usuarios con sesión del rol delegación).
     */
    public function repararEsquemaPostRenombre(string $esquemaNombre): bool
    {
        if (!$this->asegurarPropietarioEsquema($esquemaNombre)) {
            return false;
        }
        if (!$this->realignarPropietariosObjetosEnEsquema($esquemaNombre)) {
            return false;
        }
        $this->concederPrivilegiosRolSobreSuEsquema($esquemaNombre);

        return true;
    }

    /**
     * Pone el propietario de tablas, particiones, vistas, mat. vistas, secuencias y tablas foráneas del esquema en el rol homónimo al esquema.
     * No incluye índices (relkind i/I): ALTER INDEX suele fallar sin aportar a permisos DML sobre las tablas; el dueño del heap es el relevante.
     * Omite secuencias ligadas a una columna (SERIAL/IDENTITY): PostgreSQL no permite ALTER SEQUENCE OWNER en ellas; el dueño sigue al de la tabla.
     */
    public function realignarPropietariosObjetosEnEsquema(string $esquemaNombre): bool
    {
        if ($esquemaNombre === '' || preg_match('/[^A-Za-z0-9._-]/', $esquemaNombre)) {
            return false;
        }
        $oDbl = $this->getoDbl();
        $st = $oDbl->prepare(
            'SELECT c.relname, c.relkind FROM pg_class c
             INNER JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE n.nspname = :s
               AND c.relkind IN (\'r\', \'p\', \'v\', \'m\', \'S\', \'f\')
               AND c.relname NOT LIKE \'pg\_%\' ESCAPE \'\\\'
               AND NOT (
                 c.relkind = \'S\'
                 AND EXISTS (
                   SELECT 1 FROM pg_depend d
                   WHERE d.classid = \'pg_class\'::regclass
                     AND d.objid = c.oid
                     AND d.refclassid = \'pg_class\'::regclass
                     AND d.refobjsubid <> 0
                     AND d.deptype = \'a\'
                 )
               )',
        );
        if ($st === false) {
            $sClauError = 'DBRol.realignarPropietariosObjetosEnEsquema.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);

            return false;
        }
        $st->execute(['s' => $esquemaNombre]);
        $qs = '"' . str_replace('"', '""', $esquemaNombre) . '"';
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $rel = is_scalar($row['relname'] ?? null) ? (string) $row['relname'] : '';
            $kind = is_scalar($row['relkind'] ?? null) ? (string) $row['relkind'] : '';
            if ($rel === '') {
                continue;
            }
            $qt = '"' . str_replace('"', '""', $rel) . '"';
            $sql = match ($kind) {
                'S' => "ALTER SEQUENCE {$qs}.{$qt} OWNER TO {$qs}",
                'f' => "ALTER FOREIGN TABLE {$qs}.{$qt} OWNER TO {$qs}",
                'm' => "ALTER MATERIALIZED VIEW {$qs}.{$qt} OWNER TO {$qs}",
                default => "ALTER TABLE {$qs}.{$qt} OWNER TO {$qs}",
            };
            try {
                $oDbl->exec($sql);
            } catch (PDOException $e) {
                $msg = ($e->errorInfo[2] ?? $e->getMessage());
                $sClauError = 'DBRol.realignarPropietariosObjetosEnEsquema.exec '
                    . $esquemaNombre . '.' . $rel . ' (' . $kind . '): ' . $msg;
                $this->reportErrorText(
                    $sClauError,
                    'DBRol.realignarPropietariosObjetosEnEsquema',
                );
                // Vistas/MV/FT pueden fallar sin bloquear tablas; tablas/secuencias se validan después.
                if (in_array($kind, ['r', 'p', 'S'], true)) {
                    return false;
                }
            }
        }

        $stV = $oDbl->prepare(
            'SELECT c.relname, c.relkind FROM pg_class c
             INNER JOIN pg_namespace n ON n.oid = c.relnamespace
             INNER JOIN pg_roles r_own ON r_own.oid = c.relowner
             INNER JOIN pg_roles r_want ON r_want.rolname = :want
             WHERE n.nspname = :s
               AND c.relkind IN (\'r\', \'p\', \'S\')
               AND c.relname NOT LIKE \'pg\_%\' ESCAPE \'\\\'
               AND r_own.oid <> r_want.oid
             LIMIT 5',
        );
        if ($stV === false) {
            $sClauError = 'DBRol.realignarPropietariosObjetosEnEsquema.verifyPrepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);

            return false;
        }
        $stV->execute(['s' => $esquemaNombre, 'want' => $esquemaNombre]);
        $pend = $stV->fetchAll(\PDO::FETCH_ASSOC);
        if ($pend !== []) {
            $nombres = implode(', ', array_map(static function (array $r): string {
                $rel = is_scalar($r['relname'] ?? null) ? (string) $r['relname'] : '';
                $kind = is_scalar($r['relkind'] ?? null) ? (string) $r['relkind'] : '';

                return $rel . '(' . $kind . ')';
            }, $pend));
            $sClauError = 'DBRol.realignarPropietariosObjetosEnEsquema.verify Aún con dueño distinto de «'
                . $esquemaNombre . '»: ' . $nombres;
            $this->reportErrorText(
                $sClauError,
                'DBRol.realignarPropietariosObjetosEnEsquema',
            );

            return false;
        }

        return true;
    }

    /**
     * Concede USAGE del esquema y DML sobre tablas/secuencias existentes al rol homónimo (respaldo si quedaron privilegios desalineados).
     */
    public function concederPrivilegiosRolSobreSuEsquema(string $esquemaNombre): void
    {
        if ($esquemaNombre === '' || preg_match('/[^A-Za-z0-9._-]/', $esquemaNombre)) {
            return;
        }
        $oDbl = $this->getoDbl();
        $q = '"' . str_replace('"', '""', $esquemaNombre) . '"';
        $stmts = [
            "GRANT USAGE ON SCHEMA {$q} TO {$q}",
            "GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA {$q} TO {$q}",
            "GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA {$q} TO {$q}",
        ];
        foreach ($stmts as $sql) {
            try {
                $oDbl->exec($sql);
            } catch (PDOException) {
                // No bloquear el renombre si un GRANT no aplica (p. ej. permisos ya equivalentes).
            }
        }
    }

    public function crearUsuario(): bool
    {
        $oDbl = $this->getoDbl();
        // comprobar antes si existe.
        $sql = "SELECT count(*) FROM pg_roles WHERE rolname='$this->sUser'";

        if (($res = $oDbl->query($sql)) === false) {
            $sClauError = 'DBRol.query.exists';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if ($res->fetchColumn() === 0) {
            $this->sOptions = empty($this->sOptions) ? 'NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN' : $this->sOptions;
            $sql = "CREATE ROLE \"$this->sUser\" PASSWORD '$this->sPwd' $this->sOptions;";

            if (($oDblSt = $oDbl->prepare($sql)) === false) {
                $sClauError = 'DBRol.crear.prepare';
                $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            try {
                $oDblSt->execute();
            } catch (\PDOException $e) {
                $sClauError = 'DBRol.crear.execute';
                $sClauError .= ' ' . ($e->errorInfo[2] ?? $e->getMessage());
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        } else {
            $this->cambiarPassword();
        }

        return true;
    }

    public function renombrarUsuario(string $usuario_old): bool
    {
        $oDbl = $this->getoDbl();
        $this->sOptions = empty($this->sOptions) ? 'NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN' : $this->sOptions;

        $sql = "ALTER ROLE \"$usuario_old\" RENAME TO \"$this->sUser\" ";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.crear.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }

        $reintentoTrasConflicto = false;
        while (true) {
            try {
                if ($oDblSt->execute() === false) {
                    $sClauError = 'DBRol.renombrarUsuario.execute';
                    $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
                break;
            } catch (PDOException $e) {
                if (
                    !$reintentoTrasConflicto
                    && $this->esErrorRolDestinoYaExiste($e, $this->sUser)
                ) {
                    $this->eliminarRolConflicto($this->sUser);
                    $msgAviso = sprintf(
                        _('El rol "%s" ya existía en esta base de datos; se ha eliminado (DROP OWNED + DROP ROLE) y se ha aplicado el renombre desde "%s".'),
                        $this->sUser,
                        $usuario_old
                    );
                    $this->avisosRenameRol[] = $msgAviso;
                    $this->reportErrorText(
                        $msgAviso,
                        'DBRol.renombrarUsuario.rol_duplicado_remediado',
                    );
                    $reintentoTrasConflicto = true;
                    $oDblSt = $oDbl->prepare($sql);
                    continue;
                }
                $sClauError = 'DBRol.crear.execute';
                $sClauError .= ' ' . ($e->errorInfo[2] ?? $e->getMessage());
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }

        /* Because MD5-encrypted passwords use the role name as cryptographic salt,
         * renaming a role clears its password if the password is MD5-encrypted.
         */
        $this->cambiarPassword();

        $sql = "ALTER ROLE \"$this->sUser\" SET search_path TO '$this->sUser', 'public'; ";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.crear.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        try {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.renombrarUsuario.search_path';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        } catch (PDOException $e) {
            $sClauError = 'DBRol.crear.execute';
            $sClauError .= ' ' . ($e->errorInfo[2] ?? $e->getMessage());
            $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }

        if ($this->existeEsquemaEnCluster($this->sUser)) {
            return $this->repararEsquemaPostRenombre($this->sUser);
        }

        return true;
    }

    private function existeEsquemaEnCluster(string $nombreEsquema): bool
    {
        if ($nombreEsquema === '') {
            return false;
        }
        $oDbl = $this->getoDbl();
        $st = $oDbl->prepare('SELECT 1 FROM pg_namespace WHERE nspname = :n LIMIT 1');
        if ($st === false) {
            return false;
        }
        try {
            $st->execute(['n' => $nombreEsquema]);
        } catch (PDOException) {
            return false;
        }

        return (bool) $st->fetchColumn();
    }

    /**
     * @return list<string>
     */
    public function consumirAvisosRenameRol(): array
    {
        $a = $this->avisosRenameRol;
        $this->avisosRenameRol = [];

        return $a;
    }

    private function esErrorRolDestinoYaExiste(PDOException $e, string $nombreRolDestino): bool
    {
        $msg = strtolower((string) ($e->errorInfo[2] ?? $e->getMessage()));
        if (!str_contains($msg, 'role') || !str_contains($msg, strtolower($nombreRolDestino))) {
            return false;
        }
        $sqlState = (string) ($e->errorInfo[0] ?? '');
        if ($sqlState === '42710') {
            return true;
        }

        return str_contains($msg, 'already exists');
    }

    /**
     * Elimina un rol huérfano que choca con el nombre destino del renombre (DROP OWNED … CASCADE; DROP ROLE).
     */
    private function eliminarRolConflicto(string $rol): void
    {
        if ($rol === '' || preg_match('/[^A-Za-z0-9._-]/', $rol)) {
            return;
        }
        $q = '"' . str_replace('"', '""', $rol) . '"';
        $oDbl = $this->getoDbl();
        foreach (["DROP OWNED BY {$q} CASCADE", "DROP ROLE IF EXISTS {$q}"] as $sqlDrop) {
            $st = $oDbl->prepare($sqlDrop);
            if ($st === false) {
                continue;
            }
            try {
                $st->execute();
            } catch (PDOException) {
                // Siguiente sentencia o reintento de RENAME decidirá.
            }
        }
    }

    public function eliminarSchema(): bool
    {
        $oDbl = $this->getoDbl();
        $sql = "DROP SCHEMA IF EXISTS \"$this->sUser\" CASCADE";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.eliminarSchema.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.eliminarSchema.execute';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }

        return true;
    }

    public function eliminarUsuario(): void
    {
        $error = $this->intentarEliminarUsuario();
        if ($error !== null) {
            $sClauError = 'DBRol.eliminar.execute ' . $error;
            $this->reportPdoError($this->getoDbl(), $sClauError, __LINE__, __FILE__);
        }
    }

    /**
     * DROP OWNED en cada BD del cluster y DROP ROLE (conexión de mantenimiento).
     *
     * @param list<string>|null $clavesImportar plantillas importar; null = todas las BDs de devel_db_admin
     * @return string|null mensaje de error si el rol sigue existiendo o no se pudo borrar; null si ok
     */
    public function intentarEliminarUsuario(?array $clavesImportar = null): ?string
    {
        if ($this->sUser === '' || preg_match('/[^A-Za-z0-9._-]/', $this->sUser)) {
            return _('Nombre de rol no válido.');
        }

        $claves = $clavesImportar ?? self::clavesImportarParaDropOwned();
        $this->limpiarDependenciasUsuario($claves);

        $q = '"' . str_replace('"', '""', $this->sUser) . '"';
        $erroresDrop = [];
        $oConfigDB = new ConfigDB('importar');

        foreach ($claves as $clave) {
            try {
                $pdo = (new DBConnection($oConfigDB->getConexionMantenimiento($clave)))->getPDO();
                try {
                    $pdo->exec("DROP ROLE IF EXISTS {$q}");
                } catch (\PDOException $e) {
                    $erroresDrop[] = "{$clave}: {$e->getMessage()}";
                }
            } catch (\Throwable $e) {
                $erroresDrop[] = "{$clave}: {$e->getMessage()}";
            }
        }

        $instanciasPendientes = $this->clavesDondeRolExiste($claves);
        if ($instanciasPendientes !== []) {
            $detalle = implode(', ', $instanciasPendientes);
            if ($erroresDrop !== []) {
                return $detalle . ' (' . implode('; ', $erroresDrop) . ')';
            }

            return sprintf(_('El rol sigue en: %s'), $detalle);
        }

        if ($erroresDrop !== []) {
            return implode('; ', $erroresDrop);
        }

        return null;
    }

    private function rolExisteEnConexion(\PDO $pdo): bool
    {
        $st = $pdo->prepare('SELECT 1 FROM pg_roles WHERE rolname = :rol LIMIT 1');
        if ($st === false) {
            return false;
        }
        $st->execute(['rol' => $this->sUser]);

        return (bool) $st->fetchColumn();
    }

    /**
     * @param list<string> $clavesImportar
     */
    private function limpiarDependenciasUsuario(array $clavesImportar): void
    {
        $oConfigDB = new ConfigDB('importar');
        $q = '"' . str_replace('"', '""', $this->sUser) . '"';

        foreach ($clavesImportar as $clave) {
            try {
                $oDbl = (new DBConnection($oConfigDB->getConexionMantenimiento($clave)))->getPDO();
                $this->limpiarDependenciasEnConexion($oDbl, $q);
            } catch (\Throwable) {
                // Best effort en cada BD / instancia (origen, réplica…).
            }
        }
    }

    private function limpiarDependenciasEnConexion(\PDO $pdo, string $qRol): void
    {
        $this->revocarPrivilegiosRestoEnConexion($pdo, $this->sUser);
        $this->revocarMembresiasOrbixEnConexion($pdo, $qRol);
        try {
            $pdo->exec("REASSIGN OWNED BY {$qRol} TO CURRENT_USER");
        } catch (\Throwable) {
        }
        $pdo->exec("DROP OWNED BY {$qRol} CASCADE");
    }

    private function revocarMembresiasOrbixEnConexion(\PDO $pdo, string $qRol): void
    {
        foreach (['orbix', 'orbixv', 'orbixf'] as $grupo) {
            $qGrupo = '"' . str_replace('"', '""', $grupo) . '"';
            try {
                $pdo->exec("REVOKE {$qGrupo} FROM {$qRol}");
            } catch (\Throwable) {
                // El rol puede no ser miembro de este grupo en esta instancia.
            }
        }
    }

    /**
     * @param list<string> $clavesImportar
     * @return list<string>
     */
    private function clavesDondeRolExiste(array $clavesImportar): array
    {
        $oConfigDB = new ConfigDB('importar');
        $pendientes = [];

        foreach ($clavesImportar as $clave) {
            try {
                $pdo = (new DBConnection($oConfigDB->getConexionMantenimiento($clave)))->getPDO();
                if ($this->rolExisteEnConexion($pdo)) {
                    $pendientes[] = $clave;
                }
            } catch (\Throwable) {
                // No se pudo comprobar esta instancia.
            }
        }

        return $pendientes;
    }

    /** Revoca GRANT en esquemas resto/restov/restof (tras dl2resto) antes de DROP ROLE. */
    private function revocarPrivilegiosRestoEnConexion(\PDO $pdo, string $rol): void
    {
        if ($rol === '' || preg_match('/[^A-Za-z0-9._-]/', $rol)) {
            return;
        }

        $qRol = '"' . str_replace('"', '""', $rol) . '"';
        foreach (['resto', 'restov', 'restof'] as $esquemaResto) {
            $qEsquema = '"' . str_replace('"', '""', $esquemaResto) . '"';
            foreach ([
                "REVOKE ALL PRIVILEGES ON ALL TABLES IN SCHEMA {$qEsquema} FROM {$qRol}",
                "REVOKE ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA {$qEsquema} FROM {$qRol}",
                "REVOKE ALL PRIVILEGES ON SCHEMA {$qEsquema} FROM {$qRol}",
            ] as $sql) {
                try {
                    $pdo->exec($sql);
                } catch (\Throwable) {
                    // El esquema resto* puede no existir en esta BD.
                }
            }
        }
    }

    /** @return list<string> */
    private static function clavesImportarParaDropOwned(): array
    {
        $claves = ['public', 'publicv', 'publicf', 'publicv-e', 'publicf-e'];
        if (!preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR)) {
            $claves[] = 'public_select';
            $claves[] = 'publicv-e_select';
        }

        return $claves;
    }

    private function cambiarPassword(): bool
    {
        $oDbl = $this->getoDbl();

        $sql = "ALTER USER \"$this->sUser\" WITH PASSWORD '$this->sPwd';";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.pwd.prepare';
            $this->reportPdoError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.pwd.execute';
                $this->reportPdoError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }

        return true;
    }

    private function gestorErrores(): ?GestorErrores
    {
        $gestor = $_SESSION['oGestorErrores'] ?? null;

        return $gestor instanceof GestorErrores ? $gestor : null;
    }

    private function reportPdoError(PDO|PDOStatement $source, string $key, int|string $line, string $file): void
    {
        $gestor = $this->gestorErrores();
        if ($gestor !== null) {
            $gestor->addErrorAppLastError($source, $key, (string) $line, $file);
        }
    }

    private function reportErrorText(string $text, string $key): void
    {
        $gestor = $this->gestorErrores();
        if ($gestor !== null) {
            $gestor->addErrorAppLastErrorNoThrowText($text, $key, (string) __LINE__, __FILE__);
        }
    }
}
