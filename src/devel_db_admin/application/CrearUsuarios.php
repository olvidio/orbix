<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBRol;

/**
 * Crea usuarios PostgreSQL (comun / sv / sv-e / sf) y actualiza ficheros de passwords de importación.
 */
final class CrearUsuarios
{
    public function ejecutar(string $region, string $dl, bool $sessionEsSf): CrearUsuariosResult
    {
        $esquema = "$region-$dl";
        $esquema_pwd = self::generarPassword(11);
        $esquemav = $esquema . 'v';
        $esquemav_pwd = self::generarPassword(11);
        $esquemaf = $esquema . 'f';
        $esquemaf_pwd = self::generarPassword(11);

        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('public');

        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oDBRol = new DBRol();
        $oDBRol->setDbConexion($oDevelPC);
        $oDBRol->setUser($esquema);
        $oDBRol->setPwd($esquema_pwd);
        $oDBRol->crearUsuario();
        $oConfigDB->addEsquemaEnFicheroPasswords('comun', $esquema, $esquema_pwd);

        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oDBRol = new DBRol();
        $oDBRol->setDbConexion($oDevelPC);

        $oDBRol->setUser($esquemav);
        $oDBRol->setPwd($esquemav_pwd);
        $oDBRol->crearUsuario();
        $oConfigDB->addEsquemaEnFicheroPasswords('sv', $esquemav, $esquemav_pwd);
        $oDBRol->setUser($esquema);
        $oDBRol->setPwd($esquema_pwd);
        $oDBRol->crearUsuario();

        $host_sv = $config['host'];
        $port_sv = $config['port'];
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('publicv-e');
        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();
        $host_sve = $config['host'];
        $port_sve = $config['port'];

        if ($host_sv != $host_sve || $port_sv != $port_sve) {
            $oDBRol = new DBRol();
            $oDBRol->setDbConexion($oDevelPC);

            $oDBRol->setUser($esquemav);
            $oDBRol->setPwd($esquemav_pwd);
            $oDBRol->crearUsuario();
        }
        $oConfigDB->addEsquemaEnFicheroPasswords('sv-e', $esquemav, $esquemav_pwd);

        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oDBRol = new DBRol();
        $oDBRol->setDbConexion($oDevelPC);

        $oDBRol->setUser($esquemaf);
        $oDBRol->setPwd($esquemaf_pwd);
        $oDBRol->crearUsuario();
        $oConfigDB->addEsquemaEnFicheroPasswords('sf', $esquemaf, $esquemaf_pwd);

        if ($sessionEsSf) {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema('publicf');
            $oConexion = new DBConnection($config);
            $oDevelPC = $oConexion->getPDO();

            $oDBRol = new DBRol();
            $oDBRol->setDbConexion($oDevelPC);

            $oDBRol->setUser($esquemaf);
            $oDBRol->setPwd($esquemaf_pwd);
            $oDBRol->crearUsuario();
            $oConfigDB->addEsquemaEnFicheroPasswords('sf', $esquemaf, $esquemaf_pwd);
        }

        return new CrearUsuariosResult(
            $esquema,
            $esquema_pwd,
            $esquemav,
            $esquemav_pwd,
            $esquemaf,
            $esquemaf_pwd,
        );
    }

    private static function generarPassword(int $largo): string
    {
        $cadena_base = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $cadena_base .= '0123456789';
        $cadena_base .= '!@#%^&*()_,./<>?:[]{}|=+';

        $password = '';
        $limite = strlen($cadena_base) - 1;

        for ($i = 0; $i < $largo; $i++) {
            $password .= $cadena_base[rand(0, $limite)];
            $cadena_base = str_shuffle($cadena_base);
        }

        return $password;
    }
}
