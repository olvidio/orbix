<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\IdMatchPersona;
use src\dbextern\domain\SincroDB;
use src\personas\domain\Trasladar;
use src\shared\domain\value_objects\DateTimeLocal;

class CrearPersonaDesdeListasUseCase
{
    private PersonaBDURepositoryInterface $personaBDURepository;
    private IdMatchPersonaRepositoryInterface $idMatchRepository;

    public function __construct(
        PersonaBDURepositoryInterface    $personaBDURepository,
        IdMatchPersonaRepositoryInterface $idMatchRepository
    )
    {
        $this->personaBDURepository = $personaBDURepository;
        $this->idMatchRepository = $idMatchRepository;
    }

    /**
     * Crea una persona en Orbix desde la BDU y la vincula.
     *
     * @return string Error text (empty on success)
     */
    public function __invoke(int $id_nom_listas, string $tipo_persona): string
    {
        $oPersonaListas = $this->personaBDURepository->findById($id_nom_listas);
        if ($oPersonaListas === null) {
            return _("no se encontró la persona en la BDU");
        }

        $oSincroDB = new SincroDB();

        $nombre = $oPersonaListas->getNombre();
        $nx1 = $oPersonaListas->getNx1();
        $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
        $nx2 = $oPersonaListas->getNx2();
        $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
        $f_nacimiento = $oPersonaListas->getFecha_Naci();
        $lugar_nacimiento = $oPersonaListas->getLugar_Naci();
        $dl_listas = $oPersonaListas->getDl();
        $dl_orbix = $oSincroDB->dlListas2Orbix($dl_listas);

        $id_tipo_persona = substr((string)$id_nom_listas, 0, 1);
        $obj_pau = $this->resolverClasePersona($id_tipo_persona);
        if ($obj_pau === null) {
            return sprintf(_("opción no definida para tipo persona %s"), $id_tipo_persona);
        }

        // Buscar si ya está en orbix (otras dl): si está unida, intentar traslado
        $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
        if (!empty($cIdMatch[0]) && !empty($cIdMatch)) {
            $id_orbix = $cIdMatch[0]->getId_orbix();
            $oTrasladar = new Trasladar();
            $oTrasladar->getEsquemas($id_orbix, $tipo_persona);
        }

        $oHoy = new DateTimeLocal();
        // Legacy: crear persona usando clases de personas\model\entity
        $obj = 'personas\\model\\entity\\' . $obj_pau;
        $oPersona = new $obj();

        $oPersona->setSituacion('A');
        $oPersona->setF_situacion($oHoy);
        $oPersona->setNom($nombre);
        $oPersona->setNx1($nx1);
        $oPersona->setApellido1($apellido1_sinprep);
        $oPersona->setNx2($nx2);
        $oPersona->setApellido2($apellido2_sinprep);
        $oPersona->setF_nacimiento($f_nacimiento);
        $oPersona->setLugar_nacimiento($lugar_nacimiento);
        $oPersona->setDl($dl_orbix);

        if ($oPersona->DBGuardar() === false) {
            return _("hay un error, no se ha guardado");
        }
        $id_orbix = $oPersona->getId_nom();

        // Unir
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
}
