<?php

namespace src\asistentes\application\services;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\entity\Persona;

/**
 * Servicio de aplicación para operaciones de asistentes que requieren
 * coordinación entre múltiples repositorios
 *
 * @package orbix
 * @subpackage asistentes
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/12/2025
 */
class AsistenteActividadService
{
    private ActividadRepositoryInterface $actividadRepository;
    private ActividadAllRepositoryInterface $actividadAllRepository;
    private AsistenteRepositoryInterface $asistenteRepository;

    public function __construct(
        ActividadRepositoryInterface $actividadRepository,
        ActividadAllRepositoryInterface $actividadAllRepository,
        AsistenteRepositoryInterface $asistenteRepository
    ) {
        $this->actividadRepository = $actividadRepository;
        $this->actividadAllRepository = $actividadAllRepository;
        $this->asistenteRepository = $asistenteRepository;
        $oDbl = $GLOBALS['oDBE'];
        $this->asistenteRepository->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->asistenteRepository->setoDbl_select($oDbl_Select);
    }

    /**
     * Obtiene las actividades de un asistente
     *
     * @param array $aWhereNom asociativo con los valores de las variables para el nombre
     * @param array $aOperadorNom asociativo con los operadores para cada variable del nombre
     * @param array $aWhereActividad asociativo con los valores de las variables para la actividad
     * @param array $aOperadorActividad asociativo con los operadores para cada variable de la actividad
     * @param bool $reverse TRUE para ordenar de nuevo a viejo
     * @return array Una colección de objetos de tipo Asistente
     */
    public function getActividadesDeAsistente(
        array $aWhereNom,
        array $aOperadorNom,
        array $aWhereActividad = [],
        array $aOperadorActividad = [],
        bool  $reverse = false
    ): array
    {
        // Todas las actividades de la persona
        $a_id_activ_f_ini = $this->actividadRepository->getArrayIdsWithKeyFini($aWhereActividad, $aOperadorActividad);

        if (empty($a_id_activ_f_ini)) {
            return [];
        }
        // Importa el orden, se queda con la primera
        $a_Clases = [
            ['repo' => AsistenteDlRepositoryInterface::class, 'get' => 'getAsistentes'],
            ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
            ['repo' => AsistenteExRepositoryInterface::class, 'get' => 'getAsistentes'],
        ];

        $namespace = 'src\asistentes\infrastructure\repositories';
        $cAsistencias = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhereNom, $aOperadorNom);

