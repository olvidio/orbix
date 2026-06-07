<?php

namespace src\notas\application\support;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;


use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\TipoActa;
use src\shared\domain\value_objects\DateTimeLocal;
use function src\shared\domain\helpers\is_true;

/**
 * Convierte un array de entrada (`$_POST`) en un objeto `PersonaNota`
 * listo para alimentar los use cases `PersonaNotaNueva`, `PersonaNotaEditar`
 * y `PersonaNotaEliminar`.
 *
 * Encapsula la logica historica del antiguo `update_1011.php`: inputs
 * via checkbox con formato `id_nivel#id_asignatura#tipo_acta`,
 * normalizacion de `tipo_acta` y `epoca`, resolucion de asignatura si
 * `id_asignatura === 1`, etc.
 */
final class PersonaNotaInputParser
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }
    /**
     * @param array<string, mixed> $input
     */
    public function parse(array $input, bool $eliminar = false): PersonaNota
    {
        $id_pau = input_int($input, 'id_pau');

        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $sel0 = $a_sel[0] ?? null;
            if (!is_scalar($sel0)) {
                throw new \RuntimeException(_('Selección de nota no válida.'));
            }
            $id_nivel = (int)strtok((string) $sel0, '#');
            $id_asignatura = (int)strtok('#');
            $tipo_acta = (int)strtok('#');
        } else {
            $id_asignatura = input_int($input, 'id_asignatura');
            $id_nivel = input_int($input, 'id_nivel');
            $tipo_acta = input_int($input, 'tipo_acta');
        }

        if ($id_asignatura === 1) {
            $AsignaturaRepository = $this->asignaturaRepository;
            $cAsignaturas = $AsignaturaRepository->getAsignaturas(['id_nivel' => $id_nivel]);
            if (count($cAsignaturas) === 0) {
                throw new \RuntimeException(sprintf(_("No se encuentra una asignatura para el nivel: %s"), $id_nivel));
            }
            $id_asignatura = $cAsignaturas[0]->getId_asignatura();
        }

        if ($tipo_acta === 0) {
            $tipo_acta = TipoActa::FORMATO_ACTA;
        }

        $oPersonaNota = new PersonaNota();
        $oPersonaNota->setIdNivelVo(NivelId::fromNullableInt($id_nivel));
        $oPersonaNota->setIdAsignaturaVo($id_asignatura);
        $oPersonaNota->setId_nom($id_pau);
        $oPersonaNota->setTipoActaVo($tipo_acta);

        if ($eliminar) {
            return $oPersonaNota;
        }

        $id_situacion = input_int($input, 'id_situacion');
        $acta = input_string($input, 'acta');
        $f_acta = input_string($input, 'f_acta');
        $preceptor = input_string($input, 'preceptor');
        $id_preceptor = input_int($input, 'id_preceptor');
        $detalle = input_string($input, 'detalle');
        $epoca = input_int($input, 'epoca');
        $id_activ = input_int($input, 'id_activ');
        $nota_num_raw = $input['nota_num'] ?? null;
        $nota_num = is_numeric($nota_num_raw) ? (float) $nota_num_raw : 0.0;
        $nota_max = input_int($input, 'nota_max');

        if ($epoca === 0) {
            $epoca = NotaEpoca::EPOCA_OTRO;
        }

        $oF_acta = empty($f_acta) ? null : DateTimeLocal::createFromLocal($f_acta);

        $oPersonaNota->setIdSituacionVo($id_situacion);
        $oPersonaNota->setActaVo($acta);
        $oPersonaNota->setDetalleVo($detalle);
        $oPersonaNota->setF_acta($oF_acta);
        $oPersonaNota->setPreceptor(is_true($preceptor));
        $oPersonaNota->setId_preceptor($id_preceptor);
        $oPersonaNota->setEpocaVo($epoca);
        $oPersonaNota->setIdActivVo($id_activ);
        $oPersonaNota->setNotaNumVo($nota_num);
        $oPersonaNota->setNotaMaxVo($nota_max);

        return $oPersonaNota;
    }
}
