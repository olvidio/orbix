<?php

namespace src\asistentes\application;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use frontend\shared\web\Periodo;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Asistentes a actividades por centro (`lista_activ_ctr.php`).
 *
 * @return array{aCentros: array<int|string, array{nombre_ubi: string, personas: array<int, array{ap_nom: string, actividades: list<string>}>}>}
 */
final class ListaActivCtrData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private PersonaSSSCRepositoryInterface $personaSSSCRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private AsistenteActividadService $asistenteActividadService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{aCentros: array<int|string, array{nombre_ubi: string, personas: array<int, array{ap_nom: string, actividades: list<string>}>}>}
     */
    public function build(array $input): array
    {
        $Qssfsv = input_string($input, 'ssfsv');

        if (ConfigGlobal::mi_sfsv() === 1) {
            /** @var XPermisos $oPerm */
            $oPerm = $_SESSION['oPerm'];
            if ($Qssfsv === 'sf'
                && ($oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des'))) {
                $ssfsv = 'sf';
            } else {
                $ssfsv = 'sv';
            }
        }
        if (ConfigGlobal::mi_sfsv() === 2) {
            $ssfsv = 'sf';
        }
        if (!isset($ssfsv)) {
            $ssfsv = 'sv';
        }

        $Qsasistentes = input_string($input, 'sasistentes');
        $Qsactividad = input_string($input, 'sactividad');
        $Qn_agd = input_string($input, 'n_agd');
        $Qyear = input_int($input, 'year');
        $Qperiodo = input_string($input, 'periodo');
        $Qempiezamin = input_string($input, 'empiezamin');
        $Qempiezamax = input_string($input, 'empiezamax');

        if ($Qn_agd === 'sss') {
            $Qsasistentes = 'sss+';
        }

        $oTipoActiv = new \src\actividades\domain\entity\TiposActividades();
        $oTipoActiv->setSfsvText($ssfsv);
        $oTipoActiv->setAsistentesText($Qsasistentes);
        $oTipoActiv->setActividadText($Qsactividad);
        $condta = $oTipoActiv->getId_tipo_activ();

        $condta_plus = '';
        if ($Qsasistentes === 'n' && ($Qsactividad === 'ca' || $Qsactividad === 'crt')) {
            $activ = $Qsactividad === 'ca' ? 'cv' : $Qsactividad;
            $oTipoActiv = new \src\actividades\domain\entity\TiposActividades();
            $oTipoActiv->setSfsvText($ssfsv);
            $oTipoActiv->setAsistentesText('agd');
            $oTipoActiv->setActividadText($activ);
            $condta_plus = $oTipoActiv->getId_tipo_activ();
        }

        $condta_sr = '';
        if ($Qsactividad === 'crt') {
            $oTipoActiv = new \src\actividades\domain\entity\TiposActividades();
            $oTipoActiv->setSfsvText($ssfsv);
            $oTipoActiv->setAsistentesText('sr');
            $oTipoActiv->setActividadText('crt');
            $condta_sr = $oTipoActiv->getId_tipo_activ();
        }

        $condicion = '';
        $condicion .= $condta === '' ? '' : '^' . $condta;
        $condicion .= $condta_plus === '' ? '' : '|^' . $condta_plus;
        $condicion .= $condta_sr === '' ? '' : '|^' . $condta_sr;

        $aWhereAct = [];
        $aOperadorAct = [];
        $aWhereAct['id_tipo_activ'] = $condicion;
        $aOperadorAct['id_tipo_activ'] = '~';

        $oPeriodo = Periodo::conCalendarioDesdeBackend();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $aWhere = [];
        $aOperador = [];
        $tabla = 'p_n_agd';
        switch ($Qn_agd) {
            case 'a':
                $tabla = 'p_agregados';
                $aWhere['tipo_ctr'] = '^a';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'n':
                $tabla = 'p_numerarios';
                $aWhere['tipo_ctr'] = '^n';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'nm':
                $tabla = 'p_n_agd';
                $aWhere['tipo_ctr'] = '^nm';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'nj':
                $tabla = 'p_n_agd';
                $aWhere['tipo_ctr'] = '^nj(ce)*';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'sssc':
                $tabla = 'p_sssc';
                $aWhere['tipo_ctr'] = '^ss';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'c':
                $tabla = 'p_n_agd';
                $aWhere['id_ubi'] = input_int($input, 'id_ubi');
                break;
            default:
                $tabla = 'p_n_agd';
        }
        $aWhere['active'] = 't';
        $aWhere['_ordre'] = 'nombre_ubi';

        $cCentros = $this->centroDlRepository->getCentros($aWhere, $aOperador);

        $aCentros = [];
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            if ($tabla === 'p_sssc') {
                $cPersonas = $this->personaSSSCRepository->getPersonas(['id_ctr' => $id_ubi, 'situacion' => 'A', '_ordre' => 'apellido1']);
            } else {
                $cPersonas = $this->personaDlRepository->getPersonas(['id_ctr' => $id_ubi, 'situacion' => 'A', '_ordre' => 'apellido1,apellido2,nom']);
            }

            $aCentros[$id_ubi]['nombre_ubi'] = $nombre_ubi;

            $i = 0;
            $aPersonasCtr = [];
            $aWhereNom = [];
            $aPlazas = PlazaId::getArrayPosiblesPlazas();
            foreach ($cPersonas as $oPersona) {
                $i++;
                $id_nom = $oPersona->getId_nom();
                $ap_nom = $oPersona->getPrefApellidosNombre();
                $aWhereNom['id_nom'] = $id_nom;
                $aWhereNom['propio'] = 't';
                $aOperadorNom = [];
                $aWhereAct['f_ini'] = "'$inicioIso','$finIso'";
                $aOperadorAct['f_ini'] = 'BETWEEN';

                $cAsistencias = $this->asistenteActividadService->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhereAct, $aOperadorAct);
                $aActividades = [];
                if (count($cAsistencias) === 0) {
                    $aActividades = [];
                } else {
                    foreach ($cAsistencias as $oAsistente) {
                        $id_activ = $oAsistente->getId_activ();
                        $oActividad = $this->actividadAllRepository->findById($id_activ);
                        if ($oActividad === null) {
                            continue;
                        }
                        $nom_activ = $oActividad->getNom_activ();
                        $plaza = $oAsistente->getPlazaVo()?->value() ?? '';
                        $nom_plaza = '';
                        if ($plaza < PlazaId::ASIGNADA) {
                            $plaza_txt = $aPlazas[$plaza] ?? '';
                            $nom_plaza = '<span class=alert> [' . $plaza_txt . ']</span>';
                        }
                        $aActividades[] = $nom_activ . $nom_plaza;
                    }
                }
                $aPersonasCtr[$i]['ap_nom'] = $ap_nom;
                $aPersonasCtr[$i]['actividades'] = $aActividades;
            }
            $aCentros[$id_ubi]['personas'] = $aPersonasCtr;
        }

        return ['aCentros' => $aCentros];
    }
}
