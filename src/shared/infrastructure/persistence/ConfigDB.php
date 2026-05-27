<?php

namespace src\shared\infrastructure\persistence;

use RuntimeException;
use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;

/**
 * Conexión a base de datos y contraseñas por esquema (rol PostgreSQL).
 *
 * Formato partido (recomendado, mismo cluster pruebas/producción):
 * - `{base}.roles.inc` — claves esquema → user/password (único por cluster; sin prefijo pruebas-).
 * - `{prefijo}{base}.conn.inc` — bloque `default` (host, dbname…); `prefijo` = `pruebas-` solo en WEBDIR pruebas.
 *
 * Formato legado: un solo `{prefijo}{base}.inc` con `default` + esquemas (sigue soportado).
 */
class ConfigDB
{
    /** @internal Solo tests: directorio alternativo para `.inc` */
    public static ?string $dirPwdOverride = null;

    /** Réplicas / sv-e comparten el mismo mapa de roles que la base indicada. */
    private const ROLES_BASE_MAP = [
        'comun_select' => 'comun',
        'sv-e' => 'sv',
        'sv-e_select' => 'sv',
    ];

    /** @var array<string, mixed> */
    private array $data = [];

    private string $baseLogico = '';

    /** @param string $database p. ej. `comun`, `sv-e`, `comun_select` (sin prefijo pruebas-) */
    public function __construct($database)
    {
        $this->setDataBase($database);
    }

    public function getBaseLogico(): string
    {
        return $this->baseLogico;
    }

    public function tieneEsquema(string $esquema): bool
    {
        return array_key_exists($esquema, $this->data)
            && is_array($this->data[$esquema])
            && (isset($this->data[$esquema]['user']) || isset($this->data[$esquema]['password']));
    }

    /**
     * Ruta del fichero donde se definen user/password de esquemas (`.roles.inc` o monolito `.inc`).
     */
    public static function rutaFicheroEntradaEsquema(string $ficheroBase): string
    {
        $base = self::normalizarBaseLogico($ficheroBase);
        if (self::usaFormatoPartido($base)) {
            return self::dirPwd() . '/' . self::ficheroRolesNombre($base);
        }

        return self::rutaMonolitico($base);
    }

    /**
     * Indica el fichero donde conviene añadir la clave (conn para `public*`, roles para región–dl).
     */
    public static function rutaDondeAnadirEsquema(string $ficheroBase, string $esquema): string
    {
        if (str_starts_with($esquema, 'public')) {
            $connBase = match ($esquema) {
                'publicv', 'publicv-e' => 'sv',
                'publicf', 'publicf-e' => 'sf',
                default => self::normalizarBaseLogico($ficheroBase),
            };

            return self::dirPwd() . '/' . self::ficheroConnNombre($connBase);
        }

        if (preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+v$/', $esquema) === 1) {
            return self::rutaFicheroEntradaEsquema('sv');
        }
        if (preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+f$/', $esquema) === 1) {
            return self::rutaFicheroEntradaEsquema('sf-e');
        }
        if (preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+$/', $esquema) === 1) {
            return self::rutaFicheroEntradaEsquema('comun');
        }

        return self::rutaFicheroEntradaEsquema($ficheroBase);
    }

    public static function mensajeEsquemaConexionFaltante(string $ficheroBase, string $esquema): string
    {
        $rutaSugerida = self::rutaDondeAnadirEsquema($ficheroBase, $esquema);
        $encontrado = self::localizarEsquemaEnFicheros($esquema);
        if ($encontrado !== null && $encontrado !== $rutaSugerida) {
            return sprintf(
                _('El esquema «%1$s» está en %2$s pero no se cargó en la configuración «%3$s»; unifique la entrada en %4$s o corrija el formato (user/password).'),
                $esquema,
                $encontrado,
                $ficheroBase,
                $rutaSugerida,
            );
        }

        $msg = sprintf(
            _('Hay que añadir los parámetros de conexión para el esquema «%1$s» en %2$s'),
            $esquema,
            $rutaSugerida,
        );
        $baseMono = self::ficheroBaseRolesParaEsquemaRegionDl($esquema)
            ?? self::normalizarBaseLogico($ficheroBase);
        if (self::usaFormatoPartido($baseMono)) {
            $mono = self::dirPwd() . '/' . $baseMono . '.inc';
            if ($mono !== $rutaSugerida && is_readable($mono)) {
                $msg .= ' ' . sprintf(_('(si ya está en %s, se incorporará al recargar)'), $mono);
            }
        }
        if (preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+[vf]?$/', $esquema) === 1) {
            $msg .= ' ' . _('(suele hacerlo el paso «1º crear usuarios»).');
        }

        return $msg;
    }

