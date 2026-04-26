<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use frontend\shared\web\Periodo;

/**
 * Data builder para la pantalla "comunicacion de actividades a los sacd".
 *
 * Engloba toda la logica que el legacy `com_sacd_activ.php` hacia antes
 * de pintar el HTML: resolver `que`, aplicar regla `p-sacd`, calcular el
 * periodo ISO, cargar las personas, construir el listado principal y (si
 * procede) la lista de "sacd de paso".
 *
 * Sucesor de la rama `Qmail === 'no'` del dispatcher legacy.
 */
final class ComunicacionActividadesSacdData
{
    /**
     * @param array $input campos POST del form (que, id_nom, propuesta,
     *   periodo, year, empiezamin, empiezamax, sel[]).
     *
     * @return array{
     *   que: string,
     *   propuesta: string,
     *   mi_dele: string,
     *   lugar_fecha: string,
     *   periodo_txt: string,
     *   sacds: array<int, array<string, mixed>>,
     *   sacds_paso: array<int, array<string, mixed>>
     * }
     */
    public static function execute(array $input): array
    {
        $context = self::resolverContexto($input);
        $que = $context['que'];
        $id_nom = $context['id_nom'];
        $propuesta = $context['propuesta'];
        $inicioIso = $context['inicioIso'];
        $finIso = $context['finIso'];
        $periodo_txt = $context['periodo_txt'];

        $mi_dele = (string)ConfigGlobal::mi_delef();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $poblacion = (new ActividadesSacdHelper())->getLugar_dl();
        $lugar_fecha = "$poblacion, $hoy_local";

        // Sin periodo valido no podemos seguir.
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

        $cPersonas = self::cargarPersonas($que, $id_nom, $mi_dele);

        $service = new ComunicarActividadesSacdService();
        $service->setInicioIso($inicioIso);
        $service->setFinIso($finIso);
        $service->setPropuesta($propuesta);
        $service->setPersonas($cPersonas);
        $sacds = $service->getArrayComunicacion();

        // "Sacd de paso" solo cuando hay varios sacds (no un_sacd).
        $sacds_paso = [];
        if ($que !== 'un_sacd') {
            $PersonaExRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
            $cPersonasPaso = $PersonaExRepository->getPersonas([
                'situacion' => 'A',
                'sacd' => 't',
                'dl' => $mi_dele,
                '_ordre' => 'apellido1,apellido2,nom',
            ]);

            $servicePaso = new ComunicarActividadesSacdService();
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
            'sacds' => self::normalizarSacds($sacds),
            'sacds_paso' => self::normalizarSacds($sacds_paso),
        ];
    }

    /**
     * Resuelve `que`, `id_nom`, `propuesta` y periodo ISO a partir del input.
     *
     * @return array{que:string, id_nom:int, propuesta:string, inicioIso:string, finIso:string, periodo_txt:string}
     */
    public static function resolverContexto(array $input): array
    {
        $que = (string)($input['que'] ?? '');
        $id_nom = (int)($input['id_nom'] ?? 0);
        $propuesta = (string)($input['propuesta'] ?? '');

        $periodo = (string)($input['periodo'] ?? '');
        $year = (string)($input['year'] ?? '');
        $empiezamin = (string)($input['empiezamin'] ?? '');
        $empiezamax = (string)($input['empiezamax'] ?? '');

        // Si un usuario con rol 'p-sacd' entra, solo puede verse a si mismo.
        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario !== null) {
            $id_role = $oMiUsuario->getId_role();
            $aRoles = $RoleRepository->getArrayRoles();
            if (!empty($aRoles[$id_role]) && $aRoles[$id_role] === 'p-sacd') {
                $id_nom = (int)$oMiUsuario->getCsvIdPauAsString();
                $que = 'un_sacd';
            }
        }

        // `sel[]` viene de personas_select; `id_nom#id_tabla`.
        $a_sel = $input['sel'] ?? [];
        if (is_array($a_sel) && !empty($a_sel)) {
            $id_nom = (int)strtok((string)$a_sel[0], '#');
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

        // Para `un_sacd` el legacy aplica un periodo por defecto (curso
        // cruzado: 01-07 del any-1 al 30-06 del any+1).
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
     * @return array<int, mixed>
     */
    private static function cargarPersonas(string $que, int $id_nom, string $mi_dele): array
    {
        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);

        switch ($que) {
            case 'nagd':
                return $PersonaSacdRepository->getPersonas(
                    [
                        'id_tabla' => "'n','a'",
                        'situacion' => 'A',
                        'sacd' => 't',
                        'dl' => $mi_dele,
                        '_ordre' => 'apellido1,apellido2,nom',
                    ],
                    ['id_tabla' => 'IN']
                );
            case 'sssc':
                return $PersonaSacdRepository->getPersonas([
                    'id_tabla' => 'sssc',
                    'situacion' => 'A',
                    'sacd' => 't',
                    'dl' => $mi_dele,
                    '_ordre' => 'apellido1,apellido2,nom',
                ]);
            case 'un_sacd':
                $oPersona = $PersonaSacdRepository->findById($id_nom);
                return $oPersona === null ? [] : [$oPersona];
        }
        return [];
    }

    /**
     * Convierte el array indexado por id_nom a una lista plana adecuada
     * para JSON, incluyendo `id_nom` explicitamente en cada entrada.
     *
     * @param array<int, array<string, mixed>> $sacds
     * @return array<int, array<string, mixed>>
     */
    private static function normalizarSacds(array $sacds): array
    {
        $out = [];
        foreach ($sacds as $id_nom => $vector) {
            $out[] = [
                'id_nom' => (int)$id_nom,
                'nom_ap' => (string)($vector['nom_ap'] ?? ''),
                'txt' => (array)($vector['txt'] ?? []),
                'actividades' => (array)($vector['actividades'] ?? []),
            ];
        }
        return $out;
    }
}
