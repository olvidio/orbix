<?php

use src\misas\application\support\EncargoDiaTimeHelper;
use src\misas\application\support\MisasBuildInput;

/**
 * Funcion global que construye la cuadricula de "ver misas zona".
 *
 * Vive fuera de cualquier `namespace` para conservar las variables globales
 * que heredaba el controlador legacy, pero la logica procedural ya vive en
 * este mismo fichero (no en un fragmento aparte) para evitar duplicar los
 * `use` y tener una sola fuente de verdad.
 */

use src\encargossacd\domain\EncargoConstants;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

/**
 * @see \src\misas\application\VerMisasZonaData::build()
 * @param array<string, mixed> $in
 * @return array<string, mixed>
 */
function misas_ver_misas_zona_build(array $in, \src\misas\application\VerMisasZonaData $self): array
{
    $Qid_zona = MisasBuildInput::int($in, 'id_zona');
    $QEmpiezaMin = MisasBuildInput::string($in, 'empiezamin');
    $QEmpiezaMax = MisasBuildInput::string($in, 'empiezamax');
    $Qseleccion = MisasBuildInput::int($in, 'seleccion');

    try {
        [$columns_cuadricula, $data_cuadricula] = _misas_ver_misas_zona_grid(
            $Qid_zona,
            $QEmpiezaMin,
            $QEmpiezaMax,
            $Qseleccion,
            $self
        );
    } catch (\RuntimeException $e) {
        return [
            'error' => $e->getMessage(),
            'columns_cuadricula' => '[]',
            'data_cuadricula' => [],
            'id_zona' => $Qid_zona,
            'seleccion' => $Qseleccion,
            'empieza_min' => $QEmpiezaMin,
            'empieza_max' => $QEmpiezaMax,
        ];
    }

    return [
        'error' => '',
        'columns_cuadricula' => json_encode($columns_cuadricula, JSON_UNESCAPED_UNICODE),
        'data_cuadricula' => $data_cuadricula,
        'id_zona' => $Qid_zona,
        'seleccion' => $Qseleccion,
        'empieza_min' => $QEmpiezaMin,
        'empieza_max' => $QEmpiezaMax,
    ];
}

/**
 * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}
 */
