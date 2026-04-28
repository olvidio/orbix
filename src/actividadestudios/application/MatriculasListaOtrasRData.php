<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\Persona;

/**
 * @return array{
 *   titulo: string,
 *   titulo_busqueda_por_apellidos: string,
 *   msg_err: string,
 *   a_valores: array<int|string, array<string|int, mixed>>,
 *   a_Nombre?: array<int, string>
 * }
 */
final class MatriculasListaOtrasRData
{
    public static function execute(string $apellido1, string $esquemaRegionStgr): array
    {
        $tituloBusqueda = _('búsqueda por apellidos');
        $titulo = '';
        $msgErr = '';
        $aValores = [];
        $aNombre = [];

        if ($apellido1 !== '') {
            $personaPubRepository = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class);
            $aWhere = [
                'apellido1' => '^' . $apellido1,
                'situacion' => 'A',
                '_ordre' => 'dl,stgr,apellido1,nom',
            ];
            $aOperador = ['apellido1' => 'sin_acentos'];
            $cPersonas = $personaPubRepository->getPersonas($aWhere, $aOperador);
            if ($cPersonas === false) {
                $cPersonas = [];
            }
            $i = 0;
            foreach ($cPersonas as $oPersona) {
                $idNom = $oPersona->getId_nom();
                $dl = $oPersona->getDl();
                $apellidosNombre = $oPersona->getPrefApellidosNombre();
                $i++;
                $aValores[$i]['sel'] = (string)$idNom;
                $aValores[$i][5] = $idNom;
                $aValores[$i][1] = $apellidosNombre;
                $aValores[$i][2] = $dl;
                $aValores[$i][3] = '';
                $aValores[$i][4] = '';
                $aNombre[$i] = $apellidosNombre;
            }
        } else {
            $aWhere = ['json_certificados' => 'x', '_ordre' => 'id_nom'];
            $aOperador = ['json_certificados' => 'IS NULL'];
            $personaNotaOtraRepo = $GLOBALS['container']->make(
                PersonaNotaOtraRegionStgrRepositoryInterface::class,
                ['esquema_region_stgr' => $esquemaRegionStgr],
            );
            $aNotasOtrasRegiones = $personaNotaOtraRepo->getPersonaNotas($aWhere, $aOperador);

            $asignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
            $aAsignaturas = $asignaturaRepository->getArrayAsignaturas();

            $titulo = _('Lista de alumnos de otras regiones pendientes de generar certificado');
            $i = 0;
            $msgErr = '';
            $strAsignaturas = '';
            $idNomAnterior = '';
            $alert = '';
            $actividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
            $actaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
            $idNom = '';
            foreach ($aNotasOtrasRegiones as $oPersonaNotaOtraRegionDB) {
                $i++;
                $idNom = $oPersonaNotaOtraRegionDB->getId_nom();

                if ($idNomAnterior !== '' && $idNom !== $idNomAnterior) {
                    $oPersona = Persona::findPersonaEnGlobal($idNomAnterior);
                    if ($oPersona === null) {
                        $msgErr .= "<br>No encuentro a nadio con id_nom $idNomAnterior en  " . __FILE__ . ': line ' . __LINE__;
                        $idNomAnterior = $idNom;
                        continue;
                    }
                    $apellidosNombre = $oPersona->getPrefApellidosNombre();
                    $dl = $oPersona->getDl();

                    $aValores[$i]['sel'] = (string)$idNomAnterior;
                    $aValores[$i][5] = $idNomAnterior;
                    $aValores[$i][1] = $apellidosNombre;
                    $aValores[$i][2] = $dl;
                    $aValores[$i][3] = $alert;
                    $aValores[$i][4] = $strAsignaturas;
                    $aNombre[$i] = $apellidosNombre;
                    $strAsignaturas = '';
                    $alert = '';
                }
                $idAsignatura = $oPersonaNotaOtraRegionDB->getId_asignatura();
                $idActiv = $oPersonaNotaOtraRegionDB->getId_activ();
                $acta = $oPersonaNotaOtraRegionDB->getActa();
        $Acta = $actaRepository->findById($acta);
        if ($Acta !== null && $Acta->hasEmptyPdf()) {
                    $alert .= '!';
                }
                $nomAsignatura = $aAsignaturas[$idAsignatura];
                $oActividad = $actividadAllRepository->findById($idActiv);
                $nomActiv = $oActividad->getNom_activ();

                $strAsignaturas .= $strAsignaturas === '' ? '' : ', ';
                $strAsignaturas .= trim((string)$nomAsignatura);
                $strAsignaturas .= $nomActiv === '' ? '' : "($nomActiv)";

                $idNomAnterior = $idNom;
            }
            if ($idNom !== '') {
                $oPersona = Persona::findPersonaEnGlobal($idNom);
                if ($oPersona === null) {
                    $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom en  " . __FILE__ . ': line ' . __LINE__;
                } else {
                    $apellidosNombre = $oPersona->getPrefApellidosNombre();
                    $dl = $oPersona->getDl();
                    $aValores[$i + 1]['sel'] = (string)$idNom;
                    $aValores[$i + 1][5] = $idNom;
                    $aValores[$i + 1][1] = $apellidosNombre;
                    $aValores[$i + 1][2] = $dl;
                    $aValores[$i + 1][3] = $alert;
                    $aValores[$i + 1][4] = $strAsignaturas;
                    $aNombre[$i + 1] = $apellidosNombre;
                }
            }
        }

        if (!empty($aValores) && !empty($aNombre)) {
            array_multisort($aNombre, SORT_STRING, $aValores);
        }

        return [
            'titulo' => $titulo,
            'titulo_busqueda_por_apellidos' => $tituloBusqueda,
            'msg_err' => $msgErr,
            'a_valores' => $aValores,
        ];
    }
}
