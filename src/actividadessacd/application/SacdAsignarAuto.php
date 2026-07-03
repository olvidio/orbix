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
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Auto-asigna el sacd titular del centro encargado a actividades sr/sg sin sacd.
 */
final class SacdAsignarAuto
{
    /** @var array<string, int> */
    private array $a_actividades = [];

    /** @var array<int, int> */
    private array $a_activ_ctr = [];

    /** @var array<int, int> */
    private array $a_ctr_sacd = [];

    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private CargoRepositoryInterface $cargoRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private EncargoRepositoryInterface $encargoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{asignadas: int, sin_asignar: int}
     */
    public function execute(array $input): array
    {
        $f_ini_iso = FuncTablasSupport::inputString($input, 'f_ini_iso');
        if ($f_ini_iso === '') {
            return ['asignadas' => 0, 'sin_asignar' => 0];
        }

        $this->a_actividades = [];
        $this->a_activ_ctr = [];
        $this->a_ctr_sacd = [];

        return $this->run($f_ini_iso);
    }

    /**
     * @return array{asignadas: int, sin_asignar: int}
     */
    private function run(string $f_ini_iso): array
    {
        $this->seleccionarActividades($f_ini_iso);
        $a_sin_sacd = $this->actividadesSinSacd();

        $asig = 0;
        foreach ($a_sin_sacd as $id_activ) {
            $asig += $this->asignarUna($id_activ);
        }
        $sin_asig = count($a_sin_sacd) - $asig;

        return ['asignadas' => $asig, 'sin_asignar' => $sin_asig];
    }

    private function seleccionarActividades(string $f_ini_iso): void
    {
        $aWhere = [
            'id_tipo_activ' => '.(4|5|7)',
            'f_ini' => $f_ini_iso,
            'status' => StatusId::ACTUAL,
        ];
        $aOperador = [
            'id_tipo_activ' => '~',
            'f_ini' => '>',
        ];

        $this->a_actividades = $this->actividadDlRepository->getArrayIdsWithKeyFini($aWhere, $aOperador);
    }

    /**
     * @return list<int>
     */
    private function actividadesSinSacd(): array
    {
        $aIdCargos_sacd = $this->cargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $aWhere = [
            'id_cargo' => $txt_where_cargos,
        ];
        $aOperador = [
            'id_cargo' => 'IN',
        ];

        $a_sin = [];
        foreach ($this->a_actividades as $_fini => $id_activ) {
            $aWhere['id_activ'] = (int)$id_activ;
            $cActividadCargo = $this->actividadCargoRepository->getActividadCargos($aWhere, $aOperador);
            if (count($cActividadCargo) === 0) {
                $a_sin[] = (int)$id_activ;
            }
        }
        return $a_sin;
    }

    /**
     * @return array<int, int>
     */
    private function centrosEncargados(): array
    {
        if ($this->a_activ_ctr !== []) {
            return $this->a_activ_ctr;
        }
        $a_ctr = [];
        foreach ($this->a_actividades as $_fini => $id_activ) {
            $cCentros = $this->centroEncargadoRepository->getCentrosEncargados(
                ['id_activ' => (int)$id_activ, 'num_orden' => 0]
            );
            if (count($cCentros) === 1) {
                $a_ctr[(int)$id_activ] = (int)$cCentros[0]->getId_ubi();
            }
        }
        $this->a_activ_ctr = $a_ctr;
        return $a_ctr;
    }

    /**
     * @return array<int, int>
     */
    private function ctrSacd(): array
    {
        if ($this->a_ctr_sacd !== []) {
            return $this->a_ctr_sacd;
        }

        $a_ctr_sacd = [];
        $cEncargos = $this->encargoRepository->getEncargos(
            ['id_tipo_enc' => '^1[12]00'],
            ['id_tipo_enc' => '~']
        );
        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $id_ubi = (int)$oEncargo->getId_ubi();

            $cEncargosSacd = $this->encargoSacdRepository->getEncargosSacd(
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

        $aIdCargos_sacd = $this->cargoRepository->getArrayCargos('sacd');
        $id_cargo = (int)key($aIdCargos_sacd);

        $oActividadCargo = new ActividadCargo();
        $oActividadCargo->setId_item($this->actividadCargoRepository->getNewId());
        $oActividadCargo->setId_activ($id_activ);
        $oActividadCargo->setId_cargo($id_cargo);
        $oActividadCargo->setId_nom($id_nom);
        $oActividadCargo->setObserv('auto');
        if ($this->actividadCargoRepository->Guardar($oActividadCargo) === false) {
            return 0;
        }
        return 1;
    }
}
