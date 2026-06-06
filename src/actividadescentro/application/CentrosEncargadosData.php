<?php

namespace src\actividadescentro\application;

use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\procesos\domain\PermAccion;
use src\shared\config\ConfigGlobal;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Devuelve el listado de centros encargados actuales de una actividad junto
 * con el flag `permite_modificar` (calculado a partir de
 * `PermisosActividades`) para que el frontend decida si pinta los centros
 * como links (fnjs_cambiar_ctr) o como texto plano.
 *
 * Sucesor de la rama `get` del dispatcher legacy.
 */
final class CentrosEncargadosData
{
    public function __construct(
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     *
     * @return array{
     *     id_activ: int,
     *     permite_ver: bool,
     *     permite_modificar: bool,
     *     centros: list<array{id_ubi: int, nombre_ubi: string}>
     * }
     */
    public function execute(array $input): array
    {
        $id_activ = input_int($input, 'id_activ');
        if ($id_activ <= 0) {
            return [
                'id_activ' => 0,
                'permite_ver' => false,
                'permite_modificar' => false,
                'centros' => [],
            ];
        }
        $id_tipo_activ = input_string($input, 'id_tipo_activ');
        $dl_org = input_string($input, 'dl_org');

        $oPermCtr = $this->resolverPermisoCtr($id_activ, $id_tipo_activ, $dl_org);
        $permite_ver = $oPermCtr->have_perm_activ('ver') === true;
        $permite_modificar = $oPermCtr->have_perm_activ('modificar') === true;

        $centros = [];
        if ($permite_ver) {
            $cCentros = $this->centroEncargadoRepository->getCentrosEncargadosActividad($id_activ);
            foreach ($cCentros as $oCentro) {
                $centros[] = [
                    'id_ubi' => (int) $oCentro->getId_ubi(),
                    'nombre_ubi' => (string) $oCentro->getNombre_ubi(),
                ];
            }
        }

        return [
            'id_activ' => $id_activ,
            'permite_ver' => $permite_ver,
            'permite_modificar' => $permite_modificar,
            'centros' => $centros,
        ];
    }

    private function resolverPermisoCtr(int $id_activ, string $id_tipo_activ, string $dl_org): PermAccion
    {
        if (ConfigGlobal::is_app_installed('procesos')) {
            $oPermSesion = $_SESSION['oPermActividades'] ?? null;
            if ($oPermSesion instanceof PermisosActividades) {
                $oPermSesion->setActividad($id_activ, $id_tipo_activ, $dl_org);

                return $oPermSesion->getPermisoActual('ctr');
            }
        }
        $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());

        return $oPermActividades->getPermisoActual('ctr');
    }
}
