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
    public function __construct(
        private ProfesorStgrService $profesorStgrService,
        private AsistentePubRepositoryInterface $asistentePubRepository,
    ) {
    }

    /**
     * @param list<int> $aId_activ
     * @return array<int|string, string>
     */
    public function getArrayProfesoresActividad(array $aId_activ = []): array
    {
        $aProfesoresDl = $this->profesorStgrService->getArrayProfesoresDl();
        $aProfesoresEx = [];
        $msg_err = '';
        $lista = $this->asistentePubRepository->getListaAsistentesDistintos($aId_activ);
        if ($lista === false) {
            $lista = [];
        }
        foreach ($lista as $id_nom) {
            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
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

        $AllOpciones = $aOpciones + ["----------"] + $aProfesoresDl;

        if (!empty($msg_err)) {
            echo $msg_err;
        }

        return $AllOpciones;
    }
}
