<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaPub;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\RegionStgrAviso;

/**
 * @return array{
 *   titulo: string,
 *   titulo_busqueda_por_apellidos: string,
 *   msg_err: string,
 *   aviso: string,
 *   a_valores: array<int|string, array<string|int, mixed>>,
 *   a_Nombre?: array<int, string>
 * }
 */
final class MatriculasListaOtrasRData
{
    public function __construct(
        private PersonaPubRepositoryInterface $personaPubRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ActaRepositoryInterface $actaRepository,
        private DelegacionRepositoryInterface $delegacionRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   titulo: string,
     *   titulo_busqueda_por_apellidos: string,
     *   msg_err: string,
     *   aviso: string,
     *   a_valores: array<int|string, array<string|int, mixed>>,
     *   a_Nombre?: array<int, string>
     * }
     */
    public function execute(array $input): array
    {
        $apellido1 = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'apellido1');
        $esquemaRegionStgr = $this->resolveEsquemaRegionStgr(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'esquema_region_stgr'));
        $tituloBusqueda = _('búsqueda por apellidos');
        $titulo = '';
        $msgErr = '';
        /** @var array<string, array<int|string, string>> $problemasRegionStgr */
        $problemasRegionStgr = [];
        $aValores = [];
        $aNombre = [];

        if ($apellido1 !== '') {
            $aWhere = [
                'apellido1' => '^' . $apellido1,
                'situacion' => 'A',
                '_ordre' => 'dl,stgr,apellido1,nom',
            ];
            $aOperador = ['apellido1' => 'sin_acentos'];
            $sinRegionStgrPorIdNom = [];
            $cPersonas = $this->personaPubRepository->getPersonasParaListado($aWhere, $aOperador, $problemasRegionStgr, $sinRegionStgrPorIdNom);
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
                $aValores[$i][3] = isset($sinRegionStgrPorIdNom[$idNom]) ? '⚠' : '';
                $aValores[$i][4] = '';
                $aNombre[$i] = $apellidosNombre;
            }
        } else {
            $aWhere = ['json_certificados' => 'x', '_ordre' => 'id_nom'];
            $aOperador = ['json_certificados' => 'IS NULL'];
            $personaNotaOtraRepo = DependencyResolver::make(
                PersonaNotaOtraRegionStgrRepositoryInterface::class,
                ['esquema_region_stgr' => $esquemaRegionStgr],
            );
            if (!$personaNotaOtraRepo instanceof PersonaNotaOtraRegionStgrRepositoryInterface) {
                throw new \RuntimeException(_('No se pudo resolver el repositorio de notas de otras regiones'));
            }
            $aNotasOtrasRegiones = $personaNotaOtraRepo->getPersonaNotas($aWhere, $aOperador);

            $aAsignaturas = $this->asignaturaRepository->getArrayAsignaturas();

            $titulo = _('Lista de alumnos de otras regiones pendientes de generar certificado');
            $i = 0;
            $msgErr = '';
            $strAsignaturas = '';
            $idNomAnterior = '';
            $alert = '';
            $idNom = '';
            foreach ($aNotasOtrasRegiones as $oPersonaNotaOtraRegionDB) {
                $i++;
                $idNom = $oPersonaNotaOtraRegionDB->getId_nom();

                if ($idNomAnterior !== '' && $idNom !== $idNomAnterior) {
                    $marcaRegionStgr = false;
                    $oPersona = $this->findPersonaEnGlobal($idNomAnterior, $problemasRegionStgr, $marcaRegionStgr);
                    if ($oPersona === null) {
                        $msgErr .= "<br>No encuentro a nadie con id_nom $idNomAnterior en  " . __FILE__ . ': line ' . __LINE__;
                    } else {
                        $apellidosNombre = $oPersona->getPrefApellidosNombre();
                        $dl = $oPersona->getDl();
                        if ($marcaRegionStgr) {
                            $alert = self::alertaConRegionStgr($alert);
                        }

                        $aValores[$i]['sel'] = (string)$idNomAnterior;
                        $aValores[$i][5] = $idNomAnterior;
                        $aValores[$i][1] = $apellidosNombre;
                        $aValores[$i][2] = $dl;
                        $aValores[$i][3] = $alert;
                        $aValores[$i][4] = $strAsignaturas;
                        $aNombre[$i] = $apellidosNombre;
                    }
                    $strAsignaturas = '';
                    $alert = '';
                }
                $idAsignatura = $oPersonaNotaOtraRegionDB->getId_asignatura();
                $idActiv = $oPersonaNotaOtraRegionDB->getId_activ();
                $acta = $oPersonaNotaOtraRegionDB->getActa();
                if ($acta !== null && $acta !== '') {
                    $Acta = $this->actaRepository->findById($acta);
                    if ($Acta !== null && ($Acta->getPdfVo() === null)) {
                        $alert .= '!';
                    }
                }
                $nomAsignatura = $aAsignaturas[$idAsignatura];
                $nomActiv = '';
                if ($idActiv !== null) {
                    $oActividad = $this->actividadAllRepository->findById($idActiv);
                    if ($oActividad !== null) {
                        $nomActiv = $oActividad->getNom_activ();
                    }
                }

                $strAsignaturas .= $strAsignaturas === '' ? '' : ', ';
                $strAsignaturas .= trim((string)$nomAsignatura);
                $strAsignaturas .= $nomActiv === '' ? '' : "($nomActiv)";

                $idNomAnterior = $idNom;
            }
            if ($idNom !== '') {
                $marcaRegionStgr = false;
                $oPersona = $this->findPersonaEnGlobal($idNom, $problemasRegionStgr, $marcaRegionStgr);
                if ($oPersona === null) {
                    $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom en  " . __FILE__ . ': line ' . __LINE__;
                } else {
                    $apellidosNombre = $oPersona->getPrefApellidosNombre();
                    $dl = $oPersona->getDl();
                    if ($marcaRegionStgr) {
                        $alert = self::alertaConRegionStgr($alert);
                    }
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
            'aviso' => RegionStgrAviso::formatear($problemasRegionStgr),
            'a_valores' => $aValores,
        ];
    }

    /**
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     */
    private function findPersonaEnGlobal(
        int $idNom,
        array &$problemasRegionStgr,
        bool &$marcaRegionStgr = false,
    ): PersonaDl|PersonaPub|null {
        $marcaRegionStgr = false;
        try {
            $persona = Persona::findPersonaEnGlobal($idNom);
            if ($persona !== null) {
                return $persona;
            }
        } catch (\RuntimeException $e) {
            if (!RegionStgrAviso::esDlSinRegion($e)) {
                throw $e;
            }
            /** @var array<string, array<int|string, string>> $problemasParaRegistrar */
            $problemasParaRegistrar = $problemasRegionStgr;
            RegionStgrAviso::registrar($problemasParaRegistrar, $e);
            $problemasRegionStgr = self::normalizeProblemasKeys($problemasParaRegistrar);
        }

        return $this->personaPubRepository->findByIdParaListado($idNom, $problemasRegionStgr, $marcaRegionStgr);
    }

    /**
     * @param array<string, array<int|string, string>> $problemas
     * @return array<string, array<int|string, string>>
     */
    private static function normalizeProblemasKeys(array $problemas): array
    {
        $normalized = [];
        foreach ($problemas as $tipo => $items) {
            $normalized[$tipo] = [];
            foreach ($items as $key => $value) {
                $normalized[$tipo][(string) $key] = (string) $value;
            }
        }

        return $normalized;
    }

    public static function esAvisoRegionStgr(\Throwable $e): bool
    {
        return RegionStgrAviso::esDlSinRegion($e);
    }

    private static function alertaConRegionStgr(string $alert): string
    {
        return str_contains($alert, '⚠') ? $alert : $alert . '⚠';
    }

    /**
     * Esquema SV/SF de la región STGR (p. ej. H-Hv). El frontend ya no envía
     * `esquema` en POST como en apps/; se deduce de la sesión.
     */
    private function resolveEsquemaRegionStgr(string $esquemaInput): string
    {
        if ($esquemaInput !== '') {
            return $esquemaInput;
        }

        $datosRegion = $this->delegacionRepository->mi_region_stgr();
        $esquemaRaw = $datosRegion['esquema_region_stgr'] ?? '';
        $esquema = is_scalar($esquemaRaw) ? (string) $esquemaRaw : '';
        if ($esquema !== '') {
            return $esquema;
        }

        $esquemaSesion = ConfigGlobal::mi_region_dl();
        if ($esquemaSesion !== '') {
            return $esquemaSesion;
        }

        throw new \RuntimeException(_('No se pudo determinar el esquema región STGR de la sesión.'));
    }
}
