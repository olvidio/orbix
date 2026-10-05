<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\support\HuecoOpcionalDePlan;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
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
        private PersonaNotaRepositoryInterface $personaNotaRepository,
        private PersonaExRepositoryInterface $personaExRepository,
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
            $aNotasDePaso = $this->personaNotaRepository->getNotasPersonasDePasoDeRegion($esquemaRegionStgr);

            $aAsignaturas = $this->asignaturaRepository->getArrayAsignaturas();

            $titulo = _('Personas de paso pendientes de certificado');
            /** @var array<int, array{alert: string, asignaturas: string}> $grupos */
            $grupos = [];
            foreach ($aNotasDePaso as $oPersonaNota) {
                $idAsignatura = $oPersonaNota->getId_asignatura();
                if (HuecoOpcionalDePlan::esMarcadorFinCiclo($idAsignatura)) {
                    continue;
                }
                $idNom = $oPersonaNota->getId_nom();
                if (!isset($grupos[$idNom])) {
                    $grupos[$idNom] = ['alert' => '', 'asignaturas' => ''];
                }
                $alert = $grupos[$idNom]['alert'];
                $strAsignaturas = $grupos[$idNom]['asignaturas'];

                $idActiv = $oPersonaNota->getId_activ();
                $acta = $oPersonaNota->getActa();
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

                $grupos[$idNom] = ['alert' => $alert, 'asignaturas' => $strAsignaturas];
            }

            $fichas = $this->fichasDePaso(array_keys($grupos));
            $i = 0;
            foreach ($grupos as $idNom => $grupo) {
                $i++;
                $ficha = $fichas[$idNom] ?? null;
                $apellidosNombre = $ficha !== null
                    ? $ficha->getPrefApellidosNombre()
                    : sprintf(_('sin ficha (%d)'), $idNom);
                $dl = $ficha?->getDl() ?? '';
                $aValores[$i]['sel'] = (string) $idNom;
                $aValores[$i][5] = $idNom;
                $aValores[$i][1] = $apellidosNombre;
                $aValores[$i][2] = $dl;
                $aValores[$i][3] = $grupo['alert'];
                $aValores[$i][4] = $grupo['asignaturas'];
                $aNombre[$i] = $apellidosNombre;
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
     * @param list<int> $idNoms
     * @return array<int, PersonaEx>
     */
    private function fichasDePaso(array $idNoms): array
    {
        $ids = [];
        foreach ($idNoms as $idNom) {
            $idNom = (int) $idNom;
            if ($idNom < 0) {
                $ids[] = $idNom;
            }
        }
        if ($ids === []) {
            return [];
        }

        $fichas = [];
        foreach ($this->personaExRepository->getPersonas(['id_nom' => $ids], ['id_nom' => 'IN']) as $persona) {
            $fichas[$persona->getId_nom()] = $persona;
        }

        return $fichas;
    }

    public static function esAvisoRegionStgr(\Throwable $e): bool
    {
        return RegionStgrAviso::esDlSinRegion($e);
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
