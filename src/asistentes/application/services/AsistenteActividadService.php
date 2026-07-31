<?php

namespace src\asistentes\application\services;

use Psr\Container\ContainerInterface;
use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaListadoLookup;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaPub;
use src\shared\infrastructure\GlobalPdo;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;

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
        ConnectionRepositoryFactoryInterface $connectionRepositoryFactory,
        private ContainerInterface $container,
    ) {
        $this->actividadRepository = $actividadRepository;
        $this->actividadAllRepository = $actividadAllRepository;
        /** @var AsistenteRepositoryInterface $configuredRepository */
        $configuredRepository = $connectionRepositoryFactory->createWithConnection(
            AsistenteRepositoryInterface::class,
            GlobalPdo::get('oDBE'),
            GlobalPdo::get('oDBE_Select')
        );
        $this->asistenteRepository = $configuredRepository;
    }

    /**
     * Obtiene las actividades de un asistente
     *
     * @param array<string, mixed> $aWhereNom asociativo con los valores de las variables para el nombre
     * @param array<string, string> $aOperadorNom asociativo con los operadores para cada variable del nombre
     * @param array<string, mixed> $aWhereActividad asociativo con los valores de las variables para la actividad
     * @param array<string, string> $aOperadorActividad asociativo con los operadores para cada variable de la actividad
     * @param bool $reverse TRUE para ordenar de nuevo a viejo
     * @return list<\src\asistentes\domain\entity\Asistente>
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

        $namespace = 'src\asistentes\infrastructure\persistence\postgresql';
        $cAsistencias = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhereNom, $aOperadorNom);

        return $this->ordenarAsistenciasPorFecha($cAsistencias, $a_id_activ_f_ini, $reverse);
    }

    /**
     * Obtiene las asistencias de una persona a determinadas actividades
     *
     * @param int $id_nom ID de la persona
     * @param array<int|string, int> $a_id_activ_f_ini Array de actividades con fecha de inicio
     * @param bool $reverse TRUE para ordenar de nuevo a viejo
     * @return list<\src\asistentes\domain\entity\Asistente>
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

        $namespace = 'src\asistentes\infrastructure\persistence\postgresql';
        $cAsistencias = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhereNom, $aOperadorNom);

        return $this->ordenarAsistenciasPorFecha($cAsistencias, $a_id_activ_f_ini, $reverse);
    }

    /**
     * Ordena las asistencias por fecha de actividad
     *
     * @param list<Asistente> $cAsistencias Colección de asistentes
     * @param array<int|string, int> $a_id_activ_f_ini Array de IDs de actividad ordenados por fecha
     * @return list<Asistente> Asistencias ordenadas
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

            $key = array_search($id_activ, $a_id_activ_f_ini);
            if ($key !== false) {
                $cActividadesOk[$key] = $oAsistente;
            }
            $id_actividad_old = $id_activ;
        }

        if ($reverse === true) {
            krsort($cActividadesOk);
        } else {
            ksort($cActividadesOk);
        }

        return array_values($cActividadesOk);
    }

    /**
     * Obtiene el número de plazas ocupadas por delegación.
     *
     * @param int $iid_activ ID de la actividad
     * @param string $sdl Sigla de la dl que usa la plaza (derecha de "hub>sdl")
     * @param string $dl_hub Sigla propietaria de las plazas (izquierda de "hub>sdl")
     * @return int Número de plazas ocupadas (asignadas/confirmadas)
     */
    public function getPlazasOcupadasPorDl(int $iid_activ, string $sdl = '', string $dl_hub = ''): int
    {
        $mi_dele = ConfigGlobal::mi_delef();

        $oActividad = $this->actividadAllRepository->findById($iid_activ);
        if ($oActividad === null) {
            return 0;
        }
        $dl_org = $oActividad->getDl_org() ?? '';

        $aWhere = ['id_activ' => $iid_activ];
        $aOperators = [];
        $namespace = 'src\asistentes\infrastructure\persistence\postgresql';

        if ($sdl === $mi_dele) {
            if ($dl_org === $sdl) {
                // Organizadora: d_asistentes_all evita duplicar filas entre Dl/Pub/Ex.
                /** @var AsistenteRepositoryInterface $repoAll */
                $repoAll = $this->container->get(AsistenteRepositoryInterface::class);
                $cAsistentes = $repoAll->getAsistentes($aWhere, $aOperators);
            } else {
                $a_Clases = [
                    ['repo' => AsistenteExRepositoryInterface::class, 'get' => 'getAsistentes'],
                    ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
                ];
                $cAsistentes = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
            }
        } elseif ($dl_org === $sdl) {
            $cAsistentes = [];
        } elseif ($dl_org === $mi_dele) {
            // Organizadora contando uso de plazas por otra dl (cedidas / de paso).
            /** @var AsistenteRepositoryInterface $repoAll */
            $repoAll = $this->container->get(AsistenteRepositoryInterface::class);
            $cAsistentes = $repoAll->getAsistentes($aWhere, $aOperators);
        } else {
            $a_Clases = [
                ['repo' => AsistenteOutRepositoryInterface::class, 'get' => 'getAsistentes'],
                ['repo' => AsistenteExRepositoryInterface::class, 'get' => 'getAsistentes'],
            ];
            $cAsistentes = $this->asistenteRepository->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
        }

        $numAsis = 0;
        $vistos = [];
        foreach ($cAsistentes as $oAsistente) {
            $id_nom = $oAsistente->getId_nom();
            if (isset($vistos[$id_nom])) {
                continue;
            }

            [$padre, $child] = $this->parsePropietarioPlaza($oAsistente->getPropietarioVo()?->value() ?? '');

            // "dlp>dlal" (cedida) solo cuenta para sdl=dlal con hub=dlp, nunca como plaza propia dlp>dlp.
            if ($dl_hub !== '' && $padre !== $dl_hub) {
                continue;
            }
            if ($sdl !== '' && $child !== $sdl) {
                continue;
            }

            if (!$this->existePersonaParaContarPlazas($id_nom)) {
                continue;
            }

            $plazaVo = $oAsistente->getPlazaVo()?->value();
            $plaza = empty($plazaVo) ? PlazaId::PEDIDA : $plazaVo;
            if ($plaza < PlazaId::ASIGNADA) {
                continue;
            }

            $vistos[$id_nom] = true;
            $numAsis++;
        }

        return $numAsis;
    }

    /**
     * @return array{0: string, 1: string} [padre, child] de "padre>child"
     */
    private function parsePropietarioPlaza(string $propietario): array
    {
        $propietario = trim($propietario);
        if ($propietario === '' || $propietario === 'xxx') {
            return ['', ''];
        }
        $parts = explode('>', $propietario, 2);

        return [trim($parts[0]), trim($parts[1] ?? '')];
    }

    /**
     * True si la persona existe en directorio global o como PersonaEx (de paso).
     */
    private function existePersonaParaContarPlazas(int $id_nom): bool
    {
        if (Persona::findPersonaEnGlobal($id_nom) !== null) {
            return true;
        }

        /** @var PersonaExRepositoryInterface $personaExRepository */
        $personaExRepository = $this->container->get(PersonaExRepositoryInterface::class);

        return $personaExRepository->findById($id_nom) !== null;
    }

    /**
     * Obtiene todos los asistentes de una actividad
     *
     * @param int $iid_activ ID de la actividad
     * @return array<string, Asistente> Una colección de objetos de tipo Asistente ordenados por apellidos
     */
    public function getAsistentesDeActividad(int $iid_activ): array
    {
        /* Mirar si la actividad es mia o no */
        $oActividad = $this->actividadAllRepository->findById($iid_activ);
        if ($oActividad === null) {
            return [];
        }
        $id_tabla = $oActividad->getIdTablaVo()?->value() ?? '';
        // Si es de la sf quito la 'f'
        $dl = preg_replace('/f$/', '', $oActividad->getDl_org() ?? '');

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

        $namespace = 'src\asistentes\infrastructure\persistence\postgresql';
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

        uksort($cAsistentesOk, [\src\shared\domain\helpers\FuncTablasSupport::class, 'strsinacentocmp']);

        if (!empty($msg_err)) {
            error_log($msg_err);
        }

        return $cAsistentesOk;
    }

    public function buscarAsistencia(int $id_nom, int $id_activ): Asistente|false
    {
        /** @var AsistenteRepositoryInterface $AsistenteRepository */
        $AsistenteRepository = $this->container->get(AsistenteRepositoryInterface::class);
        $cAsistentes = $AsistenteRepository->getAsistentes(['id_nom' => $id_nom, 'id_activ' => $id_activ]);
        if ($cAsistentes !== []) {
            return $cAsistentes[0];
        }

        return false;
    }

    /**
     * para saber el nombre del repositorio que toca según mi dl, y la dl de la
     * actividad a la que asisto
     *
     * @throws \RuntimeException si no se encuentra la persona o la actividad
     */
    public function getRepoAsistente(int $id_nom, int $id_activ): string
    {
        // Los asistentes "de paso" con id negativo viven en d_asistentes_ex.
        // También hay PersonaEx con id positivo (GenerateIdGlobal / p_de_paso_ex).
        if ($id_nom < 0 || $id_activ < 0) {
            return AsistenteExRepositoryInterface::class;
        }

        $oPersona = $this->resolvePersonaParaAsistente($id_nom);
        $dl_persona = $oPersona->getDlVo()?->value() ?? '';
        $clasePersona = $oPersona->getClassName();
        // hay que averiguar si la actividad es de la dl o de fuera.
        $ActividadAllRepository = $this->actividadAllRepository;
        $oActividad = $ActividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            throw new \RuntimeException(sprintf(_('No se ha encontrado la actividad con id: %s'), $id_activ));
        }
        // si es de la sf quito la 'f'
        $dl_org = preg_replace('/f$/', '', $oActividad->getDl_org() ?? '');
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
                throw new \RuntimeException(_("No puede modificar los datos de asistencia de una persona de otra dl"));
            }

        }
        return $repo;
    }

    /**
     * @return PersonaDl|PersonaPub|PersonaEx
     * @throws \RuntimeException
     */
    private function resolvePersonaParaAsistente(int $id_nom): PersonaDl|PersonaPub|PersonaEx
    {
        $persona = Persona::findPersonaEnGlobal($id_nom);
        if ($persona !== null) {
            return $persona;
        }

        /** @var PersonaExRepositoryInterface $personaExRepository */
        $personaExRepository = $this->container->get(PersonaExRepositoryInterface::class);
        $personaEx = $personaExRepository->findById($id_nom);
        if ($personaEx !== null) {
            return $personaEx;
        }

        throw new \RuntimeException(PersonaListadoLookup::mensajeNoEncontrada($id_nom));
    }
}
