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
    /**
     * @return array{sacd_opciones: array<string, string>, sacd_selected: string}
     */
    public static function getData(): array
    {
        $container = $GLOBALS['container'];

        $UsuarioRepository = $container->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $id_sacd = $oMiUsuario->getCsvIdPauAsString();

        $a_sacd = [];

        $RoleRepository = $container->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $ZonaRepository = $container->get(ZonaRepositoryInterface::class);
        $cZonas = $ZonaRepository->getZonas(['id_nom' => $id_sacd]);
        $InicialesSacdService = $container->get(InicialesSacdService::class);

        if (is_array($cZonas) && count($cZonas) > 0) {
            $ZonaSacdRepository = $container->get(ZonaSacdRepositoryInterface::class);
            foreach ($cZonas as $oZona) {
                $id_zona = $oZona->getId_zona();
                $a_id_nom = $ZonaSacdRepository->getIdSacdsDeZona($id_zona);
                foreach ($a_id_nom as $id_nom) {
                    $sacd = $InicialesSacdService->obtenerNombreConIniciales($id_nom);
                    $iniciales = $InicialesSacdService->obtenerIniciales($id_nom);
                    $key = $id_nom . '#' . $iniciales;
                    $a_sacd[$key] = $sacd ?? '?';
                }
            }
        } else {
            if (!is_null($id_sacd)) {
                $sacd = $InicialesSacdService->obtenerNombreConIniciales($id_sacd);
                $iniciales = $InicialesSacdService->obtenerIniciales($id_sacd);
                $key = $id_sacd . '#' . $iniciales;
                $a_sacd[$key] = $sacd ?? '?';
            }
        }

        if ((($aRoles[$id_role] ?? '') === 'Oficial_dl') || ($_SESSION['oConfig']->is_jefeCalendario())) {
            $aWhere = [];
            $aOperador = [];
            $aWhere['sacd'] = 't';
            $aWhere['situacion'] = 'A';
            $aWhere['id_tabla'] = "'n','a'";
            $aOperador['id_tabla'] = 'IN';
            $aWhere['_ordre'] = 'apellido1,apellido2,nom';
            $PersonaSacdRepository = $container->get(PersonaSacdRepositoryInterface::class);
            $cPersonas = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
            foreach ($cPersonas as $oPersona) {
                $id_nom = $oPersona->getId_nom();
                $sacd = $InicialesSacdService->obtenerNombreConIniciales($id_nom);
                $iniciales = $InicialesSacdService->obtenerIniciales($id_nom);
                $key = $id_nom . '#' . $iniciales;
                $a_sacd[$key] = $sacd ?? '?';
            }
        }

        $selected = '';
        if ($a_sacd !== []) {
            reset($a_sacd);
            $selected = (string)key($a_sacd);
        }

        return [
            'sacd_opciones' => $a_sacd,
            'sacd_selected' => $selected,
        ];
    }
}
