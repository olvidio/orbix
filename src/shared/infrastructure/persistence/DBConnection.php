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
        $pdoDB = new PDO($dsn);
        $pdoDB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdoDB->exec("SET search_path TO \"$esquema\"");
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

        $password_encoded = urlencode($password);
        $dsn = "postgresql://$user:$password_encoded@$host:$port/" . $dbname . $str_conexio;
        if ($host === '/var/run/postgresql') {
            $dsn = "postgresql:///$dbname?host=$host";
        }

        return $dsn;
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
