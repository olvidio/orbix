<?php

namespace src\asistentes\application;

use DateTime;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaPub;
use src\shared\config\ConfigGlobal;

/**
 * Listado de asistentes a una actividad (`lista_asistentes.php`).
 */
final readonly class ListaAsistentesData
{
    public function __construct(
        private ActividadAllRepositoryInterface   $actividadAllRepository,
        private AsistenteRepositoryInterface      $asistenteRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private CargoRepositoryInterface          $cargoRepository,
    )
    {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{nom_activ: string, queSel: string, aAsistentes: array<string, array{nombre: string, a_datos_cl: array<string, string>}>}
     */
    public function build(array $input): array
    {
        $a_sel = (array)($input['sel'] ?? []);
        if ($a_sel !== []) {
            $sel0 = $a_sel[0];
            $selKey = is_string($sel0) ? $sel0 : (is_scalar($sel0) ? (string)$sel0 : '');
            $id_pau = (int)strtok($selKey, '#');
            $nomPart = strtok('#');
            $nom_activ = is_string($nomPart) ? $nomPart : '';
        } else {
            $id_pau = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau', 0);
            $oActividad = $this->actividadAllRepository->findById($id_pau);
            $nom_activ = $oActividad !== null ? $oActividad->getNom_activ() : '';
        }

        $queSel = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'queSel');

        $c = 0;
        $num = 0;
        $a_valores = [];
        $aListaCargos = [];
        $msg_err = '';

        if (ConfigGlobal::is_app_installed('actividadcargos')) {
            $cCargosEnActividad = $this->actividadCargoRepository->getActividadCargos(['id_activ' => $id_pau]);
            $mi_sfsv = ConfigGlobal::mi_sfsv();
            foreach ($cCargosEnActividad as $oActividadCargo) {
                $c++;
                $num++;
                $id_nom = $oActividadCargo->getId_nom();
                if ($id_nom === null) {
                    continue;
                }
                $aListaCargos[] = $id_nom;
                $id_cargo = $oActividadCargo->getId_cargo();
                $oCargo = $this->cargoRepository->findById($id_cargo);
                if ($oCargo === null) {
                    continue;
                }
                $tipo_cargo = $oCargo->getTipoCargoVo()?->value();
                $cargo = $oCargo->getCargoVo()->value();
                if ($tipo_cargo === 'sacd' && $mi_sfsv == 2) {
                    continue;
                }

                $oPersona = Persona::findPersonaEnGlobal($id_nom);
                if ($oPersona === null) {
                    $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ': line ' . __LINE__;
                    continue;
                }
                $nom = $oPersona->getPrefApellidosNombre();

                $observ_c = $oActividadCargo->getObserv();
                $ctr_dl = $oPersona->getCentro_o_dl();

                $aWhere = ['id_activ' => $id_pau, 'id_nom' => $id_nom];
                $aOperador = [];
                if ($id_nom !== 0 && ($cAsistente = $this->asistenteRepository->getAsistentes($aWhere, $aOperador)) !== []) {
                    if (count($cAsistente) > 1) {
                        $tabla = '';
                        foreach ($cAsistente as $Asistente) {
                            $tabla .= '<li>' . $Asistente->getNomTabla() . '</li>';
                        }
                        $msg_err .= 'ERROR: más de un asistente con el mismo id_nom<br>';
                        $msg_err .= "<br>$nom(" . $oPersona->getId_tabla() . ")<br><br>En las tablas:<ul>$tabla</ul>";
                        exit("$msg_err");
                    }
                    $propio = $cAsistente[0]->isPropio();
                    $falta = $cAsistente[0]->isFalta();
                    $est_ok = $cAsistente[0]->isEst_ok();
                    $observ = $cAsistente[0]->getObserv();

                    if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($propio)) {
                        $chk_propio = _('sí');
                    } else {
                        $chk_propio = _('no');
                    }
                    \src\shared\domain\helpers\FuncTablasSupport::isTrue($falta) ? $chk_falta = _('sí') : $chk_falta = _('no');
                    \src\shared\domain\helpers\FuncTablasSupport::isTrue($est_ok) ? $chk_est_ok = _('sí') : $chk_est_ok = _('no');
                    $asis = 't';
                    $a_valores[$c][3] = $chk_propio;
                    $a_valores[$c][4] = $chk_est_ok;
                    $a_valores[$c][5] = $chk_falta;
                } else {
                    $a_valores[$c][3] = ['span' => 3, 'valor' => _('no asiste')];
                    $observ = '';
                    $num--;
                    $asis = 'f';
                }

                $a_valores[$c][1] = $cargo;
                $a_valores[$c][2] = "$nom  ($ctr_dl)";
                $a_valores[$c][6] = "$observ_c $observ";
                $a_valores[$c][7] = $oPersona;
            }
        }

        $asistentes = [];
        $msg_err = '';
        foreach ($this->asistenteRepository->getAsistentes(['id_activ' => $id_pau]) as $oAsistente) {
            $c++;
            $num++;
            $id_nom = $oAsistente->getId_nom();
            if (in_array($id_nom, $aListaCargos)) {
                $num--;
                continue;
            }

            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ': line ' . __LINE__;
                continue;
            }
            $nom = $oPersona->getPrefApellidosNombre();
            $ctr_dl = $oPersona->getCentro_o_dl();

            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();

            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $plaza = $oAsistente->getPlaza();
                if ($plaza < 4) {
                    continue;
                }
            }
            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($propio)) {
                $chk_propio = _('sí');
            } else {
                $chk_propio = _('no');
            }
            \src\shared\domain\helpers\FuncTablasSupport::isTrue($falta) ? $chk_falta = _('sí') : $chk_falta = _('no');
            \src\shared\domain\helpers\FuncTablasSupport::isTrue($est_ok) ? $chk_est_ok = _('sí') : $chk_est_ok = _('no');

            $a_val[2] = "$nom  ($ctr_dl)";
            $a_val[3] = $chk_propio;
            $a_val[4] = $chk_est_ok;
            $a_val[5] = $chk_falta;
            $a_val[6] = $observ;
            $a_val[7] = $oPersona;
            $asistentes[$nom] = $a_val;
        }
        uksort($asistentes, [\src\shared\domain\helpers\FuncTablasSupport::class, 'strsinacentocmp']);

        $c = 0;
        if (ConfigGlobal::is_app_installed('actividadcargos')) {
            $c = count($a_valores);
        }

        foreach ($asistentes as $nom => $val) {
            $c++;
            $val[1] = "$c.-";
            $a_valores[$c] = $val;
        }

        $aAsistentes = [];
        foreach ($a_valores as $k => $val) {
            $c = $val[1];
            $oPersona = $val[7];
            $a_datos_cl = [];
            if ($queSel === 'listcl') {
                $a_datos_cl = $this->datosPersona($oPersona);
                $a_datos_cl['observ'] = (string)($a_valores[$k][6] ?? '');
            }

            $aAsistentes[$c] = [
                'nombre' => $val[2],
                'a_datos_cl' => $a_datos_cl,
            ];
        }

        return [
            'nom_activ' => $nom_activ,
            'queSel' => $queSel,
            'aAsistentes' => $aAsistentes,
        ];
    }

    /**
     * @return array{estudios: string, profesion: string, edad: string, inc_f_inc: string, eap: string, observ: string|null}
     */
    private function datosPersona(PersonaDl|PersonaPub $oPersona): array
    {
        $estudios = '';
        $profesion = '';
        $edad = '';
        $inc_f_inc = '';
        $eap = '';
        $observ = '';

        if ($oPersona instanceof PersonaPub) {
            $profesion = $oPersona->getProfesion() ?? '';
            // si no es de paso si tiene f_nacimiento:
            $oF_nacimiento = $oPersona->getF_nacimiento();
            if ($oF_nacimiento !== null) {
                $edad = (string)$oF_nacimiento->diff(new DateTime())->y;
            } else {
                $edad = (string)($oPersona->getEdad() ?? '');
            }
            $inc = $oPersona->getInc();
            $f_inc = $oPersona->getF_inc()?->getFromLocal();
            if (!empty($inc)) {
                $inc_f_inc = $inc . ' : ' . $f_inc;
            }
            $eap = $oPersona->getEap() ?? '';
        } else {
            $profesion = $oPersona->getProfesion() ?? '';
            $oF_nacimiento = $oPersona->getF_nacimiento();
            $edad = $oF_nacimiento !== null ? (string)$oF_nacimiento->diff(new DateTime())->y : '';
            $inc = $oPersona->getInc();
            if (empty($inc) || $inc === '?') {
                $f_inc = '?';
            } else {
                $f_inc = $oPersona->getF_inc()?->getFromLocal() ?? '?';
            }
            if (!empty($inc)) {
                $inc_f_inc = $inc . ' : ' . $f_inc;
            }
            $eap = empty($oPersona->getEap()) ? '?' : $oPersona->getEap();
        }

        return [
            'estudios' => $estudios,
            'profesion' => $profesion,
            'edad' => (string)$edad,
            'inc_f_inc' => $inc_f_inc,
            'eap' => $eap,
            'observ' => (string)$observ,
        ];
    }
}
