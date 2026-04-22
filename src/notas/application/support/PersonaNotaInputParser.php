<?php

namespace src\notas\application\support;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\TipoActa;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use function core\is_true;

/**
 * Convierte un array de entrada (`$_POST`) en un objeto `PersonaNota`
 * listo para alimentar los use cases `PersonaNotaNueva`, `PersonaNotaEditar`
 * y `PersonaNotaEliminar`.
 *
 * Encapsula la logica dispersa en `apps/notas/controller/update_1011.php`
 * (inputs via checkbox con formato `id_nivel#id_asignatura#tipo_acta`,
 * normalizacion de `tipo_acta` y `epoca`, resolucion de asignatura si
 * `id_asignatura === 1`, etc.).
 */
final class PersonaNotaInputParser
{
    public static function parse(array $input, bool $eliminar = false): PersonaNota
    {
        $id_pau = (int)($input['id_pau'] ?? 0);

        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $id_nivel = (int)strtok((string)$a_sel[0], '#');
            $id_asignatura = (int)strtok('#');
            $tipo_acta = (int)strtok('#');
        } else {
            $id_asignatura = (int)($input['id_asignatura'] ?? 0);
            $id_nivel = (int)($input['id_nivel'] ?? 0);
            $tipo_acta = (int)($input['tipo_acta'] ?? 0);
        }

        if ($id_asignatura === 1) {
            $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
            $cAsignaturas = $AsignaturaRepository->getAsignaturas(['id_nivel' => $id_nivel]);
            if (!is_array($cAsignaturas) || count($cAsignaturas) === 0) {
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

        $id_situacion = (int)($input['id_situacion'] ?? 0);
        $acta = (string)($input['acta'] ?? '');
        $f_acta = (string)($input['f_acta'] ?? '');
        $preceptor = (string)($input['preceptor'] ?? '');
        $id_preceptor = (int)($input['id_preceptor'] ?? 0);
        $detalle = (string)($input['detalle'] ?? '');
        $epoca = (int)($input['epoca'] ?? 0);
        $id_activ = (int)($input['id_activ'] ?? 0);
        $nota_num = isset($input['nota_num']) ? (float)$input['nota_num'] : 0.0;
        $nota_max = (int)($input['nota_max'] ?? 0);

        if ($epoca === 0) {
            $epoca = NotaEpoca::EPOCA_OTRO;
        }

        $oF_acta = empty($f_acta) ? new NullDateTimeLocal() : DateTimeLocal::createFromLocal($f_acta);

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
