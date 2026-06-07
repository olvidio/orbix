<?php

namespace src\encargossacd\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\config\ConfigGlobal;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

/**
 * Datos para el listado de SACDs susceptibles de gestionar ausencias desde
 * la ficha de jefe de zona
 * (`frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`).
 *
 * Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde
 * (Oficial_dl o jefe de calendario), la totalidad de SACDs activos. El
 * array se devuelve ordenado por iniciales para alimentar el desplegable.
 */
final class SacdAusenciasJefeZonaData
{

    public function __construct(
        private InicialesSacdRepositoryInterface $inicialesSacdRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private RoleRepositoryInterface $roleRepository,
        private UsuarioRepositoryInterface $usuarioRepository,
        private ZonaRepositoryInterface $zonaRepository,
        private ZonaSacdRepositoryInterface $zonaSacdRepository
    ) {
    }

    /**
     * @return array{ a_sacd: array<string, string> }
     */
    public function execute(): array
    {
        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return ['a_sacd' => []];
        }
        $id_role = (int) $oMiUsuario->getId_role();
        $id_sacd = $oMiUsuario->getCsvIdPauVo()?->value();

        $aRoles = $this->roleRepository->getArrayRoles();

        $cZonas = $this->zonaRepository->getZonas(['id_nom' => $id_sacd]);

        $a_sacd = [];

        if ($cZonas !== []) {
            foreach ($cZonas as $oZona) {
                $id_zona = $oZona->getId_zona();
                $cSacds = $this->zonaSacdRepository->getIdSacdsDeZona($id_zona);
                foreach ($cSacds as $id_nom) {
                    $this->addSacdOption($a_sacd, (int) $id_nom);
                }
            }
        } elseif ($id_sacd !== null && is_numeric($id_sacd)) {
            $this->addSacdOption($a_sacd, (int) $id_sacd);
        }

        $es_jefe_calendario = false;
        if (!empty($_SESSION['oConfig']) && $_SESSION['oConfig'] instanceof ConfigSnapshot) {
            $es_jefe_calendario = $_SESSION['oConfig']->is_jefeCalendario();
        }

        if (($aRoles[$id_role] ?? '') === 'Oficial_dl' || $es_jefe_calendario) {
            $cPersonas = $this->personaSacdRepository->getPersonas(
                [
                    'sacd' => 't',
                    'situacion' => 'A',
                    'id_tabla' => "'n','a'",
                    '_ordre' => 'apellido1,apellido2,nom',
                ],
                ['id_tabla' => 'IN'],
            ) ?: [];
            foreach ($cPersonas as $oPersona) {
                $this->addSacdOption($a_sacd, (int) $oPersona->getId_nom());
            }
        }

        ksort($a_sacd);

        return ['a_sacd' => $a_sacd];
    }

    /**
     * @param array<string, string> $a_sacd
     */
    private function addSacdOption(array &$a_sacd, int $id_nom): void
    {
        $oPersonaSacd = $this->personaSacdRepository->findById($id_nom);
        $sacd = $oPersonaSacd?->getNombreApellidos();
        $oInicialesSacd = $this->inicialesSacdRepository->findById($id_nom);
        $iniciales = $oInicialesSacd?->getIniciales() ?? '';
        $key = $iniciales . '#' . $id_nom;
        $a_sacd[$key] = (string) ($sacd ?? '?');
    }
}