        return $this->ordenarAsistenciasPorFecha($cAsistencias, $a_id_activ_f_ini, $reverse);
    }

    /**
     * Obtiene las asistencias de una persona a determinadas actividades
     *
     * @param int $id_nom ID de la persona
     * @param array $a_id_activ_f_ini Array de actividades con fecha de inicio
     * @param bool $reverse TRUE para ordenar de nuevo a viejo
     * @return array Una colección de objetos de tipo Asistente
     */
    public function getAsistenciasPersonaDeActividades(int $id_nom, array $a_id_activ_f_ini, bool $reverse = false): array
    {
        $aWhereNom['id_nom'] = $id_nom;
        $aOperadorNom = [];

        // Importa el orden, se queda con la primera
        $a_Clases = [
            ['repo' => AsistenteDlRepositoryInterface::class, 'get' => 'getAsistentes'],
            ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
            ['repo' => AsistenteExRepositoryInterface::class, 'get' => 'getAsistentes'],
        ];

        $namespace = 'src\asistentes\infrastructure\repositories';
        $cAsistencias = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhereNom, $aOperadorNom);

        return $this->ordenarAsistenciasPorFecha($cAsistencias, $a_id_activ_f_ini, $reverse);
    }

    /**
     * Ordena las asistencias por fecha de actividad
     *
     * @param array $cAsistencias Colección de asistentes
     * @param array $a_id_activ_f_ini Array de IDs de actividad ordenados por fecha
     * @param bool $reverse TRUE para ordenar de nuevo a viejo
     * @return array Asistencias ordenadas
     */
    private function ordenarAsistenciasPorFecha(array $cAsistencias, array $a_id_activ_f_ini, bool $reverse): array
    {
        // Descarto los que no están
        $cActividadesOk = [];
        $id_actividad_old = 0;

        foreach ($cAsistencias as $oAsistente) {
            $id_activ = $oAsistente->getId_activ();

            // Si es la misma actividad salto
            if ($id_activ === $id_actividad_old) {
                continue;
            }

            if ($key = array_search($id_activ, $a_id_activ_f_ini)) {
                $cActividadesOk[$key] = $oAsistente;
            }
            $id_actividad_old = $id_activ;
        }

        if ($reverse === true) {
            krsort($cActividadesOk);
        } else {
            ksort($cActividadesOk);
        }

        return $cActividadesOk;
    }

    /**
     * Obtiene el número de plazas ocupadas por delegación
     *
     * @param int $iid_activ ID de la actividad
     * @param string $sdl Sigla de la delegación
     * @param string $dl_hub Sigla de la delegación propietaria de las plazas
     * @return int Número de plazas ocupadas
     */
    public function getPlazasOcupadasPorDl(int $iid_activ, string $sdl = '', string $dl_hub = ''): int
    {
        $mi_dele = ConfigGlobal::mi_delef();

        /* Mirar si la actividad es mia o no */
        $oActividad = $this->actividadAllRepository->findById($iid_activ);
        $dl_org = $oActividad->getDl_org();
        $id_tabla = $oActividad->getIdTablaVo()->value();

        $aWhere['id_activ'] = $iid_activ;
        $aOperators = [];
        $namespace = 'src\asistentes\infrastructure\repositories';
        $msg_err = '';

        if ($sdl == $mi_dele) {
            if ($dl_org == $sdl) {
                $a_Clases = [
                    ['repo' => AsistenteDlRepositoryInterface::class, 'get' => 'getAsistentes'],
                    ['repo' => AsistentePubRepositoryInterface::class, 'get' => 'getAsistentes'], //Quitar los de mi dl?
                ];
                $cAsistentes = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
            } else {
                $a_Clases = [
                    ['repo' => AsistenteExRepositoryInterface::class, 'get' => 'getAsistentes'],
                    ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
                ];
                $cAsistentes = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
            }
        } else {
            if ($dl_org == $sdl) {
                $cAsistentes = [];
            } else {
                if ($dl_org == $mi_dele) {
                    $a_Clases = [
                        ['repo' => AsistentePubRepositoryInterface::class, 'get' => 'getAsistentes'],
                    ];
                    $cAsistentes = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
                } else {
                    $a_Clases = [
                        ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
                    ];
                    $cAsistentes = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
                }
            }
        }

        $numAsis = 0;
        foreach ($cAsistentes as $oAsistente) {
            $id_nom = $oAsistente->getId_nom();
            $propietario = $oAsistente->getPropietarioVo()->value() ?? '';
            $padre = strtok($propietario, '>');
            $child = strtok('>');

            if (!empty($dl_hub) && $dl_hub != $padre) continue;
            if ($sdl != $child) continue;

            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $msg_err .= "<br>No encuentro a nadie con id_nom $id_nom en  " . __FILE__ . ": line " . __LINE__;
                $msg_err .= "<br>" . _("borro la asistencia");
                $id_tabla = $oAsistente->getIdTablaVo()->value();
                switch ($id_tabla) {
                    case 'dl':
                        $repo = $GLOBALS['container']->get(AsistenteDlRepositoryInterface::class);
                        break;
                    case 'ex':
                        $repo = $GLOBALS['container']->get(AsistenteExRepositoryInterface::class);
                        break;
                    case 'out':
                        $repo = $GLOBALS['container']->get(AsistenteOutRepositoryInterface::class);
                        break;
                }
                $repo->Eliminar($oAsistente);
                continue;
            }

            $plaza = empty($oAsistente->getPlazaVo()->value()) ? PlazaId::PEDIDA : $oAsistente->getPlazaVo()->value();
            // Sólo cuento las asignadas
            if ($plaza < PlazaId::ASIGNADA) continue;

            $numAsis++;
        }

        if (!empty($msg_err)) {
            echo $msg_err;
        }

        return $numAsis;
    }

    /**
     * Obtiene todos los asistentes de una actividad
     *
     * @param int $iid_activ ID de la actividad
     * @return array Una colección de objetos de tipo Asistente ordenados por apellidos
     */
    public function getAsistentesDeActividad(int $iid_activ): array
    {
        /* Mirar si la actividad es mia o no */
        $oActividad = $this->actividadAllRepository->findById($iid_activ);
        $id_tabla = $oActividad->getIdTablaVo()->value();
        // Si es de la sf quito la 'f'
        $dl = preg_replace('/f$/', '', $oActividad->getDl_org());

        $aWhere['id_activ'] = $iid_activ;
        $aOperators = [];

        $msg_err = '';
        if ($dl === ConfigGlobal::mi_delef()) {
            // Todos los asistentes
            /* Buscar en los tres tipos de asistente: Dl, IN y Out. */
            $a_Clases = [
                ['repo' => AsistenteDlRepositoryInterface::class, 'get' => 'getAsistentes'],
                ['repo' => AsistentePubRepositoryInterface::class, 'get' => 'getAsistentes'],
                ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
            ];
        } else {
            if ($id_tabla === 'dl') {
                $a_Clases = [
                    ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
                ];
            } else {
                $a_Clases = [
                    ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
                    ['repo' => AsistenteExRepositoryInterface::class, 'get' => 'getAsistentes'],
                ];
            }
        }

        $namespace = 'src\asistentes\infrastructure\repositories';
        $cAsistentes = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);

        $cAsistentesOk = [];
        foreach ($cAsistentes as $oAsistente) {
            $id_nom = $oAsistente->getId_nom();
            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                continue;
            }
            $apellidos = $oPersona->getPrefApellidosNombre();
            $cAsistentesOk[$apellidos] = $oAsistente;
        }

        uksort($cAsistentesOk, "core\strsinacentocmp");

        if (!empty($msg_err)) {
            echo $msg_err;
        }

        return $cAsistentesOk;
    }

    public function buscarAsistencia($id_nom, $id_activ)
    {
        $AsistenteRepository = $GLOBALS['container']->get(AsistenteRepositoryInterface::class);
        $cAsistentes = $AsistenteRepository->getAsistentes(['id_nom' => $id_nom, 'id_activ' => $id_activ]);
        if (is_array($cAsistentes) && !empty($cAsistentes)) {
            return $cAsistentes[0];
        } else {
            return FALSE;
        }
    }

    /**
     * para saber el nombre del repositorio que toca según mi dl, y la dl de la
     * actividad a la que asisto
     *
     */
    public function getRepoAsistente(int $id_nom, int $id_activ): string
    {
        $msg_err = '';
        // Buscar la dl del asistente
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            $msg_err = "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            exit($msg_err);
        }
        $dl_persona = $oPersona->getDlVo()->value();
        $clasePersona = $oPersona->getClassName();
        // hay que averiguar si la actividad es de la dl o de fuera.
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($id_activ);
        // si es de la sf quito la 'f'
        $dl_org = preg_replace('/f$/', '', $oActividad->getDl_org());
        $claseActividad = $oActividad->getClassName();

        $repo = null;
        if ($dl_persona === ConfigGlobal::mi_dele()) { // Persona de mi dl
            if ($dl_org === ConfigGlobal::mi_dele()) {
                switch ($clasePersona) {
                    case 'PersonaDl':
                        $repo = AsistenteDlRepositoryInterface::class;
                        break;
                    case 'PersonaEx':
                        $repo = AsistenteExRepositoryInterface::class;
                        break;
                    default:
                        $repo = AsistentePubRepositoryInterface::class;
                        break;
                }
            } elseif ($claseActividad === 'ActividadEx') {
                $repo = AsistenteDlRepositoryInterface::class;
            } else {
                $repo = AsistenteOutRepositoryInterface::class;
            }
        } else { // persona de otra dl
            if ($clasePersona === 'PersonaEx') {
                if ($dl_org === ConfigGlobal::mi_dele()) {
                    $repo = AsistenteDlRepositoryInterface::class;
                } else {
                    $repo = AsistenteExRepositoryInterface::class;
                }
            } else {
                $repo = AsistentePubRepositoryInterface::class;
            }

            // comprobar que es una actividad de mi dl, si no no tiene permiso
            if ($dl_org !== ConfigGlobal::mi_dele() && $claseActividad !== 'ActividadEx') {
                exit (_("No puede modificar los datos de asistencia de una persona de otra dl"));
                exit (_("los datos de asistencia los modifica la dl del asistente"));
            }

        }
        return $repo;
    }
}
