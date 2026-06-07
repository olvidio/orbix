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
    /**
     * @return list<string> avisos no bloqueantes (p. ej. esquema de conexión no configurado en importar)
     */
    public function ejecutar(
        string $esquemaRefPost,
        string $region,
        string $dl,
        int $comun,
        int $sv,
        int $sf,
    ): array {
        $avisos = [];
        $region = trim($region);
        $dl = trim($dl);
        if ($region === '' || $dl === '') {
            throw new \InvalidArgumentException(_('Faltan región o delegación destino para copiar datos.'));
        }

        $esquema = "$region-$dl";
        $esquemav = $esquema . 'v';
        $esquemaf = $esquema . 'f';

        $esquemaBaseRef = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen($esquemaRefPost);
        if ($esquemaBaseRef === '' || !str_contains($esquemaBaseRef, '-')) {
            throw new \InvalidArgumentException(_('Esquema de referencia no válido.'));
        }
        [$RegionRef, $DlRef] = explode('-', $esquemaBaseRef, 2);

        $esquemaRef = $esquemaBaseRef;
        $esquemaRefv = $esquemaRef . 'v';
        $esquemaReff = $esquemaRef . 'f';

        $oConfigDB = new ConfigDB('importar');

        if ($comun !== 0) {
            $config = $this->configImportar($oConfigDB, 'public', _('comun'), $avisos);
            if ($config !== null) {
                /**
                 * lista de tablas de las que hay que copiar los valores.
                 * Posteriormente hay que cambiar el id_schema (si tiene)
                 * y actualizar la secuencia (se hace al final, en DBTrasvase)
                 *
                 * @var array<string, array{id_schema: string}> $aTablas
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

                $oTrasvase = new DBTrasvase();
                $oTrasvase->setRegion($region);
                $oTrasvase->setDl($dl);
                $oTrasvase->setDbName('comun');
                $oTrasvase->usarRolesDelEsquemaObjetivo();

                $oTrasvase->actividades('resto2dl');
                $oTrasvase->cdc('resto2dl');
                $oTrasvase->fix_seq();
                $avisos = array_merge($avisos, $oTrasvase->consumirAvisosConexion());
            }
        }

        if ($sv !== 0) {
            $config = $this->configImportar($oConfigDB, 'publicv-e', _('sv (sv-e)'), $avisos);
            if ($config !== null) {
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
                $oTrasvase->usarRolesDelEsquemaObjetivo();

                $oTrasvase->ctr('resto2dl');
                $oTrasvase->fix_seq();
                $avisos = array_merge($avisos, $oTrasvase->consumirAvisosConexion());
            }
        }

        if ($sf !== 0) {
            $config = $this->configImportar($oConfigDB, 'publicf-e', _('sf (sf-e)'), $avisos);
            if ($config !== null) {
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
                $oTrasvase->usarRolesDelEsquemaObjetivo();

                $oTrasvase->ctr('resto2dl');
                $oTrasvase->fix_seq();
                $avisos = array_merge($avisos, $oTrasvase->consumirAvisosConexion());
            }
        }

        return $avisos;
    }

    /**
     * @param list<string> $avisos
     * @return array<string, mixed>|null
     */
    private function configImportar(ConfigDB $oConfigDB, string $claveEsquema, string $etiquetaBloque, array &$avisos): ?array
    {
        if ($oConfigDB->tieneEsquema($claveEsquema)) {
            return $oConfigDB->getEsquema($claveEsquema);
        }

        $avisos[] = _('Se omitió') . ' ' . $etiquetaBloque . ': '
            . ConfigDB::mensajeAvisoEsquemaConexionFaltante('importar', $claveEsquema);

        return null;
    }
}
