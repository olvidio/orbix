<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use frontend\shared\web\Periodo;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Data builder para la pantalla "comunicacion de actividades a los sacd".
 */
final class ComunicacionActividadesSacdData
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private RoleRepositoryInterface $roleRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private PersonaExRepositoryInterface $personaExRepository,
        private ComunicarActividadesSacdService $comunicarActividadesSacdService,
        private ActividadesSacdHelper $actividadesSacdHelper,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $context = $this->resolverContexto($input);
        $que = $context['que'];
        $propuesta = $context['propuesta'];
        $inicioIso = $context['inicioIso'];
        $finIso = $context['finIso'];
        $periodo_txt = $context['periodo_txt'];

        $mi_dele = (string)ConfigGlobal::mi_delef();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $poblacion = $this->actividadesSacdHelper->getLugar_dl();
        $lugar_fecha = "$poblacion, $hoy_local";

        if ($inicioIso === '' || $finIso === '') {
            return [
                'que' => $que,
                'propuesta' => $propuesta,
                'mi_dele' => $mi_dele,
                'lugar_fecha' => $lugar_fecha,
                'periodo_txt' => $periodo_txt,
                'sacds' => [],
                'sacds_paso' => [],
                'mensaje_periodo' => _("falta determinar un periodo"),
            ];
        }

        $cPersonas = $this->cargarPersonas($que, $context['id_nom'], $mi_dele);

        $service = clone $this->comunicarActividadesSacdService;
        $service->setInicioIso($inicioIso);
        $service->setFinIso($finIso);
        $service->setPropuesta($propuesta);
        $service->setPersonas($cPersonas);
        $sacds = $service->getArrayComunicacion();

        $sacds_paso = [];
        if ($que !== 'un_sacd') {
            $cPersonasPaso = $this->personaExRepository->getPersonas([
                'situacion' => 'A',
                'sacd' => 't',
                'dl' => $mi_dele,
                '_ordre' => 'apellido1,apellido2,nom',
            ]);

            $servicePaso = clone $this->comunicarActividadesSacdService;
            $servicePaso->setInicioIso($inicioIso);
            $servicePaso->setFinIso($finIso);
            $servicePaso->setPropuesta($propuesta);
            $servicePaso->setSoloCargos(true);
            $servicePaso->setQuitarInactivos(true);
            $servicePaso->setPersonas($cPersonasPaso);
            $sacds_paso = $servicePaso->getArrayComunicacion();
        }

        return [
            'que' => $que,
            'propuesta' => $propuesta,
            'mi_dele' => $mi_dele,
            'lugar_fecha' => $lugar_fecha,
            'periodo_txt' => $periodo_txt,
            'sacds' => $this->normalizarSacds($sacds),
            'sacds_paso' => $this->normalizarSacds($sacds_paso),
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{que: string, id_nom: int, propuesta: string, inicioIso: string, finIso: string, periodo_txt: string}
     */
    public function resolverContexto(array $input): array
    {
        $que = input_string($input, 'que');
        $id_nom = input_int($input, 'id_nom');
        $propuesta = input_string($input, 'propuesta');

        $periodo = input_string($input, 'periodo');
        $year = input_string($input, 'year');
        $empiezamin = input_string($input, 'empiezamin');
        $empiezamax = input_string($input, 'empiezamax');

        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario !== null) {
            $id_role = $oMiUsuario->getId_role();
            $aRoles = $this->roleRepository->getArrayRoles();
            if (!empty($aRoles[$id_role]) && $aRoles[$id_role] === 'p-sacd') {
                $csvId = $oMiUsuario->getCsvIdPauAsString();
                $id_nom = is_numeric($csvId) ? (int)$csvId : 0;
                $que = 'un_sacd';
            }
        }

        $a_sel = $input['sel'] ?? [];
        if (is_array($a_sel) && !empty($a_sel) && is_string($a_sel[0] ?? null)) {
            $id_nom = (int)strtok($a_sel[0], '#');
            if ($que === '') {
                $que = 'un_sacd';
            }
        }

        if ($que === '') {
            $que = 'nagd';
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($year);
        $oPeriodo->setEmpiezaMin($empiezamin);
        $oPeriodo->setEmpiezaMax($empiezamax);
        $oPeriodo->setPeriodo($periodo);
        $inicioIso = (string)$oPeriodo->getF_ini_iso();
        $finIso = (string)$oPeriodo->getF_fin_iso();
        $periodo_txt = sprintf(_("atención actividades para el periodo %s"), $oPeriodo->getTxt_cusro());

        if ($que === 'un_sacd') {
            $y = (int)$year;
            if ($y === 0) {
                $y = (int)date('Y');
            }
            $inicioIso = ($y - 1) . '-07-01';
            $finIso = ($y + 1) . '-06-30';
            $periodo_txt = sprintf(_("atención actividades para el periodo %s"), "$inicioIso / $finIso");
        }

        return [
            'que' => $que,
            'id_nom' => $id_nom,
            'propuesta' => $propuesta,
            'inicioIso' => $inicioIso,
            'finIso' => $finIso,
            'periodo_txt' => $periodo_txt,
        ];
    }

    /**
     * @return list<PersonaSacd>
     */
    private function cargarPersonas(string $que, int $id_nom, string $mi_dele): array
    {
        switch ($que) {
            case 'nagd':
                /** @var list<PersonaSacd> $personas */
                $personas = $this->personaSacdRepository->getPersonas(
                    [
                        'id_tabla' => "'n','a'",
                        'situacion' => 'A',
                        'sacd' => 't',
                        'dl' => $mi_dele,
                        '_ordre' => 'apellido1,apellido2,nom',
                    ],
                    ['id_tabla' => 'IN']
                );
                return $personas;
            case 'sssc':
                /** @var list<PersonaSacd> $personas */
                $personas = $this->personaSacdRepository->getPersonas([
                    'id_tabla' => 'sssc',
                    'situacion' => 'A',
                    'sacd' => 't',
                    'dl' => $mi_dele,
                    '_ordre' => 'apellido1,apellido2,nom',
                ]);
                return $personas;
            case 'un_sacd':
                $oPersona = $this->personaSacdRepository->findById($id_nom);
                return $oPersona === null ? [] : [$oPersona];
        }
        return [];
    }

    /**
     * @param array<int, array<string, mixed>> $sacds
     * @return array<int, array<string, mixed>>
     */
    private function normalizarSacds(array $sacds): array
    {
        $out = [];
        foreach ($sacds as $id_nom => $vector) {
            $out[] = [
                'id_nom' => (int)$id_nom,
                'nom_ap' => $this->mixedToString($vector['nom_ap'] ?? ''),
                'txt' => (array)($vector['txt'] ?? []),
                'actividades' => (array)($vector['actividades'] ?? []),
            ];
        }
        return $out;
    }

    private function mixedToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }
        if (is_bool($value)) {
            return $value ? 't' : 'f';
        }
        return '';
    }
}
