<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\misas\application\support\IdNomJefeResolver;
use src\shared\config\ConfigGlobal;
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

    public function __construct(
        private readonly UsuarioRepositoryInterface     $usuarioRepository,
        private readonly RoleRepositoryInterface        $roleRepository,
        private readonly ZonaRepositoryInterface        $zonaRepository,
        private readonly CentroEllosRepositoryInterface $centroEllosRepository,
        private readonly CentroEllasRepositoryInterface $centroEllasRepository,
        private readonly EncargoSacdRepositoryInterface $encargoSacdRepository,
        private readonly EncargoRepositoryInterface     $encargoRepository,
        private readonly IdNomJefeResolver              $idNomJefeResolver,
    )
    {
    }

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
    public function getData(int $id_zona): array
    {
        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return [
                'view' => 'none',
                'zonas_opciones' => [],
                'zonas_selected' => 0,
                'centros_opciones' => [],
                'centros_selected' => '',
                'id_ubi_centro' => '',
            ];
        }

        $id_role = $oMiUsuario->getId_role();
        $aRoles = $this->roleRepository->getArrayRoles();

        $role_nom = $aRoles[$id_role] ?? '';
        /** @var array<int|string, string> $aCentros */
        $aCentros = [];
        $id_ubi = '';

        $zonas_opciones = [-1 => 'centros encargos'];
        $zonas_selected = $id_zona;

        if ($role_nom === 'Centro sv' || $role_nom === 'Centro sf') {
            $id_ubi = $oMiUsuario->getCsvIdPauVo()?->value() ?? '';
            if ($id_ubi === '') {
                return [
                    'view' => 'none',
                    'zonas_opciones' => [],
                    'zonas_selected' => 0,
                    'centros_opciones' => [],
                    'centros_selected' => '',
                    'id_ubi_centro' => '',
                ];
            }
            $oCentro = Ubi::NewUbi($id_ubi);
            $nombre_ubi = $oCentro !== null ? $oCentro->getNombreUbiVo()->value() : '';
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

        // si soy sacd
        $jefe = $this->idNomJefeResolver->resolve();
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
        $aOpcionesZona = $this->zonaRepository->getArrayZonas($id_nom_jefe);
        $aOpcionesZona = [-1 => 'centros encargos'] + $aOpcionesZona;

        $id_ubi = '';
        if ($id_zona > 0) {
            $aWhere = [];
            $aWhere['active'] = 't';
            $aWhere['id_zona'] = $id_zona;
            $aWhere['_ordre'] = 'nombre_ubi';
            $cCentrossv = $this->centroEllosRepository->getCentros($aWhere);
            $cCentrosSf = $this->centroEllasRepository->getCentros($aWhere);
            $cCentrosList = array_merge($cCentrossv, $cCentrosSf);
            foreach ($cCentrosList as $oCentro) {
                $idu = (string)$oCentro->getId_ubi();
                $id_ubi = $idu;
                $aCentros[$idu] = $oCentro->getNombre_ubi();
            }
        } else {
            $id_sacd = $oMiUsuario->getCsvIdPauAsString();
            $aWhereES = [];
            $aOperadorES = [];
            $aWhereES['id_nom'] = $id_sacd;
            $aWhereES['f_fin'] = 'x';
            $aOperadorES['f_fin'] = 'IS NULL';
            $aWhereES['_ordre'] = 'modo, f_ini DESC';
            $cEncargosSacd1 = $this->encargoSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

            $oF_hoy = new DateTimeLocal(date('Y-m-d'));
            $hoy = $oF_hoy->getIso();

            $aWhereES['f_fin'] = "'$hoy'";
            $aOperadorES['f_fin'] = '>';
            $cEncargosSacd2 = $this->encargoSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

            $cEncargosSacd = $cEncargosSacd1 + $cEncargosSacd2;
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $id_enc = $oEncargoSacd->getId_enc();
                $oEncargo = $this->encargoRepository->findById($id_enc);
                if ($oEncargo === null) {
                    continue;
                }
                $id_tipo_enc = $oEncargo->getId_tipo_enc();
                if (substr((string)$id_tipo_enc, 0, 1) <= 3) {
                    $idu = $oEncargo->getId_ubi();
                    if ($idu === null) {
                        continue;
                    }
                    $id_ubi = (string)$idu;
                    $oCentroU = Ubi::NewUbi($idu);
                    $nombre_ubi = $oCentroU !== null ? $oCentroU->getNombre_ubi() : '';
                    $aCentros[$idu] = $nombre_ubi;
                }
            }
        }

        $centros_selected = $id_ubi;

        return [
            'view' => 'sacd',
            'zonas_opciones' => $aOpcionesZona,
            'zonas_selected' => $zonas_selected,
            'centros_opciones' => $aCentros,
            'centros_selected' => $centros_selected,
            'id_ubi_centro' => '',
        ];
    }
}
