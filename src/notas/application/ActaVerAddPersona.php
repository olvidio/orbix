<?php

declare(strict_types=1);

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\NivelId;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\support\LiberarHuecoNivelNota;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Añade un alumno a un acta guardando la nota directamente en el dossier (`e_notas_dl`).
 */
final class ActaVerAddPersona
{
    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly DossierRepositoryInterface $dossierRepository,
        private readonly PersonaNotaDlRepositoryInterface $personaNotaDlRepository,
        private readonly MapaPrefijoActaEsquemaRepositoryInterface $mapaPrefijoActaEsquemaRepository,
        private readonly LiberarHuecoNivelNota $liberarHuecoNivelNota,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, mensaje: string}
     */
    public function execute(array $input): array
    {
        $acta = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta');
        $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        $nota_num_raw = $input['nota_num'] ?? null;
        $nota_num = is_numeric($nota_num_raw) ? (float) $nota_num_raw : null;
        $nota_max = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'nota_max');

        if ($acta === '' || $id_nom <= 0) {
            return ['success' => false, 'mensaje' => _('Faltan acta o persona')];
        }

        $oActa = $this->actaRepository->findById($acta);
        if (!$oActa instanceof Acta) {
            return ['success' => false, 'mensaje' => _('No se encuentra el acta')];
        }
        if ($oActa->tienePdfFirmado()) {
            return ['success' => false, 'mensaje' => _('El acta está firmada y no se puede modificar')];
        }

        $id_asignatura = (int) ($oActa->getId_asignatura() ?? 0);
        $id_activ = (int) ($oActa->getId_activ() ?? 0);
        $f_acta = $oActa->getF_acta();

        if ($id_asignatura < 1) {
            return ['success' => false, 'mensaje' => _('El acta no tiene asignatura')];
        }

        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        if ($nota_max < 1) {
            $nota_max = (int) $oConfig->getNotaMax();
        }
        $nota_corte = (float) $oConfig->getNotaCorte();

        $id_nivel = 0;
        $cAsignatura = $this->asignaturaRepository->getAsignaturas(['id_asignatura' => $id_asignatura]);
        if ($cAsignatura !== []) {
            $id_nivel = (int) ($cAsignatura[0]->getId_nivel() ?? 0);
        }

        $id_situacion = NotaSituacion::NUMERICA;
        if ($nota_num !== null && $nota_max > 0 && ($nota_num / $nota_max) < $nota_corte) {
            $id_situacion = NotaSituacion::EXAMINADO;
        }

        $existentes = $this->personaNotaRepository->getPersonaNotas([
            'id_nom' => $id_nom,
            'id_asignatura' => $id_asignatura,
            'acta' => $acta,
        ]);

        $oPersonaNota = new PersonaNota();
        $oPersonaNota->setIdNivelVo(NivelId::fromNullableInt($id_nivel));
        $oPersonaNota->setIdAsignaturaVo($id_asignatura);
        $oPersonaNota->setId_nom($id_nom);
        $oPersonaNota->setIdSituacionVo($id_situacion);
        $oPersonaNota->setActaVo($acta);
        $oPersonaNota->setF_acta($f_acta);
        $oPersonaNota->setDetalleVo('');
        $oPersonaNota->setTipoActaVo(TipoActa::FORMATO_ACTA);
        $oPersonaNota->setPreceptor(false);
        $oPersonaNota->setId_preceptor(0);
        $oPersonaNota->setEpocaVo(NotaEpoca::EPOCA_OTRO);
        $oPersonaNota->setId_activ($id_activ);
        $oPersonaNota->setNotaNumVo($nota_num);
        $oPersonaNota->setNotaMaxVo($nota_max > 0 ? $nota_max : null);

        $oEditar = new EditarPersonaNota(
            $oPersonaNota,
            $this->personaNotaRepository,
            $this->delegacionRepository,
            $this->dbSchemaRepository,
            $this->dossierRepository,
            $this->personaNotaDlRepository,
            $this->mapaPrefijoActaEsquemaRepository,
            $this->liberarHuecoNivelNota,
        );

        try {
            if ($existentes !== []) {
                $oEditar->editar($id_asignatura);
            } else {
                $oEditar->nuevo();
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'mensaje' => $e->getMessage()];
        }

        return [
            'success' => true,
            'mensaje' => sprintf(_("Nota guardada en el esquema %s"), $oEditar->getEsquemaEscritura()),
        ];
    }
}
