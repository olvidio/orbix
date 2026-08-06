<?php

namespace src\profesores\domain;

use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\entity\Persona;
use src\profesores\domain\services\ProfesorStgrService;

/**
 * GestorProfesor — lista de profesores para actividades.
 */
class ProfesorActividad
{
    /** @var list<string> */
    private array $lastAvisos = [];

    public function __construct(
        private ProfesorStgrService $profesorStgrService,
        private AsistentePubRepositoryInterface $asistentePubRepository,
    ) {
    }

    /**
     * Avisos de la última llamada a {@see getArrayProfesoresActividad()} (p. ej. id_nom huérfano).
     *
     * @return list<string>
     */
    public function getLastAvisos(): array
    {
        return $this->lastAvisos;
    }

    /**
     * @param list<int> $aId_activ
     * @return array<int|string, string>
     */
    public function getArrayProfesoresActividad(array $aId_activ = []): array
    {
        $this->lastAvisos = [];
        $aProfesoresDl = $this->profesorStgrService->getArrayProfesoresDl();
        $aProfesoresEx = [];
        $lista = $this->asistentePubRepository->getListaAsistentesDistintos($aId_activ);
        if ($lista === false) {
            $lista = [];
        }
        foreach ($lista as $id_nom) {
            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $this->lastAvisos[] = sprintf(
                    _("No encuentro a nadie con id_nom: %s"),
                    (string) $id_nom,
                );
                continue;
            }
            $obj_persona = PersonaRepositoryResolver::objPauFromInstance($oPersona);
            if ($obj_persona === 'PersonaDl') {
                continue;
            }
            if (!method_exists($oPersona, 'isProfesor_stgr')) {
                continue;
            }
            $profesor_stgr = $oPersona->isProfesor_stgr();
            if (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($profesor_stgr)) {
                continue;
            }

            $ap_nom = $oPersona->getPrefApellidosNombre();

            $aProfesoresEx[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $ap_nom,
                'ap1' => $oPersona->getApellido1Vo()->value(),
                'ap2' => $oPersona->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersona->getNomVo()?->value() ?? '',
            ];
        }
        \src\shared\domain\helpers\FuncTablasSupport::usortProfesoresPorApellidos($aProfesoresEx);

        $aOpciones = \src\shared\domain\helpers\FuncTablasSupport::profesoresOpcionesFromFilas($aProfesoresEx);

        return $aOpciones + ["----------"] + $aProfesoresDl;
    }
}
