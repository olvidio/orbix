<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\procesos\domain\PermAccion;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Devuelve los sacd encargados actuales de una actividad.
 */
final class SacdsEncargadosData
{
    public function __construct(
        private CargoRepositoryInterface $cargoRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $id_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        if ($id_activ <= 0) {
            return [
                'id_activ' => 0,
                'permite_ver' => false,
                'permite_modificar' => false,
                'sacds' => [],
            ];
        }
        $id_tipo_activ = FuncTablasSupport::inputString($input, 'id_tipo_activ');
        $dl_org = FuncTablasSupport::inputString($input, 'dl_org');

        $oPermSacd = $this->resolverPermisoSacd($id_activ, $id_tipo_activ, $dl_org);
        $permite_ver = $oPermSacd->have_perm_activ('ver') === true;
        $permite_modificar = $oPermSacd->have_perm_activ('modificar') === true;

        $sacds = [];
        if ($permite_ver) {
            $aIdCargos_sacd = $this->cargoRepository->getArrayCargos('sacd');
            $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

            $cCargos = $this->actividadCargoRepository->getActividadCargos(
                ['id_activ' => $id_activ, 'id_cargo' => $txt_where_cargos],
                ['id_cargo' => 'IN']
            );
            foreach ($cCargos as $oCargo) {
                    $id_nom = (int)$oCargo->getId_nom();
                    $oPersona = Persona::findPersonaEnGlobal($id_nom);
                    $ap_nom = is_object($oPersona)
                        ? (string)$oPersona->getPrefApellidosNombre()
                        : (string)$oPersona;
                    $sacds[] = [
                        'id_nom' => $id_nom,
                        'id_cargo' => (int)$oCargo->getId_cargo(),
                        'ap_nom' => $ap_nom,
                    ];
            }
        }

        return [
            'id_activ' => $id_activ,
            'permite_ver' => $permite_ver,
            'permite_modificar' => $permite_modificar,
            'sacds' => $sacds,
        ];
    }

    private function resolverPermisoSacd(int $id_activ, string $id_tipo_activ, string $dl_org): PermAccion
    {
        if (ConfigGlobal::is_app_installed('procesos') && isset($_SESSION['oPermActividades'])) {
            $oPerm = $_SESSION['oPermActividades'];
            if ($oPerm instanceof PermisosActividades) {
                $oPerm->setActividad($id_activ, $id_tipo_activ, $dl_org);
                return $oPerm->getPermisoActual('sacd');
            }
        }
        $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        return $oPermActividades->getPermisoActual('sacd');
    }
}
