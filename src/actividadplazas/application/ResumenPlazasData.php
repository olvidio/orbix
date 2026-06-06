<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\application\services\ResumenPlazasService;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Data builder de la pantalla resumen de plazas por actividad.
 *
 * Sucesor de `apps/actividadplazas/controller/resumen_plazas.php`.
 * Devuelve datos planos (sin objetos UI): arrays de plazas, totales,
 * opciones del desplegable de dl y flags (publicado, otra_dl).
 */
final class ResumenPlazasData
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ResumenPlazasService $resumenPlazasService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *     id_activ:int,
     *     nom_activ:string,
     *     publicado:bool,
     *     otra_dl:bool,
     *     a_plazas:array<string, mixed>,
     *     plazas_totales:int,
     *     tot_calendario:int,
     *     tot_cedidas:int,
     *     tot_conseguidas:int,
     *     tot_disponibles:int,
     *     tot_ocupadas:int,
     *     dl_opciones:array<string, string>,
     *     error?:string
     * }
     */
    public function execute(array $input): array
    {
        $id_activ = input_int($input, 'id_activ');
        $nom_activ = input_string($input, 'nom_activ');
        if ($id_activ <= 0) {
            return [
                'error' => (string)_("falta parametro id_activ"),
                'id_activ' => 0,
                'nom_activ' => $nom_activ,
                'publicado' => false,
                'otra_dl' => false,
                'a_plazas' => [],
                'plazas_totales' => 0,
                'tot_calendario' => 0,
                'tot_cedidas' => 0,
                'tot_conseguidas' => 0,
                'tot_disponibles' => 0,
                'tot_ocupadas' => 0,
                'dl_opciones' => [],
            ];
        }

        $oActividad = $this->actividadAllRepository->findById($id_activ);
        $publicado = false;
        $otra_dl = false;
        if ($oActividad !== null) {
            $pub = $oActividad->isPublicado();
            $publicado = is_true($pub) && $pub !== null;
            if ($oActividad->getDl_org() !== ConfigGlobal::mi_delef()) {
                $otra_dl = true;
            }
        }

        $this->resumenPlazasService->setId_activ($id_activ);
        $a_plazas = $this->resumenPlazasService->getResumen();

        $oDBPropiedades = new DBPropiedades();
        $dl_opciones = $oDBPropiedades->array_posibles_esquemas(true);

        $totRaw = $a_plazas['total'] ?? [];
        $tot = is_array($totRaw) ? $totRaw : [];
        $dl_opciones_out = [];
        if (is_array($dl_opciones)) {
            foreach ($dl_opciones as $key => $label) {
                if (is_string($key) && (is_string($label) || is_int($label))) {
                    $dl_opciones_out[$key] = (string)$label;
                }
            }
        }

        return [
            'id_activ' => $id_activ,
            'nom_activ' => $nom_activ,
            'publicado' => $publicado,
            'otra_dl' => $otra_dl,
            'a_plazas' => $a_plazas,
            'plazas_totales' => is_numeric($tot['actividad'] ?? null) ? (int)$tot['actividad'] : 0,
            'tot_calendario' => is_numeric($tot['calendario'] ?? null) ? (int)$tot['calendario'] : 0,
            'tot_cedidas' => is_numeric($tot['cedidas'] ?? null) ? (int)$tot['cedidas'] : 0,
            'tot_conseguidas' => is_numeric($tot['conseguidas'] ?? null) ? (int)$tot['conseguidas'] : 0,
            'tot_disponibles' => is_numeric($tot['disponibles'] ?? null) ? (int)$tot['disponibles'] : 0,
            'tot_ocupadas' => is_numeric($tot['ocupadas'] ?? null) ? (int)$tot['ocupadas'] : 0,
            'dl_opciones' => $dl_opciones_out,
        ];
    }
}
