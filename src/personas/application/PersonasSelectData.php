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
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\RegionStgrAviso;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\value_objects\PauType;

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
    /**
     * @param array<string, mixed> $input habitualmente `$_POST`
     * @return array{
     *     error?: string,
     *     tabla?: string,
     *     obj_pau?: string,
     *     id_tabla?: string,
     *     permiso?: int,
     *     sPrefs?: string,
     *     aviso?: string,
     *     total?: int,
     *     personas?: list<array{
     *         id_nom: int,
     *         id_tabla: string,
     *         nom: string,
     *         nombre_ubi: string,
     *         nivel_stgr: string,
     *         situacion: string,
     *         f_situacion: string
     *     }>
     * }
     */
    public static function build(array $input): array
    {
        $tabla = (string)($input['tabla'] ?? '');
        $Qna = (string)($input['na'] ?? '');
        $tipo = (string)($input['tipo'] ?? '');
        $Qes_sacd = (int)($input['es_sacd'] ?? 0);
        $Qexacto = (string)($input['exacto'] ?? '');
        $Qcmb = (string)($input['cmb'] ?? '');
        $Qnombre = (string)($input['nombre'] ?? '');
        $Qapellido1 = (string)($input['apellido1'] ?? '');
        $Qapellido2 = (string)($input['apellido2'] ?? '');
        $Qcentro = (string)($input['centro'] ?? '');

        $aWhere = [];
        $aOperador = [];

        if (ConfigGlobal::mi_role_pau() === PauType::PAU_NOM) {
            $oMiUsuario = $_SESSION['session_auth']['MiUsuario'];
            $id_nom = $oMiUsuario->getCsv_id_pau();
            $aWhere = ['id_nom' => $id_nom];
            $PersonaDlrepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
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
            } elseif (!$_SESSION['oPerm']->have_perm_oficina('dtor')) {
                $aWhere['situacion'] = 'B';
                $aOperador['situacion'] = '!=';
            }
            if ($Qes_sacd === 1) {
                $aWhere['sacd'] = 't';
            }

            if (!empty($aWhereCtr)) {
                $gesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
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
                $cPersonas = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class)->getPersonas($aWhere, $aOperador);
                if ($_SESSION['oPerm']->have_perm_oficina('des')) $permiso = 3;
                break;
            case 'p_supernumerarios':
                $obj_pau = 'PersonaS';
                $cPersonas = $GLOBALS['container']->get(PersonaSRepositoryInterface::class)->getPersonas($aWhere, $aOperador);
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) $permiso = 3;
                break;
            case 'p_numerarios':
                $obj_pau = 'PersonaN';
                $cPersonas = $GLOBALS['container']->get(PersonaNRepositoryInterface::class)->getPersonas($aWhere, $aOperador);
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) $permiso = 3;
                break;
            case 'p_nax':
                $obj_pau = 'PersonaNax';
                $repoNax = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
                $cPersonas = $repoNax->getPersonas($aWhere, $aOperador);
                if ($cPersonas === false) $cPersonas = [];
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) $permiso = 3;
                break;
            case 'p_agregados':
                $obj_pau = 'PersonaAgd';
                $cPersonas = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class)->getPersonas($aWhere, $aOperador);
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) $permiso = 3;
                break;
            case 'p_de_paso':
            case 'p_de_paso_ex':
                if (!empty($Qna)) {
                    $aWhere['id_tabla'] = 'p' . $Qna;
                    $id_tabla = 'p' . $Qna;
                }
                $obj_pau = 'PersonaEx';
                $cPersonas = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class)
                    ->getPersonasParaListado($aWhere, $aOperador, $problemasRegionStgr, $sinRegionStgrPorIdNom);
                if (
                    $_SESSION['oPerm']->have_perm_oficina('sm')
                    || $_SESSION['oPerm']->have_perm_oficina('agd')
                    || $_SESSION['oPerm']->have_perm_oficina('des')
                    || $_SESSION['oPerm']->have_perm_oficina('sg')
                    || $_SESSION['oPerm']->have_perm_oficina('est')
                ) {
                    $permiso = 3;
                }
                break;
            case 'nada':
                return ['error' => _("No se encuentra ningún centro con esta condición")];
        }

        $sPrefs = '';
        $PreferenciaRepository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $oPreferencia = $PreferenciaRepository->findById((int)($_SESSION['session_auth']['id_usuario'] ?? 0), 'tabla_presentacion');
        if ($oPreferencia !== null) {
            $sPrefs = (string)$oPreferencia->getPreferencia();
        }

        $aNivelStgr = NivelStgrId::getArrayNivelStgr();
        $ambito = ConfigGlobal::mi_ambito();
        $centroRepoIface = $ambito === 'rstgr'
            ? CentroRepositoryInterface::class
            : CentroDlRepositoryInterface::class;

        $a_personas = [];
        if (is_iterable($cPersonas)) {
            foreach ($cPersonas as $oPersona) {
                $id_tabla_persona = (string)$oPersona->getId_tabla();
                $id_nom = (int)$oPersona->getId_nom();
                $nom = (string)$oPersona->getPrefApellidosNombre();

                $nombre_ubi = '';
                if ($obj_pau !== 'PersonaEx') {
                    $id_ctr = $oPersona->getId_ctr();
                    if ($id_ctr !== null) {
                        $oCentroDl = $GLOBALS['container']->get($centroRepoIface)->findById($id_ctr);
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
