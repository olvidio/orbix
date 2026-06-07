<?php

namespace src\misas\application;

use src\shared\config\ConfigGlobal;
use src\misas\application\services\InicialesSacdService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

/**
 * Lista de sacerdotes disponibles en el buscador del plan SACD (según rol y zona).
 */
class BuscarPlanSacdData
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly ZonaRepositoryInterface $zonaRepository,
        private readonly InicialesSacdService $inicialesSacdService,
        private readonly ZonaSacdRepositoryInterface $zonaSacdRepository,
        private readonly PersonaSacdRepositoryInterface $personaSacdRepository,
    ) {
    }

    /**
     * @return array{sacd_opciones: array<string, string>, sacd_selected: string}
     */
    /**
     * @return array<string, mixed>
     */

    public function getData(): array
    {
        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return [
                'sacd_opciones' => [],
                'sacd_selected' => '',
            ];
        }

        $id_role = $oMiUsuario->getId_role();
        $id_sacd = $oMiUsuario->getCsvIdPauAsString();

        /** @var array<string, string> $a_sacd */
        $a_sacd = [];
        $aRoles = $this->roleRepository->getArrayRoles();
        $cZonas = $this->zonaRepository->getZonas(['id_nom' => $id_sacd]);

        if (count($cZonas) > 0) {
            foreach ($cZonas as $oZona) {
                $id_zona = $oZona->getId_zona();
                $a_id_nom = $this->zonaSacdRepository->getIdSacdsDeZona($id_zona);
                foreach ($a_id_nom as $id_nom) {
                    $sacd = $this->inicialesSacdService->obtenerNombreConIniciales($id_nom);
                    $iniciales = $this->inicialesSacdService->obtenerIniciales($id_nom);
                    $key = $id_nom . '#' . $iniciales;
                    $a_sacd[$key] = $sacd !== '' ? $sacd : '?';
                }
            }
        } elseif ($id_sacd !== null && $id_sacd !== '' && is_numeric($id_sacd)) {
            $id_sacd_int = (int) $id_sacd;
            $sacd = $this->inicialesSacdService->obtenerNombreConIniciales($id_sacd_int);
            $iniciales = $this->inicialesSacdService->obtenerIniciales($id_sacd_int);
            $key = $id_sacd_int . '#' . $iniciales;
            $a_sacd[$key] = $sacd !== '' ? $sacd : '?';
        }

        $oConfig = $_SESSION['oConfig'] ?? null;
        $esJefeCalendario = is_object($oConfig)
            && method_exists($oConfig, 'is_jefeCalendario')
            && $oConfig->is_jefeCalendario();

        if ((($aRoles[$id_role] ?? '') === 'Oficial_dl') || $esJefeCalendario) {
            $aWhere = [];
            $aOperador = [];
            $aWhere['sacd'] = 't';
            $aWhere['situacion'] = 'A';
            $aWhere['id_tabla'] = "'n','a'";
            $aOperador['id_tabla'] = 'IN';
            $aWhere['_ordre'] = 'apellido1,apellido2,nom';
            $cPersonas = $this->personaSacdRepository->getPersonas($aWhere, $aOperador);
            foreach ($cPersonas as $oPersona) {
                $id_nom = $oPersona->getId_nom();
                $sacd = $this->inicialesSacdService->obtenerNombreConIniciales($id_nom);
                $iniciales = $this->inicialesSacdService->obtenerIniciales($id_nom);
                $key = $id_nom . '#' . $iniciales;
                $a_sacd[$key] = $sacd !== '' ? $sacd : '?';
            }
        }

        $selected = '';
        if ($a_sacd !== []) {
            reset($a_sacd);
            $selected = (string) key($a_sacd);
        }

        return [
            'sacd_opciones' => $a_sacd,
            'sacd_selected' => $selected,
        ];
    }
}
