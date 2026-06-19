<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\IdMatchPersona;
use src\dbextern\application\support\SincroDBFactory;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSSSC;
use src\personas\domain\Trasladar;
use src\shared\domain\value_objects\DateTimeLocal;

class CrearPersonaDesdeListasUseCase
{
    public function __construct(
        private PersonaBDURepositoryInterface $personaBDURepository,
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private Trasladar $trasladar,
        private SincroDBFactory $sincroDBFactory,
    ) {
    }

    /**
     * @return string Error text (empty on success)
     */
    public function __invoke(int $id_nom_listas, string $tipo_persona): string
    {
        $oPersonaListas = $this->personaBDURepository->findById($id_nom_listas);
        if ($oPersonaListas === null) {
            return _("no se encontró la persona en la BDU");
        }

        $oSincroDB = $this->sincroDBFactory->create();

        $nombre = $oPersonaListas->getNombre();
        $nx1 = $oPersonaListas->getNx1();
        $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
        $nx2 = $oPersonaListas->getNx2();
        $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
        $f_nacimiento = $oPersonaListas->getFecha_Naci();
        $lugar_nacimiento = $oPersonaListas->getLugar_Naci();
        $dl_listas = $oPersonaListas->getDl();
        $dl_orbix = $oSincroDB->dlListas2Orbix($dl_listas);
        if ($dl_orbix === false) {
            return _("no se pudo resolver la delegación de listas");
        }

        $id_tipo_persona = substr((string)$id_nom_listas, 0, 1);
        $obj_pau = $this->resolverClasePersona($id_tipo_persona);
        if ($obj_pau === null) {
            return sprintf(_("opción no definida para tipo persona %s"), $id_tipo_persona);
        }

        $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
        if ($cIdMatch !== []) {
            $id_orbix = $cIdMatch[0]->getId_orbix();
            if ($id_orbix !== null) {
                $this->trasladar->setId_nom($id_orbix);
                $this->trasladar->getEsquemas($id_orbix, $tipo_persona);
            }
        }

        $oHoy = new DateTimeLocal();
        try {
            [$repo, $oPersona] = match ($obj_pau) {
                'PersonaN' => [$this->personaRepositoryResolver->personaNRepository(), new PersonaN()],
                'PersonaAgd' => [$this->personaRepositoryResolver->personaAgdRepository(), new PersonaAgd()],
                'PersonaS' => [$this->personaRepositoryResolver->personaSRepository(), new PersonaS()],
                'PersonaSSSC' => [$this->personaRepositoryResolver->personaSSSCRepository(), new PersonaSSSC()],
                default => throw new \InvalidArgumentException("obj_pau '$obj_pau' no reconocido"),
            };
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $newIdAuto = $repo->getNewId();
        $id_orbix = $repo->getNewIdNom($newIdAuto);

        $oPersona->setId_nom($id_orbix);
        $oPersona->setId_tabla($tipo_persona);
        $oPersona->setSituacion('A');
        $oPersona->setF_situacion($oHoy);
        $oPersona->setNom($nombre);
        $oPersona->setNx1($nx1);
        $oPersona->setApellido1($apellido1_sinprep);
        $oPersona->setNx2($nx2);
        $oPersona->setApellido2($apellido2_sinprep);
        $f_nacimiento_vo = DateTimeLocal::createFromLocal((string)$f_nacimiento);
        $oPersona->setF_nacimiento($f_nacimiento_vo instanceof DateTimeLocal ? $f_nacimiento_vo : null);
        $oPersona->setLugar_nacimiento($lugar_nacimiento);
        $oPersona->setDl($dl_orbix);

        if ($this->guardarPersona($repo, $obj_pau, $oPersona) === false) {
            return _("hay un error, no se ha guardado");
        }

        $oIdMatch = new IdMatchPersona();
        $oIdMatch->setId_listas($id_nom_listas);
        $oIdMatch->setId_orbix($id_orbix);
        $oIdMatch->setId_tabla($tipo_persona);

        if ($this->idMatchRepository->Guardar($oIdMatch) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $this->idMatchRepository->getErrorTxt();
        }

        return '';
    }

    private function resolverClasePersona(string $id_tipo_persona): ?string
    {
        return match ($id_tipo_persona) {
            '1' => 'PersonaN',
            '2' => 'PersonaAgd',
            '3' => 'PersonaS',
            '4' => 'PersonaSSSC',
            default => null,
        };
    }

    /**
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo
     */
    private function guardarPersona(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo,
        string $obj_pau,
        object $persona,
    ): bool {
        return match ($obj_pau) {
            'PersonaN' => $repo instanceof PersonaNRepositoryInterface && $persona instanceof PersonaN
                ? $repo->Guardar($persona) : false,
            'PersonaAgd' => $repo instanceof PersonaAgdRepositoryInterface && $persona instanceof PersonaAgd
                ? $repo->Guardar($persona) : false,
            'PersonaS' => $repo instanceof PersonaSRepositoryInterface && $persona instanceof PersonaS
                ? $repo->Guardar($persona) : false,
            'PersonaSSSC' => $repo instanceof PersonaSSSCRepositoryInterface && $persona instanceof PersonaSSSC
                ? $repo->Guardar($persona) : false,
            default => false,
        };
    }
}
