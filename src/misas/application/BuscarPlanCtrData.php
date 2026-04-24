<?php

namespace src\misas\application;

use src\shared\config\ConfigGlobal;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\misas\application\support\IdNomJefeResolver;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Formulario buscador del plan de misas por centro (zonas + centros + periodo).
 */
class BuscarPlanCtrData
{
    /**
     * @return array{
     *   view: 'sacd'|'centro'|'none',
     *   zonas_opciones: array<int|string, string>,
     *   zonas_selected: int,
     *   centros_opciones: array<int|string, string>,
     *   centros_selected: string,
     *   id_ubi_centro: string
     * }
     */
    public static function getData(int $id_zona): array
    {
        $container = $GLOBALS['container'];

        $UsuarioRepository = $container->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();

        $RoleRepository = $container->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $role_nom = $aRoles[$id_role] ?? '';
        $aCentros = [];
        $id_nom_jefe = null;
        $id_sacd = '';
        $id_ubi = '';

        $zonas_opciones = [-1 => 'centros encargos'];
        $zonas_selected = $id_zona;

        if ($role_nom === 'Centro sv' || $role_nom === 'Centro sf') {
            $id_ubi = $oMiUsuario->getCsvIdPauVo()?->value();
            $oCentro = Ubi::newUbi($id_ubi);
            $nombre_ubi = $oCentro->getNombreUbiVo()->value();
            $aCentros[$id_ubi] = $nombre_ubi;

            return [
                'view' => 'centro',
                'zonas_opciones' => $zonas_opciones,
                'zonas_selected' => -1,
                'centros_opciones' => $aCentros,
                'centros_selected' => (string)$id_ubi,
                'id_ubi_centro' => (string)$id_ubi,
            ];
        }

        if ($role_nom === 'p-sacd') {
            $jefe = IdNomJefeResolver::resolve();
            if ($jefe['error'] !== '') {
                return [
                    'view' => 'none',
                    'zonas_opciones' => [],
                    'zonas_selected' => 0,
                    'centros_opciones' => [],
                    'centros_selected' => '',
                    'id_ubi_centro' => '',
                ];
            }
            $id_nom_jefe = $jefe['id_nom_jefe'];

            if ($id_zona === 0) {
                $id_zona = -1;
            }
            $zonas_selected = $id_zona;

            $ZonaRepository = $container->get(ZonaRepositoryInterface::class);
            $aOpcionesZona = $ZonaRepository->getArrayZonas($id_nom_jefe);
            $aOpcionesZona[-1] = 'centros encargos';

            $id_ubi = null;
            if ($id_zona > 0) {
                $aWhere = [];
                $aWhere['active'] = 't';
                $aWhere['id_zona'] = $id_zona;
                $aWhere['_ordre'] = 'nombre_ubi';
                $CentroEllosRepository = $container->get(CentroEllosRepositoryInterface::class);
                $cCentrossv = $CentroEllosRepository->getCentros($aWhere);
                $CentroEllasRepository = $container->get(CentroEllasRepositoryInterface::class);
                $cCentrosSf = $CentroEllasRepository->getCentros($aWhere);
                $cCentrosList = array_merge($cCentrossv, $cCentrosSf);
                foreach ($cCentrosList as $oCentro) {
                    $idu = $oCentro->getId_ubi();
                    $id_ubi = $idu;
                    $aCentros[$idu] = $oCentro->getNombre_ubi();
                }
            } else {
                $id_sacd = $oMiUsuario->getCsvIdPauAsString();
                $EncargosSacdRepository = $container->get(EncargoSacdRepositoryInterface::class);
                $aWhereES = [];
                $aOperadorES = [];
                $aWhereES['id_nom'] = $id_sacd;
                $aWhereES['f_fin'] = 'x';
                $aOperadorES['f_fin'] = 'IS NULL';
                $aWhereES['_ordre'] = 'modo, f_ini DESC';
                $cEncargosSacd1 = $EncargosSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

                $oF_hoy = new DateTimeLocal(date('Y-m-d'));
                $hoy = $oF_hoy->getIso();

                $aWhereES['f_fin'] = "'$hoy'";
                $aOperadorES['f_fin'] = '>';
                $cEncargosSacd2 = $EncargosSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

                $cEncargosSacd = $cEncargosSacd1 + $cEncargosSacd2;

                $EncargoRepository = $container->get(EncargoRepositoryInterface::class);
                foreach ($cEncargosSacd as $oEncargoSacd) {
                    $id_enc = $oEncargoSacd->getId_enc();
                    $oEncargo = $EncargoRepository->findById($id_enc);
                    if ($oEncargo === null) {
                        continue;
                    }
                    $id_tipo_enc = $oEncargo->getId_tipo_enc();
                    if (substr((string)$id_tipo_enc, 0, 1) <= 3) {
                        $idu = $oEncargo->getId_ubi();
                        $id_ubi = $idu;
                        $oCentroU = Ubi::newUbi($idu);
                        $nombre_ubi = $oCentroU->getNombre_ubi();
                        $aCentros[$idu] = $nombre_ubi;
                    }
                }
            }

            $centros_selected = isset($id_ubi) ? (string)$id_ubi : '';

            return [
                'view' => 'sacd',
                'zonas_opciones' => $aOpcionesZona,
                'zonas_selected' => $zonas_selected,
                'centros_opciones' => $aCentros,
                'centros_selected' => $centros_selected,
                'id_ubi_centro' => '',
            ];
        }

        return [
            'view' => 'none',
            'zonas_opciones' => [],
            'zonas_selected' => 0,
            'centros_opciones' => [],
            'centros_selected' => '',
            'id_ubi_centro' => '',
        ];
    }
}
