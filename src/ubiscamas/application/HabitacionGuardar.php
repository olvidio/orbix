<?php

declare(strict_types=1);

namespace src\ubiscamas\application;

use Ramsey\Uuid\Uuid;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\HabitacionNombre;
use src\ubiscamas\domain\value_objects\HabitacionOrden;
use src\ubiscamas\domain\value_objects\NumeroCamas;
use src\ubiscamas\domain\value_objects\PlantaText;
use src\ubiscamas\domain\value_objects\TipoLavabo;

/**
 * Alta o modificación de una habitación de una casa y de sus camas: las camas
 * nuevas que se describen en el formulario y, si faltan hasta el número de
 * camas, las que se generan solas (marcando VIP hasta completar las pedidas).
 */
final class HabitacionGuardar
{
    public function __construct(
        private readonly HabitacionDlRepositoryInterface $habitacionRepository,
        private readonly CamaDlRepositoryInterface $camaRepository,
    ) {
    }

    /**
     * @param array{
     *     id_habitacion: string, id_ubi: int, orden: int, nombre: string, numero_camas: int,
     *     numero_camas_vip: int, planta: string, tipoLavabo: int, sillon: bool, adaptada: bool,
     *     observaciones: string, despacho: bool, new_camas_desc: list<string>,
     *     new_camas_larga: array<int, string>, new_camas_vip: array<int, string>
     * } $in
     * @return string texto de error, vacío si se ha guardado
     */
    public function execute(array $in): string
    {
        try {
            [$idHabitacion, $oHabitacion] = $this->habitacion($in['id_habitacion'], $in['id_ubi']);
            $oHabitacion->setOrdenVo(new HabitacionOrden($in['orden']));
            $oHabitacion->setNombreVo(HabitacionNombre::fromNullableString($in['nombre']));
            $oHabitacion->setNumeroCamasVo(NumeroCamas::fromNullableInt($in['numero_camas']));
            $oHabitacion->setNumeroCamasVipVo(NumeroCamas::fromNullableInt($in['numero_camas_vip']));
            $oHabitacion->setPlantaVo(PlantaText::fromNullableString($in['planta']));
            $oHabitacion->settipoLavaboVo(TipoLavabo::fromNullableInt($in['tipoLavabo']));
            $oHabitacion->setSillon($in['sillon']);
            $oHabitacion->setAdaptada($in['adaptada']);
            $oHabitacion->setObservacionesVo($in['observaciones']);
            $oHabitacion->setDespacho($in['despacho']);
            if ($this->habitacionRepository->Guardar($oHabitacion) === false) {
                return _("Error al guardar la habitación") . ': ' . $this->habitacionRepository->getErrorTxt();
            }

            $camas = $this->camaRepository->getCamasByHabitacion($idHabitacion);
            $numCamas = count($camas);
            $numVip = count(array_filter($camas, static fn (Cama $c): bool => (bool) $c->isVip()));

            foreach ($in['new_camas_desc'] as $index => $descripcion) {
                if ($descripcion === '') {
                    continue;
                }
                $vip = ($in['new_camas_vip'][$index] ?? '') !== '';
                $this->nuevaCama($idHabitacion, $descripcion, ($in['new_camas_larga'][$index] ?? '') !== '', $vip);
                $numCamas++;
                $numVip += $vip ? 1 : 0;
            }

            $aCrear = $in['numero_camas'] - $numCamas;
            for ($i = 1; $i <= $aCrear; $i++) {
                $desc = $in['nombre'];
                if ($aCrear > 1) {
                    $desc .= ($desc === '' ? '' : ' - ') . 'Cama ' . ($numCamas + $i);
                }
                // VIP hasta completar las pedidas, contando las que ya había.
                $vip = $numVip < $in['numero_camas_vip'];
                $this->nuevaCama($idHabitacion, $desc, false, $vip);
                $numVip += $vip ? 1 : 0;
            }
        } catch (\Exception $e) {
            return _("Error al guardar la habitación") . ': ' . $e->getMessage();
        }

        return '';
    }

    /** @return array{0: HabitacionId, 1: Habitacion} */
    private function habitacion(string $id, int $idUbi): array
    {
        $oHabitacion = null;
        if ($id === '') {
            $idHabitacion = new HabitacionId(Uuid::uuid4()->toString());
        } else {
            $idHabitacion = HabitacionId::fromNullableString($id) ?? throw new \Exception(_("Habitación no válida"));
            $oHabitacion = $this->habitacionRepository->findById($idHabitacion->value());
        }
        if ($oHabitacion === null) {
            $oHabitacion = new Habitacion();
            $oHabitacion->setIdHabitacionVo($idHabitacion);
            $oHabitacion->setIdUbiVo($idUbi);
        }
        return [$idHabitacion, $oHabitacion];
    }

    private function nuevaCama(HabitacionId $idHabitacion, string $descripcion, bool $larga, bool $vip): void
    {
        $oCama = new Cama();
        $oCama->setIdCamaVo(new CamaId(Uuid::uuid4()->toString()));
        $oCama->setIdHabitacionVo($idHabitacion);
        $oCama->setDescripcionVo(CamaDescripcion::fromNullableString($descripcion) ?? new CamaDescripcion(''));
        $oCama->setLarga($larga);
        $oCama->setVip($vip);
        if ($this->camaRepository->Guardar($oCama) === false) {
            throw new \Exception($this->camaRepository->getErrorTxt());
        }
    }
}
