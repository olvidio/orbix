<?php

namespace src\actividadessacd\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;

/**
 * Auto-asigna el sacd titular del centro encargado a las actividades de
 * sr/sg (`id_tipo_activ` regex `.(4|5|7)`) posteriores a `f_ini_iso`,
 * con `status = ACTUAL` y sin ningun cargo sacd todavia. Solo se asigna
 * a actividades con exactamente un centro encargado principal
 * (`num_orden = 0`) cuyo sacd titular se conoce.
 *
 * Sucesor de `apps/actividadessacd/model/AsignarSacd.php` (namespace
 * `actividadessacd\model`).
 */
final class SacdAsignarAuto
{
    private string $f_ini_iso;

    /** @var int[] actividades candidatas (ids indexados por f_ini). */
    private array $a_actividades = [];

    /** @var array<int,int> id_activ => id_ubi (centro encargado principal). */
    private array $a_activ_ctr = [];

    /** @var array<int,int> id_ubi => id_nom del sacd titular del centro. */
    private array $a_ctr_sacd = [];

    private function __construct(string $f_ini_iso)
    {
        $this->f_ini_iso = $f_ini_iso;
    }

    /**
     * @param array{f_ini_iso?: string} $input
     * @return array{asignadas:int, sin_asignar:int}
     */
    public static function execute(array $input): array
    {
        $f_ini_iso = (string)($input['f_ini_iso'] ?? '');
        if ($f_ini_iso === '') {
            return ['asignadas' => 0, 'sin_asignar' => 0];
        }
        return (new self($f_ini_iso))->run();
    }

    /**
     * @return array{asignadas:int, sin_asignar:int}
     */
    private function run(): array
    {
        $this->seleccionarActividades();
        $a_sin_sacd = $this->actividadesSinSacd();

        $asig = 0;
        foreach ($a_sin_sacd as $id_activ) {
            $asig += $this->asignarUna($id_activ);
        }
        $sin_asig = count($a_sin_sacd) - $asig;

        return ['asignadas' => $asig, 'sin_asignar' => $sin_asig];
    }

    private function seleccionarActividades(): void
    {
        $aWhere = [
            'id_tipo_activ' => '.(4|5|7)',
            'f_ini' => $this->f_ini_iso,
            'status' => StatusId::ACTUAL,
        ];
        $aOperador = [
            'id_tipo_activ' => '~',
            'f_ini' => '>',
        ];

        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $this->a_actividades = $ActividadDlRepository->getArrayIdsWithKeyFini($aWhere, $aOperador);
    }

    /**
     * @return int[] actividades sin ningun cargo sacd asignado.
     */
    private function actividadesSinSacd(): array
    {
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $aWhere = [
            'id_cargo' => $txt_where_cargos,
        ];
        $aOperador = [
            'id_cargo' => 'IN',
        ];

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $a_sin = [];
        foreach ($this->a_actividades as $id_activ) {
            $aWhere['id_activ'] = $id_activ;
            $cActividadCargo = $ActividadCargoRepository->getActividadCargos($aWhere, $aOperador);
            if (count($cActividadCargo) === 0) {
                $a_sin[] = (int)$id_activ;
            }
        }
        return $a_sin;
    }

    /**
     * Centro encargado principal (num_orden=0) de cada actividad candidata.
     *
     * @return array<int,int> id_activ => id_ubi.
     */
    private function centrosEncargados(): array
    {
        if ($this->a_activ_ctr !== []) {
            return $this->a_activ_ctr;
        }
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $a_ctr = [];
        foreach ($this->a_actividades as $id_activ) {
            $cCentros = $CentroEncargadoRepository->getCentrosEncargados(
                ['id_activ' => $id_activ, 'num_orden' => 0]
            );
            // solo debe haber uno
            if (count($cCentros) === 1) {
                $a_ctr[(int)$id_activ] = (int)$cCentros[0]->getId_ubi();
            }
        }
        $this->a_activ_ctr = $a_ctr;
        return $a_ctr;
    }

    /**
     * Sacd titular de cada centro (modo 2 o 3, sin f_fin) cuyo encargo es
     * de tipo atn ctr sv (1100) o atn ctr sf (1200).
     *
     * @return array<int,int> id_ubi => id_nom.
     */
    private function ctrSacd(): array
    {
        if ($this->a_ctr_sacd !== []) {
            return $this->a_ctr_sacd;
        }
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);

        $a_ctr_sacd = [];
        $cEncargos = $EncargoRepository->getEncargos(
            ['id_tipo_enc' => '^1[12]00'],
            ['id_tipo_enc' => '~']
        );
        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $id_ubi = (int)$oEncargo->getId_ubi();

            $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(
                [
                    'id_enc' => $id_enc,
                    'f_fin' => 'x',
                    'modo' => '2|3',
                ],
                [
                    'f_fin' => 'IS NULL',
                    'modo' => '~',
                ]
            );
            foreach ($cEncargosSacd as $oEncargoSacd) {
                $a_ctr_sacd[$id_ubi] = (int)$oEncargoSacd->getId_nom();
            }
        }
        $this->a_ctr_sacd = $a_ctr_sacd;
        return $a_ctr_sacd;
    }

    /**
     * Intenta asignar el sacd titular del centro encargado a una actividad.
     * Devuelve 1 si se asigno, 0 si no.
     */
    private function asignarUna(int $id_activ): int
    {
        $a_activ_ctr = $this->centrosEncargados();
        if (!isset($a_activ_ctr[$id_activ])) {
            return 0;
        }
        $id_ubi = $a_activ_ctr[$id_activ];

        $a_ctr_sacd = $this->ctrSacd();
        if (empty($a_ctr_sacd[$id_ubi])) {
            return 0;
        }
        $id_nom = $a_ctr_sacd[$id_ubi];

        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        $id_cargo = (int)key($aIdCargos_sacd);

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $oActividadCargo = new ActividadCargo();
        $oActividadCargo->setId_item($ActividadCargoRepository->getNewId());
        $oActividadCargo->setId_activ($id_activ);
        $oActividadCargo->setId_cargo($id_cargo);
        $oActividadCargo->setId_nom($id_nom);
        $oActividadCargo->setObserv('auto');
        if ($ActividadCargoRepository->Guardar($oActividadCargo) === false) {
            return 0;
        }
        return 1;
    }
}
