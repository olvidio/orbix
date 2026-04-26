<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\application\services\ResumenPlazasService;
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
    /**
     * @return array{
     *     id_activ:int,
     *     nom_activ:string,
     *     publicado:bool,
     *     otra_dl:bool,
     *     a_plazas:array,
     *     plazas_totales:int,
     *     tot_calendario:int,
     *     tot_cedidas:int,
     *     tot_conseguidas:int,
     *     tot_disponibles:int,
     *     tot_ocupadas:int,
     *     dl_opciones:array,
     *     error?:string
     * }
     */
    public static function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $nom_activ = (string)($input['nom_activ'] ?? '');
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

        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($id_activ);
        $publicado = false;
        $otra_dl = false;
        if ($oActividad !== null) {
            $pub = $oActividad->isPublicado();
            $publicado = is_true($pub) && $pub !== null;
            if ($oActividad->getDl_org() !== ConfigGlobal::mi_delef()) {
                $otra_dl = true;
            }
        }

        /** @var ResumenPlazasService $gesActividadPlazas */
        $gesActividadPlazas = $GLOBALS['container']->get(ResumenPlazasService::class);
        $gesActividadPlazas->setId_activ($id_activ);
        $a_plazas = $gesActividadPlazas->getResumen();

        $oDBPropiedades = new DBPropiedades();
        $dl_opciones = $oDBPropiedades->array_posibles_esquemas(true);

        $tot = $a_plazas['total'] ?? [];
        return [
            'id_activ' => $id_activ,
            'nom_activ' => $nom_activ,
            'publicado' => $publicado,
            'otra_dl' => $otra_dl,
            'a_plazas' => $a_plazas,
            'plazas_totales' => (int)($tot['actividad'] ?? 0),
            'tot_calendario' => (int)($tot['calendario'] ?? 0),
            'tot_cedidas' => (int)($tot['cedidas'] ?? 0),
            'tot_conseguidas' => (int)($tot['conseguidas'] ?? 0),
            'tot_disponibles' => (int)($tot['disponibles'] ?? 0),
            'tot_ocupadas' => (int)($tot['ocupadas'] ?? 0),
            'dl_opciones' => is_array($dl_opciones) ? $dl_opciones : [],
        ];
    }
}
