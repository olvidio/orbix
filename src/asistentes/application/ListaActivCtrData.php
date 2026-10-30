<?php

namespace src\asistentes\application;

use frontend\shared\web\Periodo;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\asistentes\domain\criteria\AsistenteField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;

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
        $Qssfsv = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'ssfsv');

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

        $Qsasistentes = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sasistentes');
        $Qsactividad = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad');
        $Qn_agd = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'n_agd');
        $Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'year');
        $Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
        $Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');
        $Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');

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

        $criterio = Criteria::create();
        $tabla = 'p_n_agd';
        switch ($Qn_agd) {
            case 'a':
                $tabla = 'p_agregados';
                $criterio = $criterio->where(Filter::matches(CentroDlField::TipoCtr, '^a'));
                break;
            case 'n':
                $tabla = 'p_numerarios';
                $criterio = $criterio->where(Filter::matches(CentroDlField::TipoCtr, '^n'));
                break;
            case 'nm':
                $tabla = 'p_n_agd';
                $criterio = $criterio->where(Filter::matches(CentroDlField::TipoCtr, '^nm'));
                break;
            case 'nj':
                $tabla = 'p_n_agd';
                $criterio = $criterio->where(Filter::matches(CentroDlField::TipoCtr, '^nj(ce)*'));
                break;
            case 'sssc':
                $tabla = 'p_sssc';
                $criterio = $criterio->where(Filter::matches(CentroDlField::TipoCtr, '^ss'));
                break;
            case 'c':
                $tabla = 'p_n_agd';
                $criterio = $criterio->where(
                    Filter::eq(CentroDlField::IdUbi, \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi'))
                );
                break;
            default:
                $tabla = 'p_n_agd';
        }
        $criterio = $criterio->andWhere(Filter::eq(CentroDlField::Active, true))->orderBy(CentroDlField::NombreUbi);

        $cCentros = $this->centroDlRepository->matching($criterio);

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
            $aPlazas = PlazaId::getArrayPosiblesPlazas();
            foreach ($cPersonas as $oPersona) {
                $i++;
                $id_nom = $oPersona->getId_nom();
                $ap_nom = $oPersona->getPrefApellidosNombre();
                $criteriaAsistente = Criteria::create()
                    ->where(Filter::eq(AsistenteField::IdNom, $id_nom))
                    ->andWhere(Filter::eq(AsistenteField::Propio, true));
                $aWhereAct['f_ini'] = "'$inicioIso','$finIso'";
                $aOperadorAct['f_ini'] = 'BETWEEN';

                $cAsistencias = $this->asistenteActividadService->getActividadesDeAsistente($criteriaAsistente, $aWhereAct, $aOperadorAct);
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
