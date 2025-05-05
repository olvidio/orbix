<?php

namespace core;

class DBRefresh
{

    public function __construct()
    {
    }

    public function refreshSubscriptionModulo(string $db)
    {
        $fileLog = ConfigGlobal::$directorio . '/log/db/pg_error_modulos.sql';
        //cambiar la conexión
        // OJO en "importar" no está la Base de Datos de pruebas, pero aquí sólo hace falta el host.
        $oConfigDB = new ConfigDB('importar');
        if ($db === 'comun') {
            $config = $oConfigDB->getEsquema('public_select'); //de la database comun
        }
        if ($db === 'sv-e') {
            $config = $oConfigDB->getEsquema('publicv-e_select'); //de la database sv-e
        }
        $host = $config['host'];
        $oConnection = new DBConnection($config);
        $dsn = $oConnection->getURI();
        $this->refreshSubscription($host, $db, $dsn, $fileLog);
    }

    /**
     * @param $host
     * @param string $db 'comun, sv-e'
     * @param string $dsn
     * @param $fileLog
     * @return void
     */
    public function refreshSubscription($host, string $db, string $dsn, $fileLog)
    {
        ///// REFRESCAR LA SUBSCRIPCIÓN ///////////
        // (( para saber el nombre: SELECT oid, subdbid, subname, subconninfo, subpublications FROM pg_subscription; ))
        // ALTER SUBSCRIPTION subcomun REFRESH PUBLICATION;
        $command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -h $host -d $db -U postgres -q  -X -t --pset pager=off ";
        if (ServerConf::WEBDIR === 'pruebas') {
            $command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -h $host -d pruebas-$db -U postgres -q  -X -t --pset pager=off ";
            if ($db === 'comun') {
                $command .= "-c 'ALTER SUBSCRIPTION subpruebascomun REFRESH PUBLICATION;' ";
            }
            if ($db === 'sv-e') {
                $command .= "-c 'ALTER SUBSCRIPTION subpruebassve REFRESH PUBLICATION;' ";
            }
        } else {
            $command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -h $host -d $db -U postgres -q  -X -t --pset pager=off ";
            if ($db === 'comun') {
                $command .= "-c 'ALTER SUBSCRIPTION subcomun REFRESH PUBLICATION;' ";
            }
            if ($db === 'sv-e') {
                $command .= "-c 'ALTER SUBSCRIPTION subsve REFRESH PUBLICATION;' ";
            }
        }
        $command .= "\"" . $dsn . "\"";
        $command .= " > " . $fileLog . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($fileLog);
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(4): %s<br> mirar en: %s<br>"), $command, $fileLog);
            }
        }

    }
}