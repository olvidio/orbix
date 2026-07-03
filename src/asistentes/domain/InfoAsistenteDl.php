<?php

namespace src\asistentes\domain;

use src\actividades\domain\value_objects\StatusId;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\domain\DatosInfoRepo;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * {@see DatosInfoRepo} para dossier persona `1301` cuando `tipo.class` es `AsistenteDl`:
 * mismo bloque datos que {@see Select_actividades_de_una_persona} vía modo segmento JSON,
 * pero para el fallback legacy {@see datos_tabla} (sin clase Select cargable en el servidor).
 */
class InfoAsistenteDl extends DatosInfoRepo
{
    /** Dossier “actividades de una persona”. */
    private const ID_TIPO_DOSSIER = 1301;

    public function __construct(
        private AsistenteActividadService $asistenteActividadService,
        private AsistenteDlRepositoryInterface $asistenteDlRepository,
    ) {
        $this->setTxtTitulo(_('actividades como asistente'));
        $this->setTxtEliminar(_('¿Está seguro que desea borrar a esta persona de esta actividad?'));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\asistentes\\domain\\entity\\Asistente');
        $this->setMetodoGestor('getAsistentes');
        $this->setPau('p');

        $this->setRepositoryInterface(AsistenteDlRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return self::ID_TIPO_DOSSIER;
    }

    /**
     * Alineado con {@see Select_actividades_de_una_persona::cursoWhereFromModo()} modo por defecto (1).
     */
    /**
     * @return list<Asistente>
     */
    public function getColeccion(): array
    {
        if (empty($this->id_pau)) {
            return [];
        }
        $mes = date('m');
        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        $fin_m = $oConfig->getMesFinStgr();
        $any = ($mes > $fin_m) ? ((int)date('Y') + 1) : date('Y');
        $inicurs_ca = FuncTablasSupport::cursoEst('inicio', $any)->format('Y-m-d');
        $fincurs_ca = FuncTablasSupport::cursoEst('fin', $any)->format('Y-m-d');

        $aWhereActividad = ['_ordre' => 'f_ini', 'status' => StatusId::ACTUAL, 'f_ini' => "'$inicurs_ca','$fincurs_ca'"];
        $aOperadorActividad = ['f_ini' => 'BETWEEN'];

        $aWhereNom = ['id_nom' => $this->id_pau];
        $aOperadorNom = [];

        return $this->asistenteActividadService->getActividadesDeAsistente(
            $aWhereNom,
            $aOperadorNom,
            $aWhereActividad,
            $aOperadorActividad,
            true,
        );
    }

    /**
     * PK compuesta `(id_activ, id_nom)`: {@see DatosInfoRepo::getFicha} base llama {@see AsistenteRepositoryInterface::findById} con un solo argumento.
     */
    public function getFicha(): ?Asistente
    {
        switch ($this->mod) {
            case 'nuevo':
                $ficha = parent::getFicha();
                return $ficha instanceof Asistente ? $ficha : null;
            case 'eliminar':
            case 'editar':
                $pkRaw = $this->a_pkey;
                if (empty($pkRaw)) {
                    return null;
                }
                if (is_array($pkRaw)) {
                    $pk = $pkRaw;
                } elseif (is_string($pkRaw)) {
                    $pk = json_decode($pkRaw, true);
                } else {
                    return null;
                }
                if (!is_array($pk) || !isset($pk['id_activ'], $pk['id_nom'])) {
                    return null;
                }
                return $this->asistenteDlRepository->findById((int) $pk['id_activ'], (int) $pk['id_nom']);
            default:
                return null;
        }
    }
}
