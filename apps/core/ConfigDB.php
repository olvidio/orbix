<?php

namespace core;

/**
 * Básicamente la conexión a la base de datos, con los passwd para cada esquema.
 * @author dani
 *
 */
class ConfigDB
{

    private $data;

    public function __construct($database)
    {
        $this->setDataBase($database);
    }

    public function getEsquema($esquema)
    {
        $data = $this->data['default'];
        $data['schema'] = $esquema;
        //sobreescribir los valores default
        foreach ($this->data[$esquema] as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

    public function setDataBase($database)
    {
        if (ConfigGlobal::WEBDIR === 'pruebas') {
            $database = 'pruebas-' . $database;
        }
        $this->data = include ConfigGlobal::DIR_PWD . '/' . $database . '.inc';
    }

    /**
     * Añade el usuario y password del esquema en el archivo de passwords
     * tipo: comun.inc
     *
     * @param $database
     * @param $esquema
     * @param $esquema_pwd
     * @return void
     */
    public function addEsquemaEnFicheroPasswords($database, $esquema, $esquema_pwd)
    {
        // Las bases de datos de pruebas y producción están en el mismo cluster, y 
        // por tanto los usuarios son los mismos. Hay que ponerlo en los dos ficheros:
        // Pero OJO: la parte de definición de host y dbname son diferentes!!

        // producción
        $this->addEsquema($database, $esquema, $esquema_pwd);

        // pruebas
        $database = 'pruebas-' . $database;
        $this->addEsquema($database, $esquema, $esquema_pwd);
    }

    private function addEsquema($database, $esquema, $esquema_pwd)
    {
        $filename = ServerConf::DIR_PWD . '/' . $database . '.inc';

        $this->data = include $filename;
        $this->data[$esquema] = ['user' => $esquema, 'password' => $esquema_pwd];
        file_put_contents($filename, '<?php return ' . var_export($this->data, true) . ' ;');

        // Para las DB Select
        if ($database === 'sv-e' || $database === 'comun') {
            $filename = ServerConf::DIR_PWD . '/' . $database . '_select.inc';
            $this->data = include $filename;
            $this->data[$esquema] = ['user' => $esquema, 'password' => $esquema_pwd];
            file_put_contents($filename, '<?php return ' . var_export($this->data, true) . ' ;');
        }
    }

    public function renombrarListaEsquema($database, $esquema_old, $esquema_new)
    {
        // Las bases de datos de pruebas y producción están en el mismo cluster, y 
        // por tanto los usuarios son los mismos. Hay que ponerlo en los dos ficheros:
        // Pero OJO: la parte de definición de host y dbname son diferentes!!

        $this->renombrarListaEsquemaProduccion($database, $esquema_old, $esquema_new);
        $this->renombrarListaEsquemaPruebas($database, $esquema_old, $esquema_new);
    }

    public function renombrarListaEsquemaProduccion($database, $esquema_old, $esquema_new)
    {
        $this->data = include ConfigGlobal::DIR_PWD . '/' . $database . '.inc';

        $esquema_pwd = $this->data[$esquema_old]['password'];
        unset($this->data[$esquema_old]);
        $this->data[$esquema_new] = ['user' => $esquema_new, 'password' => $esquema_pwd];

        $filename = ConfigGlobal::DIR_PWD . '/' . $database . '.inc';
        file_put_contents($filename, '<?php return ' . var_export($this->data, true) . ' ;');
    }

    public function renombrarListaEsquemaPruebas($database, $esquema_old, $esquema_new)
    {
        $database = 'pruebas-' . $database;
        $this->data = include ConfigGlobal::DIR_PWD . '/' . $database . '.inc';

        $esquema_pwd = $this->data[$esquema_old]['password'];
        unset($this->data[$esquema_old]);
        $this->data[$esquema_new] = ['user' => $esquema_new, 'password' => $esquema_pwd];

        $filename_pruebas = ConfigGlobal::DIR_PWD . '/' . $database . '.inc';
        file_put_contents($filename_pruebas, '<?php return ' . var_export($this->data, true) . ' ;');
    }
}