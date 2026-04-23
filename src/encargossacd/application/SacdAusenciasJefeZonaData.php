<?php

namespace src\encargossacd\application;

use core\ConfigGlobal;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;
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
    /**
     * @return array{ a_sacd: array<string, string> }
     */
    public static function execute(): array
    {
        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return ['a_sacd' => []];
        }
        $id_role = (int)$oMiUsuario->getId_role();
        $id_sacd = $oMiUsuario->getCsvIdPauVo()?->value();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $cZonas = $ZonaRepository->getZonas(['id_nom' => $id_sacd]);

        $InicialesSacdRepository = $GLOBALS['container']->get(InicialesSacdRepositoryInterface::class);
        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);

        $a_sacd = [];

        if (is_array($cZonas) && count($cZonas) > 0) {
            $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
            foreach ($cZonas as $oZona) {
                $id_zona = $oZona->getId_zona();
                $cSacds = $ZonaSacdRepository->getIdSacdsDeZona($id_zona);
                foreach ($cSacds as $id_nom) {
                    $oPersonaSacd = $PersonaSacdRepository->findById($id_nom);
                    $sacd = $oPersonaSacd?->getNombreApellidos();
                    $oInicialesSacd = $InicialesSacdRepository->findById($id_nom);
                    $iniciales = $oInicialesSacd?->getIniciales() ?? '';
                    $key = $iniciales . '#' . $id_nom;
                    $a_sacd[$key] = (string)($sacd ?? '?');
                }
            }
        } elseif (!is_null($id_sacd)) {
            $oPersonaSacd = $PersonaSacdRepository->findById($id_sacd);
            $sacd = $oPersonaSacd?->getNombreApellidos();
            $oInicialesSacd = $InicialesSacdRepository->findById($id_sacd);
            $iniciales = $oInicialesSacd?->getIniciales() ?? '';
            $key = $iniciales . '#' . $id_sacd;
            $a_sacd[$key] = (string)($sacd ?? '?');
        }

        $es_jefe_calendario = isset($_SESSION['oConfig'])
            && method_exists($_SESSION['oConfig'], 'is_jefeCalendario')
            && $_SESSION['oConfig']->is_jefeCalendario();

        if (($aRoles[$id_role] ?? '') === 'Oficial_dl' || $es_jefe_calendario) {
            $cPersonas = $PersonaSacdRepository->getPersonas(
                [
                    'sacd' => 't',
                    'situacion' => 'A',
                    'id_tabla' => "'n','a'",
                    '_ordre' => 'apellido1,apellido2,nom',
                ],
                ['id_tabla' => 'IN'],
            ) ?: [];
            foreach ($cPersonas as $oPersona) {
                $id_nom = $oPersona->getId_nom();
                $oInicialesSacd = new InicialesSacd();
                $sacd = $oInicialesSacd->nombre_sacd($id_nom);
                $iniciales = $oInicialesSacd->iniciales($id_nom);
                $key = $iniciales . '#' . $id_nom;
                $a_sacd[$key] = (string)($sacd ?? '?');
            }
        }

        ksort($a_sacd);

        return ['a_sacd' => $a_sacd];
    }
}
