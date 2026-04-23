<?php

namespace src\actividadessacd\application;

use core\ConfigGlobal;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\Persona;

/**
 * Devuelve la lista de sacd candidatos para asignar a una actividad.
 *
 * Se separan en dos bloques:
 *  - `sacds_ctr`: sacd del (o de los) centro/s encargado/s de la
 *    actividad, ordenados por `num_orden` del centro. Solo aplica si
 *    la app `encargossacd` esta instalada.
 *  - `sacds_todos`: listado global de sacd filtrados por la bitmask
 *    `seleccion` (`2=n/a, 4=paso, 8=sssc, 16=cp`).
 *
 * Sucesor de la rama `nuevo` del dispatcher legacy
 * `apps/actividadessacd/controller/activ_sacd_ajax.php`.
 */
final class SacdsDisponiblesData
{
    public static function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $seleccion = (int)($input['seleccion'] ?? 0);

        $sacds_ctr = [];
        if (ConfigGlobal::is_app_installed('encargossacd') && $id_activ > 0) {
            $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
            $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
            $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);

            $cCentros = $CentroEncargadoRepository->getCentrosEncargados(
                ['id_activ' => $id_activ, '_ordre' => 'num_orden']
            );
            if (is_array($cCentros)) {
                foreach ($cCentros as $oEncargado) {
                    $id_ctr = (int)$oEncargado->getId_ubi();
                    $num_orden = (int)$oEncargado->getNum_orden();
                    // Tipos de encargo de atencion centro (no rt):
                    // 1000, 1100, 1200, 1300 (y todo 100 generico sacd cl: 1001).
                    $cEncargos = $EncargoRepository->getEncargos(
                        ['id_ubi' => $id_ctr, 'id_tipo_enc' => '^1(00|100|200|300)'],
                        ['id_tipo_enc' => '~']
                    );
                    if (!is_array($cEncargos) || count($cEncargos) < 1) {
                        continue;
                    }
                    $id_enc = $cEncargos[0]->getId_enc();
                    $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(
                        ['id_enc' => $id_enc, 'modo' => '2|3', 'f_fin' => ''],
                        ['modo' => '~', 'f_fin' => 'IS NULL']
                    );
                    if (!is_array($cEncargosSacd) || count($cEncargosSacd) < 1) {
                        continue;
                    }
                    $id_nom = (int)$cEncargosSacd[0]->getId_nom();
                    $oPersona = Persona::findPersonaEnGlobal($id_nom);
                    $ap_nom = is_object($oPersona)
                        ? (string)$oPersona->getApellidosNombre()
                        : (string)$oPersona;
                    $sacds_ctr[] = [
                        'id_nom' => $id_nom,
                        'ap_nom' => $ap_nom,
                        'num_orden' => $num_orden,
                    ];
                }
            }
        }

        $sacds_todos = [];
        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        $cPersonas = $PersonaSacdRepository->getSacdsBySelect($seleccion);
        if (is_array($cPersonas)) {
            foreach ($cPersonas as $oPersona) {
                $sacds_todos[] = [
                    'id_nom' => (int)$oPersona->getId_nom(),
                    'ap_nom' => (string)$oPersona->getPrefApellidosNombre(),
                ];
            }
        }

        return [
            'id_activ' => $id_activ,
            'sacds_ctr' => $sacds_ctr,
            'sacds_todos' => $sacds_todos,
        ];
    }
}
