<?php

declare(strict_types=1);

namespace src\notas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\support\NivelCatalogoAsignaturaEnPlan;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\value_objects\NotaEpoca;

/**
 * Busca un acta por sigla + num/aa y devuelve los datos asociados.
 *
 * El acta identifica la asignatura por `id_asignatura`. El `id_nivel` del
 * desplegable (hueco curricular) se resuelve con el plan del alumno:
 * el mismo id puede ocupar slots distintos en 1997 y 2026 (p. ej. Latín III
 * 2211 → 2212 en 1997 y 2112 en 2026; 2212 en 2026 es Latín IV).
 */
final class BuscarActaData
{

    public function __construct(
        private readonly ActaDlRepositoryInterface $actaDlRepository,
        private readonly ActaExRepositoryInterface $actaExRepository,
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly NivelCatalogoAsignaturaEnPlan $nivelCatalogoAsignaturaEnPlan,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $sigla = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta_sigla');
        if ($sigla === '') {
            $sigla = ConfigGlobal::mi_delef();
        }

        $numPart = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta');
        $actaBuscar = $this->componerActa($sigla, $numPart);
        if ($actaBuscar === '') {
            return ['id_asignatura' => 'no'];
        }

        $cActas = $this->actaDlRepository->getActas(['acta' => $actaBuscar]);
        if (count($cActas) !== 1) {
            $cActas = $this->actaExRepository->getActas(['acta' => $actaBuscar]);
        }

        if (count($cActas) !== 1) {
            return ['id_asignatura' => 'no'];
        }

        $oActa = $cActas[0];
        $id_asignatura = $oActa->getId_asignatura();
        if ($id_asignatura === null) {
            return ['id_asignatura' => 'no'];
        }
        $id_activ = $oActa->getId_activ();
        $actaEncontrada = (string) $oActa->getActa();

        if (!empty($id_activ)) {
            $ActividadAllRepository = $this->actividadAllRepository;
            $oActividad = $ActividadAllRepository->findById($id_activ);
            $nom_activ = $oActividad?->getNom_activ() ?? '';
            $id_tipo_actividad = $oActividad?->getId_tipo_activ();
            $epoca = $id_tipo_actividad === 132500 ? NotaEpoca::EPOCA_INVIERNO : NotaEpoca::EPOCA_CA;
        } else {
            $nom_activ = '';
            $epoca = NotaEpoca::EPOCA_OTRO;
        }

        $idAsignatura = (int) $id_asignatura;
        $idPau = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');

        return [
            'id_asignatura' => (string) $idAsignatura,
            'id_nivel' => (string) $this->resolveIdNivel($idPau, $idAsignatura),
            'id_activ' => (string)$id_activ,
            'f_acta' => (string)$oActa->getF_acta()?->getFromLocal(),
            'nom_activ' => (string)$nom_activ,
            'epoca' => (string)$epoca,
            'acta' => $actaEncontrada,
        ];
    }

    /**
     * Hueco curricular de la asignatura del acta en el plan del alumno.
     * Sin `id_pau` no hay plan: se toma una fila cualquiera del catálogo
     * (mismo `LIMIT 1` que antes; no usar en el formulario de nota nueva).
     */
    private function resolveIdNivel(int $idPau, int $idAsignatura): int
    {
        if ($idPau > 0 && $this->nivelCatalogoAsignaturaEnPlan->esObligatoria($idAsignatura)) {
            return $this->nivelCatalogoAsignaturaEnPlan->resolve($idPau, $idAsignatura);
        }

        $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
        if ($oAsignatura === null) {
            throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $idAsignatura));
        }

        return $oAsignatura->getId_nivel();
    }

    private function componerActa(string $sigla, string $numPart): string
    {
        $numPart = trim($numPart);
        if ($numPart === '') {
            return '';
        }

        $matches = [];
        preg_match('/^(\d+)(?:\/(\d{2}))?$/', $numPart, $matches);
        if ($matches === []) {
            return '';
        }

        $soloNumero = ($matches[2] ?? '') === '';
        $any = $soloNumero ? date('y') : $matches[2];
        $num = $matches[1] . '/' . $any;

        return $sigla . ' ' . $num;
    }
}
