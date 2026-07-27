<?php

namespace src\notas\application\support;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\TipoActa;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Convierte un array de entrada (`$_POST`) en un objeto `PersonaNota`
 * listo para alimentar los use cases `PersonaNotaNueva`, `PersonaNotaEditar`
 * y `PersonaNotaEliminar`.
 */
final class PersonaNotaInputParser
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
        private readonly ActaPersonaFormListas $actaPersonaFormListas,
    ) {
    }
    /**
     * @param array<string, mixed> $input
     */
    public function parse(array $input, bool $eliminar = false): PersonaNota
    {
        $id_pau = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');

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
            $id_asignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura');
            $id_nivel = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nivel');
            $tipo_acta = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'tipo_acta');
        }

        if ($id_asignatura === 1) {
            $AsignaturaRepository = $this->asignaturaRepository;
            $plan = $this->planEstudiosDePersona->resolve($id_pau);
            [$aWhere, $aOperador] = PlanEstudiosFilter::apply($plan, ['id_nivel' => $id_nivel]);
            $cAsignaturas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);
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

        $id_situacion = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_situacion');
        $acta = $this->resolveActa($input, $tipo_acta);
        $f_acta = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'f_acta');
        $preceptor = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'preceptor');
        $id_preceptor = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_preceptor');
        $detalle = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'detalle');
        $epoca = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'epoca');
        $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $nota_num_raw = $input['nota_num'] ?? null;
        $nota_num = is_numeric($nota_num_raw) ? (float) $nota_num_raw : 0.0;
        $nota_max = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'nota_max');

        if ($epoca === 0) {
            $epoca = NotaEpoca::EPOCA_OTRO;
        }

        $parsedF_acta = $f_acta === '' ? null : DateTimeLocal::createFromLocal($f_acta);
        $oF_acta = $parsedF_acta instanceof DateTimeLocal ? $parsedF_acta : null;

        $oPersonaNota->setIdSituacionVo($id_situacion);
        $oPersonaNota->setActaVo($acta);
        $oPersonaNota->setDetalleVo($detalle);
        $oPersonaNota->setF_acta($oF_acta);
        $oPersonaNota->setPreceptor(\src\shared\domain\helpers\FuncTablasSupport::isTrue($preceptor) ?? false);
        $oPersonaNota->setId_preceptor($id_preceptor);
        $oPersonaNota->setEpocaVo($epoca);
        $oPersonaNota->setIdActivVo($id_activ);
        $oPersonaNota->setNotaNumVo($nota_num);
        $oPersonaNota->setNotaMaxVo($nota_max);

        return $oPersonaNota;
    }

    /**
     * @param array<string, mixed> $input
     */
    private function resolveActa(array $input, int $tipoActa): string
    {
        $acta = trim(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta'));
        $actaNum = trim(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta_num'));
        $actaSigla = trim(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta_sigla'));
        $certDl = trim(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta_cert_dl'));
        $certNum = trim(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta_cert_num'));

        if ($tipoActa === TipoActa::FORMATO_CERTIFICADO) {
            if ($acta !== '') {
                return $acta;
            }
            if ($certDl === '') {
                return $certNum;
            }

            return $certNum !== '' ? $certDl . ' ' . $certNum : $certDl;
        }

        $sigla = $actaSigla !== '' ? $actaSigla : ConfigGlobal::mi_delef();

        if ($acta === '' && $actaNum !== '') {
            if (preg_match('/^\d+\/\d{2}$/', $actaNum) !== 1) {
                throw new \RuntimeException(
                    _('El número de acta debe tener el formato num/aa (ej. 12/26).')
                );
            }
            $acta = $sigla . ' ' . $actaNum;
        }

        if ($acta !== '' && !$this->actaPersonaFormListas->siglaPermitidaEnActa(
            ActaPrefijosDeEsquema::prefijoDeActa($acta)
        )) {
            throw new \RuntimeException(
                _('Para actas de otra delegación o región use el formato certificado.')
            );
        }

        return $acta;
    }
}
