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

    /** @param string $database p. ej. `comun`, `sv-e`, `comun_select` (sin prefijo pruebas-) */
    public function __construct($database)
    {
        $this->setDataBase($database);
    }

    public function getEsquema($esquema)
    {
        $data = $this->data['default'];
        $data['schema'] = $esquema;
        if (!array_key_exists($esquema, $this->data)) {
            throw new RunTimeException(sprintf(_('hay que añadir los parámetros de conexión para el esquema: %s'), $esquema));
        }
        foreach ($this->data[$esquema] as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * @param string $database nombre lógico (`comun`, `sv-e`, …) con o sin prefijo `pruebas-`
     */
    public function setDataBase($database): void
    {
        $base = self::normalizarBaseLogico($database);
        if (self::usaFormatoPartido($base)) {
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
        $legacyPath = self::rutaMonolitico($baseLogico);
        $legacy = is_readable($legacyPath) ? self::cargarArrayInc($legacyPath) : [];

        if ($conn === [] && isset($legacy['default']) && is_array($legacy['default'])) {
            $conn['default'] = $legacy['default'];
        } elseif ($conn === [] && isset($legacy['default'])) {
            $conn['default'] = $legacy['default'];
        }

        $rolesPath = self::dirPwd() . '/' . self::ficheroRolesNombre($baseLogico);
        $roles = self::cargarArrayInc($rolesPath);
        if ($roles === [] && $legacy !== []) {
            $roles = self::extraerEntradasEsquema($legacy);
        }

        $merged = $conn;
        foreach ($roles as $clave => $valor) {
            if ($clave === 'default') {
                continue;
            }
            $merged[$clave] = $valor;
        }

        return $merged;
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
        file_put_contents($path, '<?php return ' . var_export($data, true) . ' ;');
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
        file_put_contents($path, '<?php return ' . var_export($data, true) . ' ;');
    }

    private function addEsquemaMonolitico(string $database, string $esquema, string $esquema_pwd): void
    {
        $filename = self::dirPwd() . '/' . $database . '.inc';
        $data = self::cargarArrayInc($filename);
        $data[$esquema] = ['user' => $esquema, 'password' => $esquema_pwd];
        file_put_contents($filename, '<?php return ' . var_export($data, true) . ' ;');

        if ($database === 'sv-e' || $database === 'comun') {
            $filenameSelect = self::dirPwd() . '/' . $database . '_select.inc';
            $dataSelect = self::cargarArrayInc($filenameSelect);
            $dataSelect[$esquema] = ['user' => $esquema, 'password' => $esquema_pwd];
            file_put_contents($filenameSelect, '<?php return ' . var_export($dataSelect, true) . ' ;');
        }
    }

    private function renombrarClaveEnMonolitico(string $database, string $esquema_old, string $esquema_new): void
    {
        $filename = self::dirPwd() . '/' . $database . '.inc';
        $data = self::cargarArrayInc($filename);

        if (!isset($data[$esquema_old]) || !is_array($data[$esquema_old]) || !isset($data[$esquema_old]['password'])) {
            return;
        }

        $pwd = $data[$esquema_old]['password'];
        unset($data[$esquema_old]);
        $data[$esquema_new] = ['user' => $esquema_new, 'password' => $pwd];
        file_put_contents($filename, '<?php return ' . var_export($data, true) . ' ;');
    }
}
