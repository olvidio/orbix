<?php

namespace src\notas\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\entity\Persona;
use function core\is_true;

/**
 * Agrega los datos de la tabla "select1011" (listado de notas de una
 * persona dentro del dossier 1011). Se usa desde
 * `src\notas\application\Select1011` que se ocupa solo del pintado con
 * `web\Lista` + `ViewPhtml`.
 */
final class Select1011Data
{
    public static function getTabla(int $id_pau, int $permiso): array
    {
        $msg = self::getMensajeMatriculasPendientes($id_pau);

        $aValores = self::getFilas($id_pau, $permiso);

        return [
            'aValores' => $aValores,
            'aviso' => $msg,
        ];
    }

    private static function getMensajeMatriculasPendientes(int $id_pau): string
    {
        $matriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        $cMatriculasPendientes = $matriculaRepository->getMatriculasPendientes($id_pau);
        if (count($cMatriculasPendientes) === 0) {
            return '';
        }
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
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

    private static function getFilas(int $id_pau, int $permiso): array
    {
        $PersonaNotaRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $cPersonaNotas = $PersonaNotaRepository->getPersonaNotas(
            ['id_nom' => $id_pau, '_ordre' => 'id_nivel'],
            ['id_asignatura' => '<']
        );

        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);

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
