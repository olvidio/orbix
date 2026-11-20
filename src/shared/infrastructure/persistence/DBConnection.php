<?php

namespace src\shared\infrastructure\persistence;

use PDO;

class DBConnection
{

    /** @var array<string, mixed> */
    private array $config;
    private ?string $esquemaOverride = null;

    public function setEsquema(?string $esquema): void
    {
        $this->esquemaOverride = $esquema;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getPDO(): PDO
    {
        $config = $this->config;

        $host = $this->configString($config, 'host');
        $port = $this->configString($config, 'port');
        $dbname = $this->configString($config, 'dbname');
        $user = $this->configString($config, 'user');
        $password = $this->configString($config, 'password');
        $str_conexio = $this->buildSslDsnSuffix($config, ';');

        // OJO Con las comillas dobles para algunos caracteres del password ($...)
        //$dsn = 'pgsql:host='.$host.' port='.$port.' dbname=\''.$dbname.'\' user=\''.$user.'\' password=\''.$password.'\'';
        $dsn = 'pgsql:host=' . $host . ';port=' . $port . ';dbname=\'' . $dbname . '\';user=\'' . $user . '\';password=\'' . $password . '\';' . $str_conexio;

        $esquema = $this->esquemaOverride ?? $this->configString($config, 'schema');
        // Pdo\Pgsql (subclase de PDO): los métodos del driver (copyFromFile, …) solo existen aquí; sobre un PDO
        // genérico (pgsqlCopyFromFile, …) están obsoletos desde PHP 8.5.
        $pdoDB = new \Pdo\Pgsql($dsn);
        $pdoDB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        // Identificador entre comillas dobles: las de dentro se duplican (si no, cerraban el identificador).
        $pdoDB->exec('SET search_path TO "' . str_replace('"', '""', $esquema) . '"');
        /* le paso la gestión a la clase web\datetimelocal */
        //$oDB->exec("SET DATESTYLE TO '$datestyle'");
        return $pdoDB;
    }

    public function getURI(): string
    {
        $config = $this->config;

        $host = $this->configString($config, 'host');
        $port = $this->configString($config, 'port');
        $dbname = $this->configString($config, 'dbname');
        $user = $this->configString($config, 'user');
        $password = $this->configString($config, 'password');
        $str_conexio = $this->buildSslDsnSuffix($config, '&');
        if ($str_conexio !== '') {
            $str_conexio = '?' . $str_conexio;
        }

        if ($host === '/var/run/postgresql') {
            return "postgresql:///$dbname?host=$host";
        }
        // La contraseña no va en la URI (acabaría en la línea de órdenes de psql/pg_dump, a la vista de
        // cualquier proceso, y en los mensajes de error): va en un fichero de claves propio.
        self::anotarEnPgpass($host, $port, $user, $password);

        return "postgresql://$user@$host:$port/" . $dbname . $str_conexio;
    }

    /** Fichero `PGPASSFILE` de este proceso (se borra al terminar). */
    private static ?string $pgpass = null;

    private static function anotarEnPgpass(string $host, string $port, string $user, string $password): void
    {
        if ($password === '') {
            return;
        }
        if (self::$pgpass === null || !is_file(self::$pgpass)) {
            $fichero = tempnam(sys_get_temp_dir(), 'orbix_pgpass_');
            if ($fichero === false) {
                throw new \RuntimeException('No se ha podido crear el fichero de claves de PostgreSQL');
            }
            chmod($fichero, 0600);
            self::$pgpass = $fichero;
            putenv('PGPASSFILE=' . $fichero);
            register_shutdown_function(static function () use ($fichero): void {
                @unlink($fichero);
            });
        }
        $esc = static fn (string $v): string => str_replace(['\\', ':'], ['\\\\', '\\:'], $v);
        $linea = implode(':', [$esc($host), $port !== '' ? $port : '*', '*', $esc($user), $esc($password)]) . "\n";
        file_put_contents(self::$pgpass, $linea, FILE_APPEND | LOCK_EX);
    }

    public function getPDOListas(): PDO
    {
        $config = $this->config;

        // con odbc:
        //$str_conexio = $config['driver'] . ":" . $config['dbname'];
        //$oDB = new PDO($str_conexio, $config['user'], $config['password'], array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING, \PDO::ATTR_TIMEOUT => 3));

        // con sqlsrv
        // A partir del driver 18, poner: Encrypt = no;
        $options = ['LoginTimeout' => 3];
        if (empty($config['host'])
            || empty($config['dbname'])
            || empty($config['user'])
            || empty($config['password'])) {
            throw new \InvalidArgumentException(_("Error de configuración: faltan parámetros para conectar con la BDU."));
        }
        $host = $this->configString($config, 'host');
        $dbname = $this->configString($config, 'dbname');
        $user = $this->configString($config, 'user');
        $password = $this->configString($config, 'password');
        $oDB = new PDO("sqlsrv:server = " . $host . "; Database = " . $dbname . "; Encrypt = no;", $user, $password, $options);

        return $oDB;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function buildSslDsnSuffix(array $config, string $separator): string
    {
        $parts = [];
        foreach (['sslmode', 'sslcert', 'sslkey', 'sslrootcert'] as $key) {
            $value = $this->configString($config, $key);
            if ($value !== '') {
                $parts[] = $key . '=' . $value;
            }
        }

        return implode($separator, $parts);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configString(array $config, string $key): string
    {
        $value = $config[$key] ?? null;
        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return '';
    }
}
