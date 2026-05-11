<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\postgresql\DBTabla;
use src\shared\infrastructure\persistence\postgresql\DBTrasvase;

/**
 * Copia tablas y traslada datos resto→dl según flags comun/sv/sf (herramienta devel_db_admin).
 */
final class CopiarEsquema
{
    public function ejecutar(
        string $esquemaRefPost,
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): void {
        $esquema = "$region-$dl";
        $esquemav = $esquema . 'v';
        $esquemaf = $esquema . 'f';

        $a_reg = explode('-', $esquemaRefPost);
        $RegionRef = $a_reg[0];
        $DlRef = substr($a_reg[1], 0, -1);

        $esquemaRef = "$RegionRef-$DlRef";
        $esquemaRefv = $esquemaRef . 'v';
        $esquemaReff = $esquemaRef . 'f';

        if ($comun !== 0) {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema('public');

            /**
             * lista de tablas de las que hay que copiar los valores.
             * Posteriormente hay que cambiar el id_schema (si tiene)
             * y actualizar la secuencia (se hace al final, en DBTrasvase)
             *
             * @var array $aTablas
             */
            $aTablas = [
                'a_tipos_actividad' => ['id_schema' => 'yes'],
                'xa_tipo_tarifa' => ['id_schema' => 'yes'],
                'x_config_schema' => ['id_schema' => 'yes'],
            ];
            $oDBTabla = new DBTabla();
            $oDBTabla->setConfig($config);
            $oDBTabla->setRef($esquemaRef);
            $oDBTabla->setNew($esquema);
            $oDBTabla->setTablas($aTablas);
            $oDBTabla->copiar();
            // para la DB Select de la máquina interna
            // No hay que volver a copiar, simplemente refrescar la Subscripción:
            // Ya se hace al crear la tabla.
            // (( para saber el nombre: SELECT oid, subdbid, subname, subconninfo, subpublications FROM pg_subscription; ))
            // ALTER SUBSCRIPTION subcomun REFRESH PUBLICATION;

            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('comun');

            $oTrasvase->actividades('resto2dl');
            $oTrasvase->cdc('resto2dl');
            $oTrasvase->fix_seq();
        }

        if ($sv !== 0) {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema('publicv-e');

            $aTablas = [
                'aux_cross_usuarios_grupos' => ['id_schema' => 'yes'],
                'aux_grupmenu' => ['id_schema' => 'yes'],
                'aux_grupmenu_rol' => ['id_schema' => 'yes'],
                'aux_grupo_permmenu' => ['id_schema' => 'yes'],
                'aux_grupos_y_usuarios' => ['id_schema' => 'yes'],
                'aux_menus' => ['id_schema' => 'yes'],
                'aux_usuarios' => ['id_schema' => 'yes'],
                'web_preferencias' => ['id_schema' => 'yes'],
                'm0_mods_installed_dl' => ['id_schema' => 'yes'],
            ];
            $oDBTabla = new DBTabla();
            $oDBTabla->setConfig($config);
            $oDBTabla->setRef($esquemaRefv);
            $oDBTabla->setNew($esquemav);
            $oDBTabla->setTablas($aTablas);
            $oDBTabla->copiar();

            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('sv');

            $oTrasvase->ctr('resto2dl');
            $oTrasvase->fix_seq();
        }

        if ($sf !== 0) {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema('publicf-e');

            $aTablas = [
                'aux_cross_usuarios_grupos' => ['id_schema' => 'yes'],
                'aux_grupmenu' => ['id_schema' => 'yes'],
                'aux_grupmenu_rol' => ['id_schema' => 'yes'],
                'aux_grupo_permmenu' => ['id_schema' => 'yes'],
                'aux_grupos_y_usuarios' => ['id_schema' => 'yes'],
                'aux_menus' => ['id_schema' => 'yes'],
                'aux_usuarios' => ['id_schema' => 'yes'],
                'web_preferencias' => ['id_schema' => 'yes'],
                'm0_mods_installed_dl' => ['id_schema' => 'yes'],
            ];
            $oDBTabla = new DBTabla();
            $oDBTabla->setConfig($config);
            $oDBTabla->setRef($esquemaReff);
            $oDBTabla->setNew($esquemaf);
            $oDBTabla->setTablas($aTablas);
            $oDBTabla->copiar();

            $oTrasvase = new DBTrasvase();
            $oTrasvase->setRegion($region);
            $oTrasvase->setDl($dl);
            $oTrasvase->setDbName('sf');

            $oTrasvase->ctr('resto2dl');
            $oTrasvase->fix_seq();
        }
    }
}
