<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\input_int;

/**
 * Devuelve la lista de sacd candidatos para asignar a una actividad.
 */
final class SacdsDisponiblesData
{
    public function __construct(
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $id_activ = input_int($input, 'id_activ');
        $seleccion = input_int($input, 'seleccion');

        $sacds_ctr = [];
        if (ConfigGlobal::is_app_installed('encargossacd') && $id_activ > 0) {
            $cCentros = $this->centroEncargadoRepository->getCentrosEncargados(
                ['id_activ' => $id_activ, '_ordre' => 'num_orden']
            );
            foreach ($cCentros as $oEncargado) {
                $id_ctr = (int)$oEncargado->getId_ubi();
                $num_orden = (int)$oEncargado->getNum_orden();
                $cEncargos = $this->encargoRepository->getEncargos(
                    ['id_ubi' => $id_ctr, 'id_tipo_enc' => '^1(00|100|200|300)'],
                    ['id_tipo_enc' => '~']
                );
                if (count($cEncargos) < 1) {
                    continue;
                }
                $id_enc = (int)$cEncargos[0]->getId_enc();
                $cEncargosSacd = $this->encargoSacdRepository->getEncargosSacd(
                    ['id_enc' => $id_enc, 'modo' => '2|3', 'f_fin' => ''],
                    ['modo' => '~', 'f_fin' => 'IS NULL']
                );
                if (count($cEncargosSacd) < 1) {
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

        $sacds_todos = [];
        $cPersonas = $this->personaSacdRepository->getSacdsBySelect($seleccion);
        foreach ($cPersonas as $oPersona) {
            $sacds_todos[] = [
                'id_nom' => (int)$oPersona->getId_nom(),
                'ap_nom' => (string)$oPersona->getPrefApellidosNombre(),
            ];
        }

        return [
            'id_activ' => $id_activ,
            'sacds_ctr' => $sacds_ctr,
            'sacds_todos' => $sacds_todos,
        ];
    }
}
