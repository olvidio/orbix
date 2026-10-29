<?php

namespace src\asistentes\application;

use src\asistentes\domain\criteria\AsistenteField;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use frontend\shared\web\Lista;
use frontend\shared\web\Periodo;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;

/**
 * Listado estudios por centro (`lista_est_ctr.php`).
 */
final class ListaEstCtrData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private MatriculaRepositoryInterface $matriculaRepository,
        private AsistenteActividadService $asistenteActividadService,
        private AsistenteRepositoryInterface $asistenteRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{lista_html: string}
     */
    public function build(array $input): array
    {
        $oHoy = new \src\shared\domain\value_objects\DateTimeLocal();

        $Qn_agd = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'n_agd');
        $Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi', 0);
        $Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
        $Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'year');
        $Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');
        $Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');

        $oPeriodo = Periodo::conCalendarioDesdeBackend();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $aWhereNom['propio'] = 't';
        $aWhereAct['f_ini'] = "'$inicioIso','$finIso'";
        $aOperadorAct['f_ini'] = 'BETWEEN';
        $aWhereAct['id_tipo_activ'] = '^1(12|33)';
        $aOperadorAct['id_tipo_activ'] = '~';

        $criterioCtr = Criteria::create();
        switch ($Qn_agd) {
            case 'a':
                $tabla = 'p_agregados';
                $criterioCtr = $criterioCtr->where(Filter::matches(CentroDlField::TipoCtr, '^a'));
                break;
            case 'n':
                $tabla = 'p_numerarios';
                $criterioCtr = $criterioCtr->where(Filter::matches(CentroDlField::TipoCtr, '^n'));
                break;
            case 'nm':
                $tabla = 'p_n_agd';
                $criterioCtr = $criterioCtr->where(Filter::matches(CentroDlField::TipoCtr, '^nm'));
                break;
            case 'nj':
                $tabla = 'p_n_agd';
                $criterioCtr = $criterioCtr->where(Filter::matches(CentroDlField::TipoCtr, '^nj(ce)*'));
                break;
            case 'sssc':
                $tabla = 'p_n_agd';
                // Igualdad literal con '^ss' (nunca coincide): se conserva el comportamiento previo.
                $criterioCtr = $criterioCtr->where(Filter::eq(CentroDlField::TipoCtr, '^ss'));
                break;
            case 'c':
                $tabla = 'p_n_agd';
                $criterioCtr = $criterioCtr->where(Filter::eq(CentroDlField::IdUbi, $Qid_ubi));
                break;
            default:
                $tabla = 'p_n_agd';
        }

        $cCentros = $this->centroDlRepository->matching($criterioCtr);
        $a_valores = [];
        $aGrupos = [];
        foreach ($cCentros as $oCentroDl) {
            $id_ubi = $oCentroDl->getId_ubi();
            $aGrupos[$id_ubi] = $oCentroDl->getNombre_ubi();

            $aWhere = [];
            $aOperador = [];
            $aWhere['situacion'] = 'A';
            $aWhere['id_ctr'] = $id_ubi;
            $aWhere['_ordre'] = 'apellido1,apellido2,nom';
            $tipo_ctr = $oCentroDl->getTipo_ctr() ?? '';
            if (strpos($tipo_ctr, 'n') === 0) {
                $aWhere['sacd'] = 'f';
                $aWhere['nivel_stgr'] = NivelStgrId::N;
                $aOperador['nivel_stgr'] = '!=';
            }

            $cPersonas = $this->personaDlRepository->getPersonas($aWhere, $aOperador);
            $i = 0;
            foreach ($cPersonas as $oPersonaDl) {
                $i++;
                $id_nom = $oPersonaDl->getId_nom();
                $nom = $oPersonaDl->getPrefApellidosNombre();
                $nivelVal = $oPersonaDl->getNivelStgrVo()?->value();
                $a_valores[$id_ubi][$i][1] = $i;
                $a_valores[$id_ubi][$i][2] = $nom;

                $aWhereNom['id_nom'] = $id_nom;
                $aOperadorNom = [];
                $cAsistentes = $this->asistenteActividadService->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhereAct, $aOperadorAct);
                $a = 0;
                foreach ($cAsistentes as $oAsistente) {
                    $a++;
                    $id_activ = $oAsistente->getId_activ();
                    $oActividad = $this->actividadAllRepository->findById($id_activ);
                    if ($oActividad === null) {
                        continue;
                    }
                    $nom_activ = $oActividad->getNom_activ();

                    $oF_ini = $oActividad->getF_ini();
                    if ($oF_ini < $oHoy) {
                        $nom_activ = _("ya lo ha hecho");
                        $asignaturas = '';
                    } else {
                        switch ($nivelVal) {
                            case NivelStgrId::N:
                                $asignaturas = _("plan de formación");
                                break;
                            case NivelStgrId::R:
                                $asignaturas = _("repaso");
                                break;
                            default:
                                $asignaturas = '';
                                $cMatriculas = $this->matriculaRepository->getMatriculas(['id_nom' => $id_nom, 'id_activ' => $id_activ]);
                                foreach ($cMatriculas as $oMatricula) {
                                    $id_asignatura = $oMatricula->getId_asignatura();
                                    $preceptor = $oMatricula->isPreceptor();
                                    $id_preceptor = $oMatricula->getId_preceptor();
                                    $oAsignatura = $this->asignaturaRepository->findById($id_asignatura);
                                    if ($oAsignatura === null) {
                                        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
                                    }
                                    $nombre_corto = $oAsignatura->getNombre_corto();
                                    $creditos = $oAsignatura->getCreditos();
                                    if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($preceptor)) {
                                        if (!empty($id_preceptor)) {
                                            $oPersona = Persona::findPersonaEnGlobal($id_preceptor);
                                            if ($oPersona === null) {
                                                $preceptor = '(p)';
                                            } else {
                                            $cAsistentesP = $this->asistenteRepository->matching(Criteria::create()
                                                ->where(Filter::eq(AsistenteField::IdActiv, $id_activ))
                                                ->andWhere(Filter::eq(AsistenteField::IdNom, $id_nom)));
                                            $p = count($cAsistentesP) > 0 ? '*' : '';
                                            $preceptor = '(p: ' . $oPersona->getPrefApellidosNombre() . ')' . $p;
                                            }
                                        } else {
                                            $preceptor = '(p)';
                                        }
                                    } else {
                                        $preceptor = '';
                                    }
                                    $asignaturas .= "$nombre_corto ($creditos " . _("créditos") . ')' . $preceptor . '<br>';
                                }
                        }
                    }
                    $a_valores[$id_ubi][$i][3] = $nom_activ;
                    $a_valores[$id_ubi][$i][4] = $asignaturas;
                }
            }
        }

        $a_cabeceras = [];
        $a_cabeceras[] = _("nº");
        $a_cabeceras[] = _("nombre");
        $a_cabeceras[] = _("actividad");
        $a_cabeceras[] = _("asignaturas");

        asort($aGrupos);

        $oLista = new Lista();
        $oLista->setGrupos($aGrupos);
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        $oLista->setPie(_("(*) El preceptor no asiste al ca"));

        return ['lista_html' => $oLista->listaPaginada()];
    }
}
