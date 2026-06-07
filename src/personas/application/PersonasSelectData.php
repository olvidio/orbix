<?php

namespace src\personas\application;

use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\RegionStgrAviso;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\value_objects\PauType;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Caso de uso detras del endpoint `/src/personas/personas_select_data`.
 *
 * Aglutina toda la interaccion con `src/` (repositorios de personas y centros,
 * preferencia del usuario, traduccion de nivel_stgr) para que
 * `frontend/personas/controller/personas_select.php` no tenga `use src\\...`.
 *
 * Devuelve un array serializable con los datos crudos que la vista usa para
 * montar la `web\Lista`. No instancia componentes de UI (es responsabilidad del
 * frontend) ni emite HTML.
 */
final class PersonasSelectData
{
    public function __construct(
        private PersonaDlRepositoryInterface $personaDlRepository,
        private PersonaSSSCRepositoryInterface $personaSSSCRepository,
        private PersonaSRepositoryInterface $personaSRepository,
        private PersonaNRepositoryInterface $personaNRepository,
        private PersonaNaxRepositoryInterface $personaNaxRepository,
        private PersonaAgdRepositoryInterface $personaAgdRepository,
        private PersonaPubRepositoryInterface $personaPubRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroRepositoryInterface $centroRepository,
        private PreferenciaRepositoryInterface $preferenciaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input habitualmente `$_POST`
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $tabla = input_string($input, 'tabla');
        $Qna = input_string($input, 'na');
        $tipo = input_string($input, 'tipo');
        $Qes_sacd = input_int($input, 'es_sacd');
        $Qexacto = input_string($input, 'exacto');
        $Qcmb = input_string($input, 'cmb');
        $Qnombre = input_string($input, 'nombre');
        $Qapellido1 = input_string($input, 'apellido1');
        $Qapellido2 = input_string($input, 'apellido2');
        $Qcentro = input_string($input, 'centro');

        $aWhere = [];
        $aOperador = [];
        $oPerm = $_SESSION['oPerm'] ?? null;
        $tienePermOficina = static fn (string $perm): bool => $oPerm instanceof XPermisos
            && $oPerm->have_perm_oficina($perm);

        if (ConfigGlobal::mi_role_pau() === PauType::PAU_NOM) {
            $sessionAuth = $_SESSION['session_auth'] ?? null;
            $oMiUsuario = is_array($sessionAuth) ? ($sessionAuth['MiUsuario'] ?? null) : null;
            if (!is_object($oMiUsuario) || !method_exists($oMiUsuario, 'getCsv_id_pau')) {
                return ['error' => _('No se encuentra el usuario')];
            }
            $id_nom = (int) $oMiUsuario->getCsv_id_pau();
            $aWhere = ['id_nom' => $id_nom];
            $PersonaDlrepository = $this->personaDlRepository;
            $oPersona = $PersonaDlrepository->findById($id_nom);
            $tabla = match ($oPersona?->getId_tabla()) {
                'n' => 'p_numerarios',
                's' => 'p_supernumerarios',
                'x' => 'p_nax',
                'a' => 'p_agregados',
                'pa', 'pn' => 'p_de_paso_ex',
                default => 'nada',
            };
        } else {
            $aWhereCtr = [];
            $aOperadorCtr = [];

            if (!empty($Qapellido1)) {
                $aWhere['apellido1'] = $Qapellido1;
                if (empty($Qexacto)) {
                    $aWhere['apellido1'] = '^' . $aWhere['apellido1'];
                    $aOperador['apellido1'] = 'sin_acentos';
                }
            }
            if (!empty($Qapellido2)) {
                $aWhere['apellido2'] = $Qapellido2;
                if (empty($Qexacto)) {
                    $aWhere['apellido2'] = '^' . $aWhere['apellido2'];
                    $aOperador['apellido2'] = 'sin_acentos';
                }
            }
            if (!empty($Qnombre)) {
                $aWhere['nom'] = $Qnombre;
                if (empty($Qexacto)) {
                    $aWhere['nom'] = '^' . $aWhere['nom'];
                    $aOperador['nom'] = 'sin_acentos';
                }
            }
            if (!empty($Qcentro)) {
                if (!empty($Qexacto)) {
                    $aWhereCtr['nombre_ubi'] = addslashes(strtr($Qcentro, "+", "."));
                } else {
                    $nom_ubi = addslashes($Qcentro);
                    $nom_ubi = str_replace("+", "\+", $nom_ubi);
                    $aWhereCtr['nombre_ubi'] = '^' . $nom_ubi;
                    $aOperadorCtr['nombre_ubi'] = 'sin_acentos';
                }
            }
            if (empty($Qcmb)) {
                $aWhere['situacion'] = 'A';
            } elseif (!$tienePermOficina('dtor')) {
                $aWhere['situacion'] = 'B';
                $aOperador['situacion'] = '!=';
            }
            if ($Qes_sacd === 1) {
                $aWhere['sacd'] = 't';
            }

            if (!empty($aWhereCtr)) {
                $gesCentros = $this->centroDlRepository;
                $cCentros = $gesCentros->getCentros($aWhereCtr, $aOperadorCtr);
                $aId_ctrs = [];
                foreach ($cCentros as $oCentro) {
                    $aId_ctrs[] = $oCentro->getId_ubi();
                }
                if (!empty($aId_ctrs)) {
                    $aWhere['id_ctr'] = "{" . implode(', ', $aId_ctrs) . "}";
                    $aOperador['id_ctr'] = 'ANY';
                } else {
                    $tabla = 'nada';
                }
            }
        }

        $id_tabla = '';
        $permiso = 1;
        $obj_pau = '';
        $cPersonas = [];
        $problemasRegionStgr = [];
        $sinRegionStgrPorIdNom = [];
        switch ($tabla) {
            case 'p_sssc':
                $obj_pau = 'PersonaSSSC';
                $cPersonas = $this->personaSSSCRepository->getPersonas($aWhere, $aOperador);
                if ($tienePermOficina('des')) {
                    $permiso = 3;
                }
                break;
            case 'p_supernumerarios':
                $obj_pau = 'PersonaS';
                $cPersonas = $this->personaSRepository->getPersonas($aWhere, $aOperador);
                if ($tienePermOficina('sg')) {
                    $permiso = 3;
                }
                break;
            case 'p_numerarios':
                $obj_pau = 'PersonaN';
                $cPersonas = $this->personaNRepository->getPersonas($aWhere, $aOperador);
                if ($tienePermOficina('sm')) {
                    $permiso = 3;
                }
                break;
            case 'p_nax':
                $obj_pau = 'PersonaNax';
                $repoNax = $this->personaNaxRepository;
                $cPersonas = $repoNax->getPersonas($aWhere, $aOperador);
                if ($tienePermOficina('nax')) {
                    $permiso = 3;
                }
                break;
            case 'p_agregados':
                $obj_pau = 'PersonaAgd';
                $cPersonas = $this->personaAgdRepository->getPersonas($aWhere, $aOperador);
                if ($tienePermOficina('agd')) {
                    $permiso = 3;
                }
                break;
            case 'p_de_paso':
            case 'p_de_paso_ex':
                if (!empty($Qna)) {
                    $aWhere['id_tabla'] = 'p' . $Qna;
                    $id_tabla = 'p' . $Qna;
                }
                $obj_pau = 'PersonaEx';
                $cPersonas = $this->personaPubRepository
                    ->getPersonasParaListado($aWhere, $aOperador, $problemasRegionStgr, $sinRegionStgrPorIdNom);
                if (
                    $tienePermOficina('sm')
                    || $tienePermOficina('agd')
                    || $tienePermOficina('des')
                    || $tienePermOficina('sg')
                    || $tienePermOficina('est')
                ) {
                    $permiso = 3;
                }
                break;
            case 'nada':
                return ['error' => _("No se encuentra ningún centro con esta condición")];
        }

        $sPrefs = '';
        $PreferenciaRepository = $this->preferenciaRepository;
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        /** @var array<string, mixed>|null $sessionAuthTyped */
        $sessionAuthTyped = is_array($sessionAuth) ? $sessionAuth : null;
        $idUsuario = $sessionAuthTyped !== null ? input_int($sessionAuthTyped, 'id_usuario') : 0;
        $oPreferencia = $PreferenciaRepository->findById($idUsuario, 'tabla_presentacion');
        if ($oPreferencia !== null) {
            $sPrefs = (string)$oPreferencia->getPreferencia();
        }

        $aNivelStgr = NivelStgrId::getArrayNivelStgr();
        $ambito = ConfigGlobal::mi_ambito();
        $centroRepository = $ambito === 'rstgr'
            ? $this->centroRepository
            : $this->centroDlRepository;

        $a_personas = [];
        foreach ($cPersonas as $oPersona) {
                $id_tabla_persona = (string)$oPersona->getId_tabla();
                $id_nom = (int)$oPersona->getId_nom();
                $nom = (string)$oPersona->getPrefApellidosNombre();

                $nombre_ubi = '';
                if ($obj_pau !== 'PersonaEx') {
                    $id_ctr = $oPersona->getId_ctr();
                    if ($id_ctr !== null) {
                        $oCentroDl = $centroRepository->findById($id_ctr);
                        $nombre_ubi = (string)($oCentroDl?->getNombre_ubi() ?? '');
                    }
                } else {
                    $nombre_ubi = (string)($oPersona->getDl() ?? '');
                }

                $fila = [
                    'id_nom' => $id_nom,
                    'id_tabla' => $id_tabla_persona,
                    'nom' => $nom,
                    'nombre_ubi' => $nombre_ubi,
                    'nivel_stgr' => '',
                    'situacion' => '',
                    'f_situacion' => '',
                ];
                if (($tabla === 'p_numerarios' || $tabla === 'p_agregados') && $tipo !== 'planning') {
                    $fila['nivel_stgr'] = (string)($aNivelStgr[$oPersona->getNivel_stgr()] ?? '');
                }
                if (!empty($Qcmb)) {
                    $fila['situacion'] = (string)$oPersona->getSituacion();
                    $fila['f_situacion'] = (string)($oPersona->getF_situacion()?->getFromLocal() ?? '');
                }
                if (isset($sinRegionStgrPorIdNom[$id_nom])) {
                    RegionStgrAviso::registrarPersonaSinSchema(
                        $problemasRegionStgr,
                        $id_nom,
                        $nom,
                        (string)($oPersona->getDl() ?? ''),
                    );
                }
                $a_personas[$nom . '_' . $id_nom] = $fila;
        }
        uksort($a_personas, 'src\shared\domain\helpers\strsinacentocmp');

        $personas = array_values($a_personas);

        $result = [
            'tabla' => $tabla,
            'obj_pau' => $obj_pau,
            'id_tabla' => $id_tabla,
            'permiso' => $permiso,
            'sPrefs' => $sPrefs,
            'total' => count($personas),
            'personas' => $personas,
        ];
        if ($problemasRegionStgr !== []) {
            $result['aviso'] = RegionStgrAviso::formatear($problemasRegionStgr);
        }

        return $result;
    }
}
