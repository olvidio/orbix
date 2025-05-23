<?php

namespace asistentes\model;

use actividadcargos\model\entity\Cargo;
use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\GestorActividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadplazas\model\GestorResumenPlazas;
use asistentes\model\entity\Asistente;
use asistentes\model\entity\GestorAsistente;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use ubis\model\entity\Casa;
use ubis\model\entity\Ubi;
use web\Lista;
use web\TiposActividades;
use function core\strtoupper_dlb;

/**
 * Lista los asistentes de una relación de actividades seleccionada
 *
 *
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
class ListaPlazas
{
    public function getMi_dele()
    {
        return $this->smi_dele;
    }

    public function getWhere()
    {
        return $this->aWhere;
    }

    public function getOperador()
    {
        return $this->aOperador;
    }

    public function setMi_dele($smi_dele)
    {
        $this->smi_dele = $smi_dele;
    }

    public function setWhere($aWhere)
    {
        $this->aWhere = $aWhere;
    }

    public function setSacd($sacd)
    {
        $this->bsacd = $sacd;
    }

    public function setOperador($aOperador)
    {
        $this->aOperador = $aOperador;
    }

    public function getId_tipo_activ()
    {
        return $this->iid_tipo_activ;
    }

    public function setId_tipo_activ($iid_tipo_activ)
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    private $bsacd = FALSE;
    private $smi_dele;
    private $aWhere;
    private $aOperador;
    private $iid_tipo_activ;

    public function getLista()
    {

        $oTipoActiv = new TiposActividades($this->iid_tipo_activ);
        $sasistentes = $oTipoActiv->getAsistentesText();
        $sactividad = $oTipoActiv->getActividadText();

        $GesActividades = new GestorActividad();
        $cActividades = $GesActividades->getActividades($this->aWhere, $this->aOperador);

        if (is_array($cActividades) && count($cActividades) < 1) {
            echo strtoupper_dlb(_("no existe ninguna actividad con esta condición"));
            // retorno una lista vacia para que no dé errores.
            $oLista = new Lista();
            $oLista->setGrupos(array());
            return $oLista;
        }

        if (($sasistentes == "s") and ($sactividad == "cv")) {
            $titulo = strtoupper_dlb(_("relación de cargos en las cv de s"));
        } else {
            if (($sasistentes == "sss+") and ($sactividad == "cv")) {
                $titulo = strtoupper(_("propuesta de cl en cv de sss+"));
            } else {
                $titulo = strtoupper_dlb(_("relación de asistentes a las actividades seleccionadas"));
            }
        }

        $k = 0;
        $centros = '';
        $aGrupos = [];
        $a_activ = [];
        $msg_err = '';
        $gesActividadPlazas = new GestorResumenPlazas();
        foreach ($cActividades as $oActividad) {
            $k++;  // recorro todas las actividades seleccionadas, utilizo el contador k
            $id_activ = $oActividad->getId_activ();
            $nom_activ = $oActividad->getNom_activ();
            $observ = $oActividad->getObserv();
            $dl_org_activ = $oActividad->getDl_org();
            $id_ubi_casa = $oActividad->getId_ubi();
            $plazas = $oActividad->getPlazas();
            $publicado = $oActividad->getPublicado();

            // Plazas
            $gesActividadPlazas->setId_activ($id_activ);
            $a_plazas = $gesActividadPlazas->getResumen();
            $plazas_max = '';
            $plazas_min = '';
            $plazas_casa = '';
            if (!empty($id_ubi_casa)) {
                $oCasaDl = new Casa($id_ubi_casa);
                $plazas_max = !empty($plazas) ? $plazas : $oCasaDl->getPlazas();
                $plazas_min = $oCasaDl->getPlazas_min();
                $plazas_casa .= !empty($plazas_max) ? $plazas_max : '';
                $plazas_casa .= !empty($plazas_min) ? ' - ' . $plazas_min : '';
            }

            $id_pau = $id_activ;
            $txt_ctr = '';
            if (ConfigGlobal::is_app_installed('actividadcentros')) {
                if ((($sasistentes == "s") || ($sasistentes == "sss+")) and ($sactividad == "cv")) {
                    // para las cv de s y de sss+ consulto los ctr que organizan
                    $oGesEncargados = new GestorCentroEncargado();
                    $cCtrsEncargados = $oGesEncargados->getCentrosEncargados(array('id_activ' => $id_activ, '_ordre' => 'num_orden'));

                    $c = 0;
                    foreach ($cCtrsEncargados as $oCentroEncargado) {
                        $c++;
                        $num_orden = $oCentroEncargado->getNum_orden();
                        $id_ubi = $oCentroEncargado->getId_ubi();
                        $Centro = Ubi::NewUbi($id_ubi);
                        $ctr = $Centro->getNombre_ubi();
                        if ($c > 1) $txt_ctr .= '; ';
                        $txt_ctr .= $ctr;
                    }
                    //$a_activ[$id_activ]['ctr_encargados']=$txt_ctr;
                }
            }
            $nom_activ = empty($txt_ctr) ? $nom_activ : "$nom_activ [$txt_ctr]";
            $nom_activ = empty($observ) ? $nom_activ : "$nom_activ $observ";

            if (!($sasistentes == "sss+" and $sactividad == "cv")) {
                if (ConfigGlobal::is_app_installed('actividadcargos')) {
                    //selecciono el cl
                    $oGesActividadCargos = new GestorActividadCargo();
                    $cActividadCargos = $oGesActividadCargos->getActividadCargos(array('id_activ' => $id_pau));
                    $cl = 0;
                    $num = 0; //número total de asistentes
                    $plazas_pedidas = 0; // plazas pedidas o 'en espera'
                    $aIdCargos = []; // id_nom de los cargos para no ponerlos como asistentes.
                    foreach ($cActividadCargos as $oActividadCargo) {
                        $id_nom = $oActividadCargo->getId_nom();
                        $aIdCargos[] = $id_nom;
                        $id_cargo = $oActividadCargo->getId_cargo();
                        $oCargo = new Cargo($id_cargo);
                        $cargo_cl = $oCargo->getCargo();
                        $oPersona = Persona::NewPersona($id_nom);
                        if (!is_object($oPersona)) {
                            $msg_err .= "<br>$oPersona con id_nom: $id_nom para la actividad $nom_activ";
                            $msg_err .= "<br>en  " . __FILE__ . ": line " . __LINE__;
                            continue;
                        }
                        $sacd = $oPersona->getSacd();
                        if ($this->bsacd && $sacd != 't') continue;

                        $cl++;
                        $num++;
                        $id_tabla = $oPersona->getId_tabla();
                        $ap_nom = $oPersona->getPrefApellidosNombre();
                        $ctr_dl = $oPersona->getCentro_o_dl();

                        // ahora miro si también asiste:
                        $oGesAsistentes = new GestorAsistente();
                        $cAsistentes = $oGesAsistentes->getAsistentes(array('id_activ' => $id_pau, 'id_nom' => $id_nom));

                        if (is_array($cAsistentes) && count($cAsistentes) > 0) {
                            $asis = "t";
                            $texto = "";
                        } else {
                            $texto = "No asiste";
                            $asis = "f";
                        }
                        $a_activ[$id_activ][$num]['cargo'] = $cargo_cl;
                        $a_activ[$id_activ][$num]['ap_nom'] = "$ap_nom ($ctr_dl)";
                        $a_activ[$id_activ][$num]['texto'] = $texto;
                    }
                }

                $oGesAsistentes = new GestorAsistente();
                $cAsistentes = $oGesAsistentes->getAsistentesDeActividad($id_pau);
                foreach ($cAsistentes as $oAsistente) {
                    $id_nom = $oAsistente->getId_nom();
                    if (in_array($id_nom, $aIdCargos)) continue; // si ya está como cargo, no lo pongo.
                    $oPersona = Persona::NewPersona($id_nom);
                    if (!is_object($oPersona)) {
                        $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                        continue;
                    }
                    $sacd = $oPersona->getSacd();
                    if ($this->bsacd && $sacd != 't') continue;

                    $id_tabla = $oPersona->getId_tabla();
                    $ap_nom = $oPersona->getPrefApellidosNombre();
                    $ctr_dl = $oPersona->getCentro_o_dl();
                    $dl = $oPersona->getDl();

                    // Si no organizo, no veo a los que no son de la dl.
                    if ($dl_org_activ != $this->smi_dele) {
                        if ($dl != $this->smi_dele) {
                            continue;
                        }
                    }

                    if (ConfigGlobal::is_app_installed('actividadplazas')) {
                        // pedidas y 'en espera'
                        if ($oAsistente->getPlaza() < Asistente::PLAZA_ASIGNADA) {
                            // Sólo los de mi dl
                            if ($dl != $this->smi_dele) {
                                continue;
                            }
                            $plazas_pedidas++;
                            $key = $num + $plazas + $plazas_pedidas; // al final de la lista
                            $a_activ[$id_activ][$key]['cargo'] = '';
                            $a_activ[$id_activ][$key]['ap_nom'] = "<span class=\"alert\">$ap_nom ($ctr_dl)</span>";
                        } else { // asignados y confirmados
                            $num++;
                            $a_activ[$id_activ][$num]['cargo'] = $num;
                            $a_activ[$id_activ][$num]['ap_nom'] = "$ap_nom ($ctr_dl)";
                        }
                    } else {
                        $num++;
                        $a_activ[$id_activ][$num]['cargo'] = $num;
                        $a_activ[$id_activ][$num]['ap_nom'] = "$ap_nom ($ctr_dl)";
                    }
                }
            }
            if (!empty($plazas_max) && $num > $plazas_max) {
                $num_txt = "<span class=\"alert\">$num</span>";
            } else {
                $num_txt = $num;
            }
            // ordenar por indice asistencia ($key) para que ponga los pedidos al final
            // en el caso de que existan asistentes...
            if (!empty($num)) ksort($a_activ[$id_activ]);

            // Puede estar vacío
            $pl_calendario = empty($a_plazas[$this->smi_dele]['calendario']) ? 0 : $a_plazas[$this->smi_dele]['calendario'];
            $pl_cedidas = empty($a_plazas[$this->smi_dele]['total_cedidas']) ? 0 : $a_plazas[$this->smi_dele]['total_cedidas'];
            $pl_conseguidas = empty($a_plazas[$this->smi_dele]['total_conseguidas']) ? 0 : $a_plazas[$this->smi_dele]['total_conseguidas'];
            $pl_disponibles = empty($a_plazas[$this->smi_dele]['total_disponibles']) ? 0 : $a_plazas[$this->smi_dele]['total_disponibles'];
            $pl_ocupadas = empty($a_plazas[$this->smi_dele]['total_ocupadas']) ? 0 : $a_plazas[$this->smi_dele]['total_ocupadas'];
            /*
            A) Plazas totales:  XX;
            B) Plazas disponibles para la dl;
            C) Plazas ocupadas + pedidas;
            Resultado de  B)-C) (si este valor es negativo que salga en ROJO)
            */
            $pl_ocupadas_pedidas = $pl_ocupadas + $plazas_pedidas;
            $pl_dif = $pl_disponibles - $pl_ocupadas_pedidas;
            if (!empty($pl_dif) && $pl_dif < 0) {
                $pl_dif_txt = "<span class=\"alert\">$pl_dif</span>";
            } else {
                $pl_dif_txt = $pl_dif;
            }

            if ($publicado === true) {
                $plazas_txt = sprintf(_("plazas (max-min): %s, para la dl: %s, ocupadas + pedidas: %s"), $plazas_casa, $pl_disponibles, $pl_ocupadas_pedidas);
                // Nombre actividad y plazas:
                $aGrupos[$id_activ] = $nom_activ;
                //Si es sólo los sacd no tiene sentido el resumen de plazas.
                if (!$this->bsacd) {
                    $aGrupos[$id_activ] .= '<br>' . " $plazas_txt, " . _("dif") . ": $pl_dif_txt";
                }
            } else {
                $plazas_txt = sprintf(_("plazas (max-min): %s, ocupadas: %s"), $plazas_casa, $num_txt);
                $aGrupos[$id_activ] = $nom_activ;
                //Si es sólo los sacd no tiene sentido el resumen de plazas.
                if (!$this->bsacd) {
                    $aGrupos[$id_activ] .= '<br>' . " $plazas_txt";
                }
            }
        }

        $a_cabeceras[] = _("num");
        $a_cabeceras[] = _("nombre");

        if (!empty($msg_err)) {
            echo $msg_err;
        }

        $oLista = new Lista();
        $oLista->setGrupos($aGrupos);
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_activ);

        return $oLista;
    }
}