    /**
     * Mismo texto que {@see mensajeEsquemaConexionFaltante}, prefijado para listas de avisos no bloqueantes.
     */
    public static function mensajeAvisoEsquemaConexionFaltante(
        string $ficheroBase,
        string $esquema,
        string $sufijo = '',
    ): string {
        return _('Aviso:') . ' ' . self::mensajeEsquemaConexionFaltante($ficheroBase, $esquema) . $sufijo;
    }

    /**
     * Base lógica del `.roles.inc` para esquemas región–dl (`B-xx`, `B-xxv`, `B-xxf`).
     */
    public static function ficheroBaseRolesParaEsquemaRegionDl(string $esquema): ?string
    {
        if (preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+v$/', $esquema) === 1) {
            return 'sv';
        }
        if (preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+f$/', $esquema) === 1) {
            return 'sf-e';
        }
        if (preg_match('/^[A-Za-z0-9]+-[A-Za-z0-9]+$/', $esquema) === 1) {
            return 'comun';
        }

        return null;
    }

    public function mensajeEsquemaFaltante(string $esquema): string
    {
        return self::mensajeEsquemaConexionFaltante($this->baseLogico, $esquema);
    }

    public function getEsquema($esquema)
    {
        $data = $this->data['default'];
        $data['schema'] = $esquema;
        if (!$this->tieneEsquema($esquema)) {
            throw new RunTimeException($this->mensajeEsquemaFaltante((string) $esquema));
        }
        foreach ($this->data[$esquema] as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Conexión de mantenimiento (postgres / importar): host y BD de la plantilla `public*`, credenciales del
     * bloque conn o `default` (nunca de un rol región–dl en `.roles.inc`).
     *
     * @param string $claveImportar p. ej. `public`, `publicv`, `publicv-e`, `publicf`
     * @return array<string, mixed>
     */
    public function getConexionMantenimiento(string $claveImportar): array
    {
        if (!isset($this->data['default']) || !is_array($this->data['default'])) {
            throw new RuntimeException(sprintf(
                _('Falta el bloque default en la configuración importar (%s).'),
                $this->baseLogico,
            ));
        }

        $out = $this->data['default'];
        $plantilla = $this->plantillaConnImportar($claveImportar);

        foreach (['host', 'port', 'dbname', 'sslmode', 'sslcert', 'sslkey', 'sslrootcert', 'ssh_user'] as $clave) {
            if (isset($plantilla[$clave])) {
                $out[$clave] = $plantilla[$clave];
            }
        }

        foreach (['user', 'password'] as $clave) {
            if (isset($out[$clave]) && $out[$clave] !== '') {
                continue;
            }
            if (isset($plantilla[$clave]) && $plantilla[$clave] !== '') {
                $out[$clave] = $plantilla[$clave];
            }
        }

        $out['schema'] = 'public';

        if (!isset($out['user'], $out['password']) || $out['user'] === '' || $out['password'] === '') {
            throw new RuntimeException(sprintf(
                _('Faltan user/password de mantenimiento en importar (%1$s, plantilla «%2$s»). Revise default y %3$s.'),
                $this->baseLogico,
                $claveImportar,
                self::ficheroConnNombre($this->baseLogico),
            ));
        }

        if (!isset($out['port']) || $out['port'] === '') {
            $out['port'] = 5432;
        }

        return $out;
    }

    /**
     * Conexión importar hacia réplica (comun_select / sv-e_select).
     * Mismo criterio que {@see \src\devel_db_admin\application\CrearEsquema}.
     *
     * @param 'public_select'|'publicv-e_select' $claveImportar
     * @return array<string, mixed>
     */
    public function getConexionImportarReplica(string $claveImportar): array
    {
        $config = $this->getConexionMantenimiento($claveImportar);
        $config = $this->completarConnReplicaSiAmbigua($claveImportar, $config);
        $config['schema'] = $claveImportar;

        return $config;
    }

    /**
     * Si la plantilla réplica apunta al mismo host/dbname que default, buscar en getEsquema,
     * comun.conn.inc o monolitos *\_select.inc (mismo criterio operativo que CrearEsquema).
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function completarConnReplicaSiAmbigua(string $claveImportar, array $config): array
    {
        $default = is_array($this->data['default'] ?? null) ? $this->data['default'] : [];
        if (!$this->connReplicaAmbigua($config, $default)) {
            return $config;
        }

        if ($this->tieneEsquema($claveImportar)) {
            try {
                $config = $this->fusionarConnPrefer($config, $this->getEsquema($claveImportar));
            } catch (RuntimeException) {
                // seguir con otros fallbacks
            }
        }

        if ($this->connReplicaAmbigua($config, $default)) {
            $config = $this->fusionarPlantillaConnOtraBase($claveImportar, $config);
        }

        if ($this->connReplicaAmbigua($config, $default)) {
            $config = $this->fusionarMonolitoSelect($claveImportar, $config);
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $default
     */
    private function connReplicaAmbigua(array $config, array $default): bool
    {
        $hostConfig = (string) ($config['host'] ?? '');
        $hostDefault = (string) ($default['host'] ?? '');
        $dbConfig = (string) ($config['dbname'] ?? '');
        $dbDefault = (string) ($default['dbname'] ?? '');

        return ($hostConfig === '' || $hostConfig === $hostDefault)
            && ($dbConfig === '' || $dbConfig === $dbDefault);
    }

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $overlay
     * @return array<string, mixed>
     */
    private function fusionarConnPrefer(array $base, array $overlay): array
    {
        foreach (['host', 'port', 'dbname', 'user', 'password', 'sslmode', 'sslcert', 'sslkey', 'sslrootcert', 'ssh_user'] as $clave) {
            if (isset($overlay[$clave]) && $overlay[$clave] !== '') {
                $base[$clave] = $overlay[$clave];
            }
        }

        return $base;
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function fusionarPlantillaConnOtraBase(string $claveImportar, array $config): array
    {
        $baseConn = match ($claveImportar) {
            'public_select' => 'comun',
            'publicv-e_select' => 'sv',
            default => null,
        };
        if ($baseConn === null) {
            return $config;
        }

        $connPath = self::dirPwd() . '/' . self::ficheroConnNombre($baseConn);
        if (!is_readable($connPath)) {
            return $config;
        }

        $conn = self::cargarArrayInc($connPath);
        if (!is_array($conn[$claveImportar] ?? null)) {
            return $config;
        }

        return $this->fusionarConnPrefer($config, $conn[$claveImportar]);
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function fusionarMonolitoSelect(string $claveImportar, array $config): array
    {
        $fichero = match ($claveImportar) {
            'public_select' => 'comun_select',
            'publicv-e_select' => 'sv-e_select',
            default => null,
        };
        if ($fichero === null) {
            return $config;
        }

        foreach ([$fichero, 'pruebas-' . $fichero] as $nombre) {
            $path = self::dirPwd() . '/' . $nombre . '.inc';
            if (!is_readable($path)) {
                continue;
            }
            $data = self::cargarArrayInc($path);
            $block = is_array($data['default'] ?? null) ? $data['default'] : $data;

            return $this->fusionarConnPrefer($config, $block);
        }

        return $config;
    }

    /**
     * Bloque `public*` del fichero `.conn.inc` (sin roles DL); complemento desde datos mergeados si hace falta.
     *
     * @return array<string, mixed>
     */
    private function plantillaConnImportar(string $claveImportar): array
    {
        $desdeConn = [];
        if (self::usaFormatoPartido($this->baseLogico)) {
            $conn = self::cargarArrayInc(self::dirPwd() . '/' . self::ficheroConnNombre($this->baseLogico));
            if (is_array($conn[$claveImportar] ?? null)) {
                $desdeConn = $conn[$claveImportar];
            }
        }

        $desdeMerged = $this->data[$claveImportar] ?? [];
        if (!is_array($desdeMerged)) {
            $desdeMerged = [];
        }

        $out = [];
        foreach (['host', 'port', 'dbname', 'user', 'password', 'sslmode', 'sslcert', 'sslkey', 'sslrootcert', 'ssh_user'] as $clave) {
            if (isset($desdeConn[$clave])) {
                $out[$clave] = $desdeConn[$clave];
            } elseif (isset($desdeMerged[$clave])) {
                $out[$clave] = $desdeMerged[$clave];
            }
        }

        return $out;
    }

    /**
     * @param string $database nombre lógico (`comun`, `sv-e`, …) con o sin prefijo `pruebas-`
     */
    public function setDataBase($database): void
    {
        $base = self::normalizarBaseLogico($database);
        $this->baseLogico = $base;
        $baseRoles = self::baseRolesParaFichero($base);
        if (self::usaFormatoPartido($baseRoles)) {
            $this->data = self::cargarDatosMergeados($base);

            return;
        }

        $archivo = $base;
        if (ConfigGlobal::WEBDIR === 'pruebas' && !str_starts_with($archivo, 'pruebas-')) {
            $archivo = 'pruebas-' . $archivo;
        }
        $path = self::dirPwd() . '/' . $archivo . '.inc';
        $this->data = self::cargarArrayInc($path);
    }

    /**
     * Nombre del fichero que se muestra al comprobar claves de esquema (roles en formato partido).
     */
    public static function ficheroIncNombre(string $baseSinExtension): string
    {
        if (self::usaFormatoPartido($baseSinExtension)) {
            return self::ficheroRolesNombre($baseSinExtension);
        }

        if (ConfigGlobal::WEBDIR === 'pruebas') {
            return 'pruebas-' . $baseSinExtension . '.inc';
        }

        return $baseSinExtension . '.inc';
    }

    public static function ficheroConnNombre(string $baseSinExtension): string
    {
        $prefijo = ConfigGlobal::WEBDIR === 'pruebas' ? 'pruebas-' : '';

        return $prefijo . $baseSinExtension . '.conn.inc';
    }

    public static function ficheroRolesNombre(string $ficheroBase): string
    {
        return self::baseRolesParaFichero($ficheroBase) . '.roles.inc';
    }

    public static function baseRolesParaFichero(string $ficheroBase): string
    {
        return self::ROLES_BASE_MAP[$ficheroBase] ?? $ficheroBase;
    }

    public static function usaFormatoPartido(string $ficheroBase): bool
    {
        $path = self::dirPwd() . '/' . self::ficheroRolesNombre($ficheroBase);

        return is_readable($path);
    }

    /** @return list<string> */
    public static function clavesEnFicheroRoles(string $ficheroBase): array
    {
        if (self::usaFormatoPartido($ficheroBase)) {
            return self::clavesDesdeArchivo(self::dirPwd() . '/' . self::ficheroRolesNombre($ficheroBase));
        }

        $archivo = $ficheroBase;
        if (ConfigGlobal::WEBDIR === 'pruebas') {
            $archivo = 'pruebas-' . $archivo;
        }

        return self::clavesDesdeArchivo(self::dirPwd() . '/' . $archivo . '.inc');
    }

    /**
     * Añade usuario/password del esquema. En formato partido: un solo `.roles.inc` (cluster compartido).
     */
    public function addEsquemaEnFicheroPasswords($database, $esquema, $esquema_pwd): void
    {
        $base = self::normalizarBaseLogico((string) $database);
        if (self::usaFormatoPartido($base)) {
            $this->escribirClaveEnRoles($base, $esquema, $esquema_pwd);

            return;
        }

        $this->addEsquemaMonolitico($base, $esquema, $esquema_pwd);
        if (!preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR)) {
            $this->addEsquemaMonolitico('pruebas-' . $base, $esquema, $esquema_pwd);
        }
    }

    /**
     * Quita usuario/password del esquema en los mismos ficheros que {@see addEsquemaEnFicheroPasswords}.
     */
    public function removeEsquemaEnFicheroPasswords($database, string $esquema): void
    {
        $base = self::normalizarBaseLogico((string) $database);
        if (self::usaFormatoPartido($base)) {
            $this->eliminarClaveEnRoles($base, $esquema);

            return;
        }

        $this->removeEsquemaMonolitico($base, $esquema);
        if (!preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR)) {
            $this->removeEsquemaMonolitico('pruebas-' . $base, $esquema);
        }
    }

    /**
     * Renombra clave de esquema en `.roles.inc` (formato partido) o en ambos monolitos prod/pruebas (legado).
     */
    public function renombrarListaEsquema($database, $esquema_old, $esquema_new): void
    {
        $base = self::normalizarBaseLogico((string) $database);
        if (self::usaFormatoPartido($base)) {
            $this->renombrarClaveEnRoles($base, $esquema_old, $esquema_new);

            return;
        }

        $this->renombrarClaveEnMonolitico($base, $esquema_old, $esquema_new);
        if (!preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR)) {
            $this->renombrarClaveEnMonolitico('pruebas-' . $base, $esquema_old, $esquema_new);
        }
    }

    /**
     * Genera `{base}.roles.inc` y `.conn.inc` (prod y pruebas) desde monolitos existentes.
     * Idempotente: no sobrescribe ficheros que ya existen.
     *
     * @return list<string> mensajes descriptivos
     */
    public static function crearFicherosPartidosDesdeMonoliticos(string $baseLogico): array
    {
        $msgs = [];
        $base = self::normalizarBaseLogico($baseLogico);
        $dir = self::dirPwd();
        $rolesPath = $dir . '/' . self::ficheroRolesNombre($base);

        if (is_readable($rolesPath)) {
            $msgs[] = sprintf('Ya existe %s', self::ficheroRolesNombre($base));
        } else {
            $roles = [];
            $fuentes = [$base . '.inc', 'pruebas-' . $base . '.inc'];
            if ($base === 'comun') {
                $fuentes[] = 'comun_select.inc';
            }
            if ($base === 'sv') {
                $fuentes[] = 'sv-e.inc';
                $fuentes[] = 'sv-e_select.inc';
            }
            foreach ($fuentes as $nombre) {
                foreach (self::extraerEntradasEsquema(self::cargarArrayInc($dir . '/' . $nombre)) as $clave => $valor) {
                    if (!isset($roles[$clave])) {
                        $roles[$clave] = $valor;
                    }
                }
            }
            if ($roles === []) {
                $msgs[] = sprintf('Sin entradas de esquema en monolitos para %s', $base);
            } else {
                file_put_contents($rolesPath, '<?php return ' . var_export($roles, true) . ' ;');
                $msgs[] = sprintf('Creado %s', self::ficheroRolesNombre($base));
            }
        }

        $connProd = $dir . '/' . $base . '.conn.inc';
        if (!is_readable($connProd)) {
            $prod = self::cargarArrayInc($dir . '/' . $base . '.inc');
            if (isset($prod['default']) && is_array($prod['default'])) {
                file_put_contents($connProd, '<?php return ' . var_export(['default' => $prod['default']], true) . ' ;');
                $msgs[] = sprintf('Creado %s.conn.inc', $base);
            }
        }

        $connPruebas = $dir . '/pruebas-' . $base . '.conn.inc';
        if (!is_readable($connPruebas)) {
            $pruebas = self::cargarArrayInc($dir . '/pruebas-' . $base . '.inc');
            if (isset($pruebas['default']) && is_array($pruebas['default'])) {
                file_put_contents($connPruebas, '<?php return ' . var_export(['default' => $pruebas['default']], true) . ' ;');
                $msgs[] = sprintf('Creado pruebas-%s.conn.inc', $base);
            }
        }

        return $msgs;
    }

    /**
     * @return array<string, mixed>
     */
    private static function cargarDatosMergeados(string $baseLogico): array
    {
        $conn = self::cargarArrayInc(self::dirPwd() . '/' . self::ficheroConnNombre($baseLogico));

        foreach (self::rutasMonoliticoComplementarias($baseLogico) as $legacyPath) {
            if (!is_readable($legacyPath)) {
                continue;
            }
            $legacy = self::cargarArrayInc($legacyPath);
            if ($conn === [] && isset($legacy['default']) && is_array($legacy['default'])) {
                $conn['default'] = $legacy['default'];
            } elseif ($conn === [] && isset($legacy['default'])) {
                $conn['default'] = $legacy['default'];
            }
        }

        $baseRoles = self::baseRolesParaFichero($baseLogico);
        $rolesPath = self::dirPwd() . '/' . self::ficheroRolesNombre($baseRoles);
        $roles = self::cargarArrayInc($rolesPath);

        $merged = $conn;
        foreach ($roles as $clave => $valor) {
            if ($clave === 'default') {
                continue;
            }
            $merged[$clave] = $valor;
        }

        foreach (self::rutasMonoliticoComplementarias($baseLogico) as $legacyPath) {
            if (!is_readable($legacyPath)) {
                continue;
            }
            $legacy = self::cargarArrayInc($legacyPath);
            foreach (self::extraerEntradasEsquema($legacy) as $clave => $valor) {
                if (!isset($merged[$clave])) {
                    $merged[$clave] = $valor;
                }
            }
        }

        return $merged;
    }

    /**
     * @return list<string>
     */
    private static function rutasMonoliticoComplementarias(string $baseLogico): array
    {
        $paths = [self::dirPwd() . '/' . $baseLogico . '.inc'];
        if (ConfigGlobal::WEBDIR === 'pruebas') {
            $paths[] = self::dirPwd() . '/pruebas-' . $baseLogico . '.inc';
        }

        $baseRoles = self::baseRolesParaFichero($baseLogico);
        if ($baseRoles !== $baseLogico) {
            $paths[] = self::dirPwd() . '/' . $baseRoles . '.inc';
            if (ConfigGlobal::WEBDIR === 'pruebas') {
                $paths[] = self::dirPwd() . '/pruebas-' . $baseRoles . '.inc';
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * Devuelve la ruta del primer fichero legible que contiene la clave (diagnóstico).
     */
    public static function localizarEsquemaEnFicheros(string $esquema): ?string
    {
        foreach (['comun', 'sv', 'sf', 'sf-e'] as $base) {
            $rolesPath = self::dirPwd() . '/' . self::ficheroRolesNombre($base);
            if (is_readable($rolesPath)) {
                $roles = self::cargarArrayInc($rolesPath);
                if (isset($roles[$esquema]) || isset(self::extraerEntradasEsquema($roles)[$esquema])) {
                    return $rolesPath;
                }
            }
            foreach (self::rutasMonoliticoComplementarias($base) as $path) {
                if (!is_readable($path)) {
                    continue;
                }
                $data = self::cargarArrayInc($path);
                if (isset(self::extraerEntradasEsquema($data)[$esquema])) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, array{user?: string, password?: string}>
     */
    private static function extraerEntradasEsquema(array $data): array
    {
        $out = [];
        foreach ($data as $clave => $valor) {
            if ($clave === 'default' || !is_array($valor)) {
                continue;
            }
            if (isset($valor['password']) || isset($valor['user'])) {
                $out[$clave] = $valor;
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    private static function cargarArrayInc(string $path): array
    {
        if (!is_readable($path)) {
            return [];
        }
        try {
            $loaded = include $path;
        } catch (\Throwable) {
            return [];
        }

        return is_array($loaded) ? $loaded : [];
    }

    private static function rutaMonolitico(string $baseLogico): string
    {
        $archivo = $baseLogico;
        if (ConfigGlobal::WEBDIR === 'pruebas') {
            $archivo = 'pruebas-' . $archivo;
        }

        return self::dirPwd() . '/' . $archivo . '.inc';
    }

    private static function dirPwd(): string
    {
        if (self::$dirPwdOverride !== null) {
            return self::$dirPwdOverride;
        }

        return ConfigGlobal::getDIR_PWD();
    }

    private static function normalizarBaseLogico(string $database): string
    {
        if (str_starts_with($database, 'pruebas-')) {
            return substr($database, strlen('pruebas-'));
        }

        return $database;
    }

    /** @return list<string> */
    private static function clavesDesdeArchivo(string $path): array
    {
        $data = self::cargarArrayInc($path);
        $keys = [];
        foreach (array_keys($data) as $clave) {
            if ($clave !== 'default' && is_string($clave)) {
                $keys[] = $clave;
            }
        }

        return $keys;
    }

    private function escribirClaveEnRoles(string $ficheroBase, string $esquema, string $esquema_pwd): void
    {
        $path = self::dirPwd() . '/' . self::ficheroRolesNombre($ficheroBase);
        $data = self::cargarArrayInc($path);
        $data[$esquema] = ['user' => $esquema, 'password' => $esquema_pwd];
        $this->guardarArrayInc($path, $data);
    }

    private function renombrarClaveEnRoles(string $ficheroBase, string $esquema_old, string $esquema_new): void
    {
        $path = self::dirPwd() . '/' . self::ficheroRolesNombre($ficheroBase);
        $data = self::cargarArrayInc($path);

        if (!isset($data[$esquema_old]) || !is_array($data[$esquema_old]) || !isset($data[$esquema_old]['password'])) {
            return;
        }

        $pwd = $data[$esquema_old]['password'];
        unset($data[$esquema_old]);
        $data[$esquema_new] = ['user' => $esquema_new, 'password' => $pwd];
        $this->guardarArrayInc($path, $data);
    }

    private function addEsquemaMonolitico(string $database, string $esquema, string $esquema_pwd): void
    {
        $entrada = ['user' => $esquema, 'password' => $esquema_pwd];
        $this->escribirEntradaMonolito($database, $esquema, $entrada);
        $select = self::nombreMonolitoSelectPar($database);
        if ($select !== null) {
            $this->escribirEntradaMonolito($select, $esquema, $entrada);
        }
    }

    private function removeEsquemaMonolitico(string $database, string $esquema): void
    {
        $this->eliminarEntradaMonolito($database, $esquema);
        $select = self::nombreMonolitoSelectPar($database);
        if ($select !== null) {
            $this->eliminarEntradaMonolito($select, $esquema);
        }
    }

    private function renombrarClaveEnMonolitico(string $database, string $esquema_old, string $esquema_new): void
    {
        if (!$this->renombrarEntradaMonolito($database, $esquema_old, $esquema_new)) {
            return;
        }

        $select = self::nombreMonolitoSelectPar($database);
        if ($select !== null) {
            $this->renombrarEntradaMonolito($select, $esquema_old, $esquema_new);
        }
    }

    /**
     * Par réplica monolito: `comun`↔`comun_select`, `pruebas-comun`↔`pruebas-comun_select`, igual sv-e.
     */
    private static function nombreMonolitoSelectPar(string $database): ?string
    {
        $norm = self::normalizarBaseLogico($database);

        if ($norm !== 'comun' && $norm !== 'sv-e') {
            return null;
        }

        return $database . '_select';
    }

    /** @param array{user: string, password: string} $entrada */
    private function escribirEntradaMonolito(string $database, string $esquema, array $entrada): void
    {
        $path = self::dirPwd() . '/' . $database . '.inc';
        $data = self::cargarArrayInc($path);
        $data[$esquema] = $entrada;
        $this->guardarArrayInc($path, $data);
    }

    private function eliminarEntradaMonolito(string $database, string $esquema): void
    {
        $path = self::dirPwd() . '/' . $database . '.inc';
        $data = self::cargarArrayInc($path);
        if (!isset($data[$esquema])) {
            return;
        }
        unset($data[$esquema]);
        $this->guardarArrayInc($path, $data);
    }

    private function renombrarEntradaMonolito(string $database, string $esquema_old, string $esquema_new): bool
    {
        $path = self::dirPwd() . '/' . $database . '.inc';
        $data = self::cargarArrayInc($path);

        if (!isset($data[$esquema_old]) || !is_array($data[$esquema_old]) || !isset($data[$esquema_old]['password'])) {
            return false;
        }

        $pwd = $data[$esquema_old]['password'];
        unset($data[$esquema_old]);
        $data[$esquema_new] = ['user' => $esquema_new, 'password' => $pwd];
        $this->guardarArrayInc($path, $data);

        return true;
    }

    private function eliminarClaveEnRoles(string $ficheroBase, string $esquema): void
    {
        $path = self::dirPwd() . '/' . self::ficheroRolesNombre($ficheroBase);
        $data = self::cargarArrayInc($path);
        if (!isset($data[$esquema])) {
            return;
        }
        unset($data[$esquema]);
        $this->guardarArrayInc($path, $data);
    }

    /** @param array<string, mixed> $data */
    private function guardarArrayInc(string $path, array $data): void
    {
        file_put_contents($path, '<?php return ' . var_export($data, true) . ' ;');
    }
}
