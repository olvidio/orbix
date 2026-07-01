<?php

namespace src\asistentes\application;

use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;

final class PlazaPropietarioAsignacion implements PlazaPropietarioAsignacionInterface
{
    public function __construct(
        private ResumenPlazasService $resumenPlazasService,
    ) {
    }

    public function asegurar(Asistente $asistente, int $plazaActual, int $plazaNueva): string
    {
        if (!ConfigGlobal::is_app_installed('actividadplazas')) {
            return '';
        }
        if ($plazaNueva <= PlazaId::DENEGADA) {
            return '';
        }
        if ($plazaActual >= PlazaId::ASIGNADA) {
            return '';
        }

        $this->resumenPlazasService->setId_activ($asistente->getId_activ());
        $dl_de_paso = $this->resolverDlDePaso($asistente->getId_nom());

        $propietario = $asistente->getPropietarioVo()?->value() ?? '';
        if ($propietario !== '' && $propietario !== 'xxx') {
            if (!$this->resumenPlazasService->esPropiedadClaveDisponible($propietario, $dl_de_paso)) {
                return (string) _('Ya están todas las plazas ocupadas');
            }

            return '';
        }

        $prop = $this->resumenPlazasService->getPrimeraPropiedadLibre($dl_de_paso);
        if ($prop === null) {
            return (string) _('Ya están todas las plazas ocupadas');
        }

        $asistente->setPropietarioVo($prop);

        return '';
    }

    /**
     * @return false|string dl de paso para PersonaEx, false en caso contrario
     */
    private function resolverDlDePaso(int $id_nom): false|string
    {
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if (!is_object($oPersona)) {
            return false;
        }

        $obj_pau = PersonaRepositoryResolver::objPauFromInstance($oPersona);
        if ($obj_pau === 'PersonaEx') {
            $dl = $oPersona->getDl();

            return $dl !== null && $dl !== '' ? $dl : false;
        }

        return false;
    }
}