function _misas_ver_misas_zona_grid(int $Qid_zona, string $QEmpiezaMin, string $QEmpiezaMax, int $Qseleccion, \src\misas\application\VerMisasZonaData $self): array
{
    $dmYtoIso = static function (string $dmY): string {
        $partes = explode('/', $dmY);
        if (count($partes) !== 3) {
            return date('Y-m-d');
        }

        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    };

    $isoMin = $dmYtoIso($QEmpiezaMin) . ' 00:00:00';
    $isoMax = $dmYtoIso($QEmpiezaMax) . ' 23:59:59';

    $a_iniciales = [];
    $a_sacd = [];

    $PersonaSacdRepository = $self->getPersonaSacdRepository();

    if ($Qseleccion & 2) {
        // `getIdSacdsDeZona` vive en `ZonaSacdRepositoryInterface`, no en
        // `ZonaRepositoryInterface`; usar el repo equivocado revienta con
        // "Call to undefined method" al ejecutar esta rama.
        $ZonaSacdRepository = $self->getZonaSacdRepository();
        $a_Id_nom = $ZonaSacdRepository->getIdSacdsDeZona($Qid_zona);

        foreach ($a_Id_nom as $id_nom) {
            $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
            if ($PersonaSacd === null) {
                continue;
            }
            $sacd = $PersonaSacd->getNombreApellidos();
            $nom = mb_substr($PersonaSacd->getNom() ?? '', 0, 1);
            $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
            $ap2 = mb_substr($PersonaSacd->getApellido2() ?? '', 0, 1);
            $iniciales = strtoupper($nom . $ap1 . $ap2);

            $a_iniciales[$id_nom] = $iniciales;

            $key = $id_nom . '#' . $iniciales;

            $a_sacd[$key] = $sacd;
        }
    }
    if ($Qseleccion & 4) {
        $aWhere = [];
        $aOperador = [];
        $aWhere['sacd'] = 't';
        $aWhere['situacion'] = 'A';
        $aWhere['id_tabla'] = "'n','a'";
        $aOperador['id_tabla'] = 'IN';
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';
        $cPersonas = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
        foreach ($cPersonas as $oPersonaSacd) {
            $id_nom = $oPersonaSacd->getId_nom();
            $sacd = $oPersonaSacd->getNombreApellidos();
            $nom = mb_substr($oPersonaSacd->getNom() ?? '', 0, 1);
            $ap1 = mb_substr($oPersonaSacd->getApellido1(), 0, 1);
            $ap2 = mb_substr($oPersonaSacd->getApellido2() ?? '', 0, 1);
            $iniciales = strtoupper($nom . $ap1 . $ap2);

            $a_iniciales[$id_nom] = $iniciales;

            $key = $id_nom . '#' . $iniciales;

            $a_sacd[$key] = $sacd;
        }
    }
    if ($Qseleccion & 8) {
        $aWhere = [];
        $aOperador = [];
        $aWhere['sacd'] = 't';
        $aWhere['situacion'] = 'A';
        $aWhere['_ordre'] = 'apellido1,apellido2,nom';

        $cPersonas = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
        foreach ($cPersonas as $oPersonaSacd) {
            $id_nom = $oPersonaSacd->getId_nom();
            $sacd = $oPersonaSacd->getNombreApellidos();
            $nom = mb_substr($oPersonaSacd->getNom() ?? '', 0, 1);
            $ap1 = mb_substr($oPersonaSacd->getApellido1(), 0, 1);
            $ap2 = mb_substr($oPersonaSacd->getApellido2() ?? '', 0, 1);
            $iniciales = strtoupper($nom . $ap1 . $ap2);

            $a_iniciales[$id_nom] = $iniciales;

            $key = $id_nom . '#' . $iniciales;

            $a_sacd[$key] = $sacd;
        }
    }

    $columns_cuadricula = [
        ['id' => 'encargo', 'name' => 'Encargo', 'field' => 'encargo', 'width' => 150, 'cssClass' => 'cell-title'],
    ];

    $oInicio = new DateTimeLocal($isoMin);
    $oFin = new DateTimeLocal($isoMax);
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($oInicio, $interval, $oFin);
    $a_dates = iterator_to_array($date_range);

    $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
    foreach ($a_dates as $date) {
        $num_dia = $date->format('Y-m-d');
        $dia_week = $date->format('N');
        $nom_dia = $a_dias_semana[$dia_week];

        $columns_cuadricula[] =
            ['id' => $num_dia, 'name' => $nom_dia, 'field' => $num_dia, 'width' => 80, 'cssClass' => 'cell-title'];
    }

    $data_cuadricula = [];
    $a_tipo_enc = [8010, 8011];
    $EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin, $self->getEncargoHorarioRepository(), $self->getEncargoRepository());
    $EncargosZona->setATipoEnc($a_tipo_enc);
    $cEncargosZona = $EncargosZona->getEncargos();

    $EncargoDiaRepository = $self->getEncargoDiaRepository();

    foreach ($cEncargosZona as $oEncargo) {
        $id_enc = $oEncargo->getId_enc();
        $desc_enc = $oEncargo->getDesc_enc();
        $data_cols = [];
        $meta_dia = [];
        foreach ($a_dates as $date) {
            $num_dia = $date->format('Y-m-d');

            $data_cols[$num_dia] = ' -- ';

            $meta_dia[$num_dia] = [
                'uuid_item' => '',
                'color' => '',
                'key' => '',
                'tstart' => '',
                'tend' => '',
                'observ' => '',
                'id_enc' => $id_enc,
                'dia' => $num_dia,
                'tipo' => '',
                'texto' => '',
            ];

            $inicio_dia = $num_dia . ' 00:00:00';
            $fin_dia = $num_dia . ' 23:59:59';
            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia', '$fin_dia'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

            if (count($cEncargosDia) > 1) {
                throw new \RuntimeException(_('solo deberia haber uno'));
            }

            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                $hora_ini = EncargoDiaTimeHelper::format($oEncargoDia->getTstart(), 'H:i');
                if ($hora_ini === '00:00') {
                    $hora_ini = '';
                }
                $iniciales = $a_iniciales[$id_nom] ?? '';
                $color = '';

                $meta_dia[$num_dia] = [
                    'uuid_item' => $oEncargoDia->getUuidItemVo()->value(),
                    'color' => $color,
                    'key' => "$id_nom#$iniciales",
                    'tstart' => EncargoDiaTimeHelper::hora($oEncargoDia->getTstart()),
                    'tend' => EncargoDiaTimeHelper::hora($oEncargoDia->getTend()),
                    'observ' => $oEncargoDia->getObserv(),
                    'id_enc' => $id_enc,
                    'dia' => $num_dia,
                    'tipo' => 'misas',
                    'texto' => '',
                ];
                $iniciales_out = $iniciales . (empty($oEncargoDia->getObserv()) ? '' : '*');
                $data_cols[$num_dia] = $iniciales_out . ' ' . $hora_ini;
            }
        }
        $data_cols['encargo'] = $desc_enc;
        $data_cols['meta'] = $meta_dia;
        $data_cuadricula[] = $data_cols;
    }

    return [$columns_cuadricula, $data_cuadricula];
}
