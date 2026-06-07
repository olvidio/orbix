<?php

namespace src\notas\application;


use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\is_true;

/**
 * Agrega los datos de la tabla "select_notas_de_una_persona" (listado de
 * notas de una persona dentro del dossier 1011). Se usa desde
 * `src\notas\application\Select_notas_de_una_persona` que se ocupa solo
 * del pintado con `frontend\shared\web\Lista` + `ViewNewPhtml`.
 */
final class NotasDeUnaPersonaData
{

    public function __construct(
        private readonly MatriculaRepositoryInterface $matriculaRepository,
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }
    /**
     * @return array{aValores: array<int, array<int|string, mixed>>, aviso: string}
     */
    public function getTabla(int $id_pau, int $permiso): array
    {
        $msg = $this->getMensajeMatriculasPendientes($id_pau);

        $aValores = $this->getFilas($id_pau, $permiso);

        return [
            'aValores' => $aValores,
            'aviso' => $msg,
        ];
    }

    private function getMensajeMatriculasPendientes(int $id_pau): string
    {
        $matriculaRepository = $this->matriculaRepository;
        $cMatriculasPendientes = $matriculaRepository->getMatriculasPendientes($id_pau);
        if (count($cMatriculasPendientes) === 0) {
            return '';
        }
        $ActividadAllRepository = $this->actividadAllRepository;
        $AsignaturaRepository = $this->asignaturaRepository;
        $msg = '';
        foreach ($cMatriculasPendientes as $oMatricula) {
            $oActividad = $ActividadAllRepository->findById($oMatricula->getId_activ());
            $oAsignatura = $AsignaturaRepository->findById($oMatricula->getId_asignatura());
            if ($oAsignatura === null) {
                continue;
            }
            $msg .= empty($msg) ? '' : '<br>';
            $msg .= sprintf(
                _("ca: %s, asignatura: %s"),
                $oActividad?->getNom_activ() ?? '',
                $oAsignatura->getNombre_corto()
            );
        }
        return _("tiene pendiente de poner las notas de:") . '<br>' . $msg;
    }

    /**
     * @return array<int, array<int|string, mixed>>
     */
    private function getFilas(int $id_pau, int $permiso): array
    {
        $PersonaNotaRepository = $this->personaNotaRepository;
        $cPersonaNotas = $PersonaNotaRepository->getPersonaNotas(
            ['id_nom' => $id_pau, '_ordre' => 'id_nivel'],
            ['id_asignatura' => '<']
        );

        $AsignaturaRepository = $this->asignaturaRepository;
        $ActividadAllRepository = $this->actividadAllRepository;

        $i = 0;
        $a_valores = [];
        foreach ($cPersonaNotas as $oPersonaNota) {
            $i++;
            $id_nivel = $oPersonaNota->getIdNivelVo()->value();
            $id_asignatura = $oPersonaNota->getId_asignatura();
            $acta = $oPersonaNota->getActa();
            if ($acta == NotaSituacion::CURSADA) {
                $acta = '';
            }

            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();

            if ($id_asignatura > 3000) {
                $cOpcionales = $AsignaturaRepository->getAsignaturas(['id_nivel' => $id_nivel]);
                if (empty($cOpcionales)) {
                    $nombre_corto = _("opcional de sobra");
                } else {
                    $nom_op = $cOpcionales[0]->getNombre_corto();
                    $nombre_corto = $nom_op . ' (' . $nombre_corto . ')';
                }
            }

            $nom_activ = '';
            $id_activ = $oPersonaNota->getId_activ();
            if (!empty($id_activ)) {
                $oActividad = $ActividadAllRepository->findById($id_activ);
                $nom_activ = $oActividad?->getNom_activ() ?? '';
            }

            $preceptorText = is_true($oPersonaNota->isPreceptor()) ? _("sí") : _("no");
            $id_preceptor = $oPersonaNota->getId_preceptor();
            if ($id_preceptor && is_true($oPersonaNota->isPreceptor())) {
                $oPersona = Persona::findPersonaEnGlobal($id_preceptor);
                $nom = $oPersona?->getPrefApellidosNombre() ?? _("no lo encuentro");
                $preceptorText .= ' (' . $nom . ')';
            }

            $tipo_acta = $oPersonaNota->getTipo_acta();
            $a_valores[$i]['sel'] = $permiso == 3 ? "$id_nivel#$id_asignatura#$tipo_acta" : '';
            $a_valores[$i][1] = $nombre_corto;
            $a_valores[$i][2] = $oPersonaNota->getNota_txt();
            $a_valores[$i][3] = $acta;
            $a_valores[$i][4] = $oPersonaNota->getF_acta()?->getFromLocal();
            $a_valores[$i][5] = $preceptorText;
            $a_valores[$i][6] = $oPersonaNota->getEpoca();
            $a_valores[$i][7] = $oPersonaNota->getDetalle();
            $a_valores[$i][8] = $nom_activ;
        }

        return $a_valores;
    }
}
