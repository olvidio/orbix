<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\application\services\InicialesSacdService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

/**
 * Opciones del desplegable dinámico de SACD en el modal de la cuadrícula de zona.
 *
 * El payload sigue el espíritu del contrato de `refactor.md` (id, selected, filas
 * ordenadas). `rows` conserva el orden del HTML legacy: opción actual, opción en
 * blanco si aplica, resto ordenado por clave.
 */
class DesplegableSacdData
{
    public static function getData(int $id_zona, int $id_sacd, int $seleccion, string $dia): array
    {
        $container = $GLOBALS['container'];
        $InicialesSacdService = $container->get(InicialesSacdService::class);

        $sacd = $InicialesSacdService->obtenerNombreConIniciales($id_sacd);
        $iniciales = $InicialesSacdService->obtenerIniciales($id_sacd);
        $firstKey = $iniciales . '#' . $id_sacd;

        $rows = [];
        $rows[] = ['value' => $firstKey, 'label' => $sacd];
        if ($id_sacd !== 0) {
            $rows[] = ['value' => '', 'label' => ''];
        }

        $lista_sacd = [];

        if ($seleccion & 1) {
            $ZonaSacdRepository = $container->get(ZonaSacdRepositoryInterface::class);
            $a_Id_nom = $ZonaSacdRepository->getIdSacdsDeZona($id_zona);
            $EncargoRepository = $container->get(EncargoRepositoryInterface::class);
            $EncargoDiaRepository = $container->get(EncargoDiaRepositoryInterface::class);

            foreach ($a_Id_nom as $id_nom) {
                $libre = true;
                $inicio_dia = $dia . ' 00:00:00';
                $fin_dia = $dia . ' 23:59:59';
                $aWhere = [
                    'id_nom' => $id_nom,
                    'tstart' => "'$inicio_dia', '$fin_dia'",
                ];
                $aOperador = [
                    'tstart' => 'BETWEEN',
                ];
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
                foreach ($cEncargosDia as $oEncargoDia) {
                    $id_enc = $oEncargoDia->getId_enc();
                    $aWhere = [];
                    $aOperador = [];
                    $aWhere['id_enc'] = $id_enc;
                    $cEncargos = $EncargoRepository->getEncargos($aWhere, $aOperador);
                    foreach ($cEncargos as $oEncargo) {
                        $id_tipo_enc = $oEncargo->getId_tipo_enc();
                        if ((int)substr((string)$id_tipo_enc, 1, 1) === 1) {
                            $libre = false;
                        }
                    }
                }
                if ($libre) {
                    $aWhere = [];
                    $aWhere['id_zona'] = $id_zona;
                    $aWhere['id_nom'] = $id_nom;
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere);
                    $dia_ts = strtotime($dia);
                    $n_dia_semana = date('N', $dia_ts);
                    $oZonaSacd = $cZonaSacd[0];
                    switch ($n_dia_semana) {
                        case 1:
                            $libre = $oZonaSacd->isDw1();
                            break;
                        case 2:
                            $libre = $oZonaSacd->isDw2();
                            break;
                        case 3:
                            $libre = $oZonaSacd->isDw3();
                            break;
                        case 4:
                            $libre = $oZonaSacd->isDw4();
                            break;
                        case 5:
                            $libre = $oZonaSacd->isDw5();
                            break;
                        case 6:
                            $libre = $oZonaSacd->isDw6();
                            break;
                        case 7:
                            $libre = $oZonaSacd->isDw7();
                            break;
                    }
                }
                if ($libre) {
                    $sacd_nom = $InicialesSacdService->obtenerNombreConIniciales($id_nom);
                    $iniciales_nom = $InicialesSacdService->obtenerIniciales($id_nom);
                    $key = $iniciales_nom . '#' . $id_nom;
                    $lista_sacd[$key] = $sacd_nom;
                }
            }
        }

        if ($seleccion & 2) {
            $ZonaSacdRepository = $container->get(ZonaSacdRepositoryInterface::class);
            $a_Id_nom = $ZonaSacdRepository->getIdSacdsDeZona($id_zona);
            foreach ($a_Id_nom as $id_nom) {
                $sacd_nom = $InicialesSacdService->obtenerNombreConIniciales($id_nom);
                $iniciales_nom = $InicialesSacdService->obtenerIniciales($id_nom);
                $key = $iniciales_nom . '#' . $id_nom;
                $lista_sacd[$key] = $sacd_nom;
            }
        }

        if ($seleccion & 4) {
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
                $sacd_nom = $InicialesSacdService->obtenerNombreConIniciales($id_nom);
                $iniciales_nom = $InicialesSacdService->obtenerIniciales($id_nom);
                $key = $iniciales_nom . '#' . $id_nom;
                $lista_sacd[$key] = $sacd_nom;
            }
        }

        if ($seleccion & 8) {
            $aWhere = [];
            $aOperador = [];
            $aWhere['sacd'] = 't';
            $aWhere['situacion'] = 'A';
            $aWhere['_ordre'] = 'apellido1,apellido2,nom';
            $PersonaSacdRepository = $container->get(PersonaSacdRepositoryInterface::class);
            $cPersonas = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
            foreach ($cPersonas as $oPersona) {
                $id_nom = $oPersona->getId_nom();
                $sacd_nom = $InicialesSacdService->obtenerNombreConIniciales($id_nom);
                $iniciales_nom = $InicialesSacdService->obtenerIniciales($id_nom);
                $key = $iniciales_nom . '#' . $id_nom;
                $lista_sacd[$key] = $sacd_nom;
            }
        }

        ksort($lista_sacd);
        foreach ($lista_sacd as $key => $label) {
            $rows[] = ['value' => $key, 'label' => $label];
        }

        return [
            'id' => 'id_sacd',
            'rows' => $rows,
            'selected' => $firstKey,
        ];
    }

    /** Misma forma que el JSON servido antes de la migración (`<SELECT ID="id_sacd">`). */
    public static function legacySelectHtml(array $payload): string
    {
        $html = '<SELECT ID="id_sacd">';
        foreach ($payload['rows'] as $row) {
            $v = htmlspecialchars((string)$row['value'], ENT_QUOTES, 'UTF-8');
            $l = htmlspecialchars((string)$row['label'], ENT_QUOTES, 'UTF-8');
            $html .= '<OPTION VALUE="' . $v . '">' . $l . '</OPTION>';
        }
        $html .= '</SELECT>';

        return $html;
    }
}
