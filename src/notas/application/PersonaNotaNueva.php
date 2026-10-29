<?php

namespace src\notas\application;

use src\notas\domain\criteria\PersonaNotaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\application\support\HuecoOpcionalDePlan;
use src\notas\application\support\LiberarHuecoNivelNota;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Crea una nueva `PersonaNota`. Thin wrapper sobre `EditarPersonaNota::nuevo()`.
 *
 * @return array{error: string, mensaje: string, esquema: string}
 */
final class PersonaNotaNueva
{
    public function __construct(
        private readonly PersonaNotaInputParser $personaNotaInputParser,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly DossierRepositoryInterface $dossierRepository,
        private readonly PersonaNotaDlRepositoryInterface $personaNotaDlRepository,
        private readonly MapaPrefijoActaEsquemaRepositoryInterface $mapaPrefijoActaEsquemaRepository,
        private readonly LiberarHuecoNivelNota $liberarHuecoNivelNota,
        private readonly ActaFirmadaPolicy $firmadaPolicy,
        private readonly HuecoOpcionalDePlan $huecoOpcionalDePlan,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string, mensaje: string, esquema: string}
     */
    public function execute(array $input): array
    {
        try {
            $oPersonaNota = $this->personaNotaInputParser->parse($input);
            $mod = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'mod');
            if ($mod !== 'editar') {
                $this->reubicarOpcionalDeSobra($oPersonaNota);
            }
            $firmada = $this->firmadaPolicy->mensajeSiFirmada($oPersonaNota->getActa());
            if ($firmada !== '') {
                return ['error' => $firmada, 'mensaje' => '', 'esquema' => ''];
            }
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
            $rta = $oEditar->nuevo();
            $esquema = (string) ($rta['esquema'] ?? $oEditar->getEsquemaEscritura());

            return [
                'error' => '',
                'mensaje' => sprintf(_("Nota guardada en el esquema %s"), $esquema),
                'esquema' => $esquema,
            ];
        } catch (\RuntimeException $e) {
            return [
                'error' => $e->getMessage(),
                'mensaje' => '',
                'esquema' => '',
            ];
        }
    }

    private function reubicarOpcionalDeSobra(\src\notas\domain\entity\PersonaNota $oPersonaNota): void
    {
        $idAsignatura = (int) $oPersonaNota->getId_asignatura();
        if (!HuecoOpcionalDePlan::esOpcionalConcreta($idAsignatura)) {
            return;
        }

        $idNom = (int) $oPersonaNota->getId_nom();
        $notas = [];
        foreach ($this->personaNotaRepository->matching(Criteria::create()->where(Filter::eq(F::IdNom, $idNom))) as $nota) {
            $notas[] = [
                'id_asignatura' => (int) $nota->getId_asignatura(),
                'id_nivel' => (int) $nota->getIdNivelVo()->value(),
            ];
        }

        $nivel = $this->huecoOpcionalDePlan->nivelAlGuardar(
            $idNom,
            $idAsignatura,
            (int) $oPersonaNota->getId_nivel(),
            $notas,
        );
        if ($nivel === null) {
            throw new \RuntimeException(_("No hay hueco de opcional libre."));
        }
        $oPersonaNota->setIdNivelVo(NivelId::fromNullableInt($nivel));
    }
}
