<?php

declare(strict_types=1);

namespace src\notas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\value_objects\NotaEpoca;

/**
 * Busca un acta por sigla + num/aa y devuelve los datos asociados.
 */
final class BuscarActaData
{

    public function __construct(
        private readonly ActaDlRepositoryInterface $actaDlRepository,
        private readonly ActaExRepositoryInterface $actaExRepository,
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
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

        $AsignaturaRepository = $this->asignaturaRepository;
        $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
        if ($oAsignatura === null) {
            throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
        }

        return [
            'id_asignatura' => (string)$id_asignatura,
            'id_nivel' => (string)$oAsignatura->getId_nivel(),
            'id_activ' => (string)$id_activ,
            'f_acta' => (string)$oActa->getF_acta()?->getFromLocal(),
            'nom_activ' => (string)$nom_activ,
            'epoca' => (string)$epoca,
            'acta' => $actaEncontrada,
        ];
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
