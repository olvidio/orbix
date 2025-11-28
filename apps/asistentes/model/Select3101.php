<?php

namespace asistentes\model;

use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\ActividadAll;
use actividadplazas\model\GestorResumenPlazas;
use asistentes\model\entity\Asistente;
use asistentes\model\entity\GestorAsistente;
use core\ConfigGlobal;
use core\ViewPhtml;
use dossiers\model\PermDossier;
use personas\model\entity\Persona;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use web\Hash;
use web\Lista;
use function core\is_true;

/**
 * Esta página muestra una tabla con los asistentes de una actividad.
 * Primero los miembros del cl y después el resto.
 *  Con los botones de:
 *            modificar y borrar asistencia.
 *            añadir, modificar y quitar cargo.
 *            plan de estudios
 *            transferir a históricos.
 *  En el caso de ser "des" o "vcsd" al quitar cargo, también elimino la asistencia.
 * abajo se añaden los botones para añadir una nueva persona.
 *
 * OJO Está como include de dossiers_ver.php
 *
 * @param integer $_POST ['obj_pau']  Se pasa a otras páginas.
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @ajax        23/8/2007.
 * @version 1.0
 * @created 23/09/2010
 *
 * @package    delegacion
 */
class Select3101
{

    // ------ de uso interno

    /**
     * array con los permisos (si o no) para añadir las personas (agd, n...)
     * según el tipo de actividad de que se trate y quién seamos nosotros.
     *
     * @var array $ref_perm
     */
    private $a_ref_perm;
    /* @var $msg_err string */
    private $msg_err;
    /* @var $a_valores array */
    private $a_valores;

    private $a_asistentes;
    private $mi_dele;
    private $plazas_txt;
    private $plazas_totales;
    private $id_tipo_activ;
    private $dl_org;
    private $id_ubi;
    private $num; // numero ordinal de asistencias
    private $aListaCargos;

    private $publicado;
    private $leyenda_html;
    private $resumen_plazas;
    private $resumen_plazas2;
    private $aLinks_dl;

    private $a_plazas_resumen;
    private $a_plazas_conseguidas;

    /**
     * Para pasar a la vista, aparece como alerta antes de ejecutarse
     * @var string $txt_eliminar
     */
    private $txt_eliminar;
    /* @var $bloque string  necesario para el script */
    private $bloque;

    // ---------- Variables requeridas
    /* @var $queSel integer */
    private $queSel;
    /* @var $id_dossier integer */
    private $id_dossier;
    /* @var $pau string */
    private $pau;
    /* @var $obj_pau string */
    private $obj_pau;
    /* @var $id_pau integer */
    private $id_pau;
    /**
     * 3: para todo, 2, 1:solo lectura
     * @var integer $permiso
     */
    private $permiso;

    // ------ Variables para mantener la selección de la grid al volver atrás
    private $Qid_sel;
    private $Qscroll_id;
    private mixed $status;

    private function incrementa(&$var)
    {
        if (empty($var)) {
            $var = 1;
        } else {
            $var++;
        }
    }

    private function getBotones()
    {
        if (ConfigGlobal::is_app_installed('asistentes') && ConfigGlobal::mi_ambito() !== 'rstgr') {
            $a_botones[] = array('txt' => _("modificar asistencia"),
                'click' => "fnjs_modificar(this.form)"
            );
            $a_botones[] = array('txt' => _("cambiar actividad"),
                'click' => "fnjs_mover(this.form,$this->id_pau)"
            );
            $a_botones[] = array('txt' => _("borrar asistencia"),
                'click' => "fnjs_borrar(this.form)"
            );
            $a_botones[] = array('txt' => _("transferir a históricos"),
                'click' => "fnjs_transferir(this.form)"
            );
        }
        if (ConfigGlobal::is_app_installed('actividadcargos') && ConfigGlobal::mi_ambito() !== 'rstgr') {
            $a_botones[] = array('txt' => _("añadir cargo"),
                'click' => "fnjs_add_cargo(this.form)"
            );
            $a_botones[] = array('txt' => _("modificar cargo"),
                'click' => "fnjs_mod_cargo(this.form)"
            );
            $a_botones[] = array('txt' => _("quitar cargo"),
                'click' => "fnjs_borrar_cargo(this.form)"
            );
        }
        if (ConfigGlobal::is_app_installed('actividadestudios')) {
            $a_botones[] = array('txt' => _("plan estudios"),
                'click' => "fnjs_matriculas(this.form,\"#frm_matriculas\")"
            );
            $a_botones[] = array('txt' => _("E43"),
                'click' => "fnjs_e43(this.form)"
            );
        }

        return $a_botones;
    }

    private function getCabeceras()
    {
        $a_cabeceras = array(array('name' => _("num"), 'width' => 40),
            array('name' => _("nombre y apellidos"), 'width' => 300),
            array('name' => _("dl"), 'width' => 4),
            array('name' => _("propio"), 'width' => 40),
            array('name' => _("est. ok"), 'width' => 40),
            array('name' => _("falta"), 'width' => 40),
            array('name' => _("observaciones actividad"), 'width' => 150),
            array('name' => _("sacd."), 'width' => 10),
            array('name' => _("telf."), 'width' => 80),
            array('name' => _("mails"), 'width' => 100),
            array('name' => _("nombre"), 'width' => 30),
            array('name' => _("apellidos"), 'width' => 30),
            array('name' => _("ctr"), 'width' => 30),
        );
        return $a_cabeceras;
    }


    private function getDatosActividad()
    {
        $oActividad = new ActividadAll($this->id_pau);
        $this->id_tipo_activ = $oActividad->getId_tipo_activ();
        $this->dl_org = $oActividad->getDl_org();
        $this->plazas_totales = $oActividad->getPlazas();
        $this->id_ubi = $oActividad->getId_ubi();
        $this->publicado = $oActividad->getPublicado();

    }

    private function getTituloPlazas()
    {
        if (empty($this->plazas_totales)) {
            $oCasa = Ubi::NewUbi($this->id_ubi);
            // A veces por error se puede poner una actividad a un ctr...
            if (method_exists($oCasa, 'getPlazas')) {
                $plazas_max = $oCasa->getPlazas();
                $plazas_min = $oCasa->getPlazas_min();
            } else {
                $plazas_max = '';
                $plazas_min = '';
            }
            $plazas_txt = _("plazas casa (max - min)") . ": ";
            $plazas_txt .= !empty($plazas_max) ? $plazas_max : '?';
            $plazas_txt .= !empty($plazas_min) ? ' - ' . $plazas_min : '';
        } else {
            $plazas_txt = _("plazas actividad") . ": ";
            $plazas_txt .= !empty($this->plazas_totales) ? $this->plazas_totales : '?';
        }
        $this->plazas_txt = $plazas_txt;
    }

    /**
     * Genera:
     * $this->a_plazas_conseguidas
     * $this->a_pazas_resumen
     */
    private function contarPlazas()
    {
        $a_plazas_resumen = [];
        $a_plazas_conseguidas = [];

        $gesActividadPlazasR = new GestorResumenPlazas();
        $gesActividadPlazasR->setId_activ($this->id_pau);

        $this->a_plazas_conseguidas = [];
        $aaa = $gesActividadPlazasR->getResumen();
        $this->a_plazas_resumen = $aaa;
    }


    /**
     * Establece
     *        $this->num = $num;
     *        $this->a_valores = $a_valores;
     * Incrementa:
     *        $this->a_plazas_conseguidas
     *        $this->a_pazas_resumen
     */
    public function getCargos()
    {
        // Permisos según el tipo de actividad
        $oPermDossier = new PermDossier();
        $this->a_ref_perm = $oPermDossier->perm_pers_activ($this->id_tipo_activ);

        // primero el cl:
        // primero los cargos
        $gesAsistentes = new GestorAsistente();
        $c = 0;
        $num = 0;
        $a_valores = [];
        $this->aListaCargos = [];
        $GesCargosEnActividad = new GestorActividadCargo();
        $cCargosEnActividad = $GesCargosEnActividad->getActividadCargos(array('id_activ' => $this->id_pau));
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        foreach ($cCargosEnActividad as $oActividadCargo) {
            $c++;
            $num++; // número total de asistentes.
            $id_item_cargo = $oActividadCargo->getId_item();
            $id_nom = $oActividadCargo->getId_nom();
            $this->aListaCargos[] = $id_nom;
            $id_cargo = $oActividadCargo->getId_cargo();
            $oCargo = $CargoRepository->findById($id_cargo);
            $tipo_cargo = $oCargo->getTipoCargoVo()?->value();
            $cargo = $oCargo->getCargoVo()->value();
            // para los sacd en sf
            if ($tipo_cargo === 'sacd' && $mi_sfsv == 2) {
                continue;
            }

            $oPersona = Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $this->msg_err .= "<br>";
                $this->msg_err .= sprintf(_("%s. En %s linea %s"), $oPersona, __FILE__, __LINE__);
                continue;
            }

            $nom = $oPersona->getPrefApellidosNombre();
            $nombre = $oPersona->getNom();
            $apellidos = $oPersona->getApellidos();
            $sacd = ($oPersona->getSacd()) ? _("sí") : '';
            // Añado los telf:
            $telfs = '';
            $telfs_fijo = $oPersona->telecos_persona($id_nom, "telf", " / ", "*", FALSE);
            $telfs_movil = $oPersona->telecos_persona($id_nom, "móvil", " / ", "*", FALSE);
            if (!empty($telfs_fijo) && !empty($telfs_movil)) {
                $telfs = $telfs_fijo . " / " . $telfs_movil;
            } else {
                $telfs .= $telfs_fijo ?? '';
                $telfs .= $telfs_movil ?? '';
            }
            $mails = $oPersona->telecos_persona($id_nom, "e-mail", " / ", "*", FALSE);

            $puede_agd = $oActividadCargo->getPuede_agd();
            $observ_cargo = $oActividadCargo->getObserv();
            $dl_asistente = $oPersona->getDl();
            $ctr_dl = $oPersona->getCentro_o_dl();
            // permisos (añado caso de cargo sin nombre = todos permiso)
            if ($id_tabla = $oPersona->getId_tabla()) {
                $a_act = $this->a_ref_perm[$id_tabla];
                if ($a_act["perm"]) {
                    $this->permiso = 3;
                } else {
                    $this->permiso = 1;
                }
            } else {
                $this->permiso = 3;
            }

            // ahora miro si también asiste:
            $plaza = Asistente::PLAZA_PEDIDA;
            $aWhere = array('id_activ' => $this->id_pau, 'id_nom' => $id_nom);
            $aOperador = array('id_activ' => '=', 'id_nom' => '=');
            // me aseguro de que no sea un cargo vacio (sin id_nom)
            if (!empty($id_nom) && $cAsistente = $gesAsistentes->getAsistentes($aWhere, $aOperador)) {
                if (is_array($cAsistente) && count($cAsistente) > 1) {
                    $tabla = '';
                    foreach ($cAsistente as $Asistente) {
                        $tabla .= "<li>" . $Asistente->getNomTabla() . "</li>";
                    }
                    $msg_err = _("ERROR: más de un asistente con el mismo id_nom") . "<br>";
                    $msg_err .= "<br>$nom(" . $oPersona->getId_tabla() . ")<br><br>";
                    $msg_err .= _("En las tablas") . ":<ul>$tabla</ul>";
                    exit ("$msg_err");
                }
                $oAsistente = $cAsistente[0];
                $propio = $oAsistente->getPropio();
                $falta = $oAsistente->getFalta();
                $est_ok = $oAsistente->getEst_ok();
                $observ = $oAsistente->getObserv();
                $plaza = empty($oAsistente->getPlaza()) ? Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();

                // contar plazas
                if (ConfigGlobal::is_app_installed('actividadplazas')) {
                    // las cuento todas y a la hora de enseñar miro si soy la dl org o no.
                    // propiedad de la plaza:
                    $propietario = $oAsistente->getPropietario() ?? '';
                    $padre = strtok($propietario, '>');
                    $child = strtok('>');
                    $dl = $child;
                    //si es de otra dl no distingo cedidas.
                    // no muestro ni cuento las que esten en estado distinto al asignado o confirmado (>3)
                    if ($padre != $this->mi_dele) {
                        if ($plaza > Asistente::PLAZA_DENEGADA) {
                            $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                            if (!empty($child) && $child != $padre) {
                                $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                            }
                        } else {
                            if (!empty($child) && $child == $this->mi_dele) {
                                $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                            } elseif (!empty($padre)) {
                                continue;
                            }
                        }
                    } else {  // En mi dl distingo las cedidas
                        // si no es de (la dl o de paso ) y no tiene la plaza asignada o confirmada no lo muestro
                        if ($child != $this->mi_dele) {
                            if ($plaza < Asistente::PLAZA_ASIGNADA) {
                                continue;
                            } else {
                                $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                                $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                            }
                        } else {
                            $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                        }
                    }
                }

                if (is_true($propio)) {
                    $chk_propio = _("sí");
                    // Para los de des, elimino el cargo y la asistencia. Para el resto, sólo el cargo (no la asistencia).
                    if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
                        $eliminar = 2;
                    } else {
                        $eliminar = 1;
                    }
                } else {
                    $chk_propio = _("no");
                    $eliminar = 2;  //si no es propio, al eliminar el cargo, elimino la asistencia
                }
                is_true($falta) ? $chk_falta = _("sí") : $chk_falta = _("no");
                is_true($est_ok) ? $chk_est_ok = _("sí") : $chk_est_ok = _("no");
                $asis = "t";

                if ($this->permiso == 3) {
                    $a_valores[$c]['sel'] = "$id_nom#$id_item_cargo#$eliminar";
                } else {
                    $a_valores[$c]['sel'] = "";
                }
                $a_valores[$c][4] = $chk_propio;
                $a_valores[$c][5] = $chk_est_ok;
                $a_valores[$c][6] = $chk_falta;
            } else {
                $a_valores[$c][4] = array('span' => 3, 'valor' => _("no asiste"));
                $observ = '';
                $num--;
                $asis = "f";
            }

            if (!empty($plaza)) {
                $a_valores[$c]['clase'] = 'plaza' . $plaza;
            } else {
                $a_valores[$c]['clase'] = 'plaza1';
            }

            $a_valores[$c][1] = $cargo;
            $a_valores[$c][2] = "$nom  ($ctr_dl)";
            $a_valores[$c][3] = $dl_asistente;
            $a_valores[$c][7] = "$observ_cargo $observ";
            $a_valores[$c][8] = "$sacd";
            $a_valores[$c][9] = "$telfs";
            $a_valores[$c][10] = "$mails";
            $a_valores[$c][11] = "$nombre";
            $a_valores[$c][12] = "$apellidos";
            $a_valores[$c][13] = "$ctr_dl";
        }

        $this->num = $num;
        $this->a_valores = $a_valores;
    }

    /**
     * Establece:
     *        $a_asistentes
     * Incrementa las propiedades:
     * $this->a_plazas_resumen
     * $this->a_plazas_conseguidas
     *
     */
    public function getAsistentes()
    {
        $gesAsistentes = new GestorAsistente();
        $this->a_asistentes = [];
        $cAsistentes = $gesAsistentes->getAsistentes(array('id_activ' => $this->id_pau));
        foreach ($cAsistentes as $oAsistente) {
            $this->num++;
            $id_nom = $oAsistente->getId_nom();
            // si ya está en la lista voy a por otro asistente
            if (in_array($id_nom, $this->aListaCargos)) {
                $this->num--;
                continue;
            }

            $oPersona = Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $this->msg_err .= "<br>";
                $this->msg_err .= sprintf(_("%s. En %s linea %s"), $oPersona, __FILE__, __LINE__);
                continue;
            }
            $obj_pau = $oPersona->getClassName();
            $nom = $oPersona->getPrefApellidosNombre();
            $nombre = $oPersona->getNom();
            $apellidos = $oPersona->getApellidos();
            $sacd = ($oPersona->getSacd()) ? _("sí") : '';
            $dl_asistente = $oPersona->getDl();
            $ctr_dl = $oPersona->getCentro_o_dl();
            // Añado los telf:
            $telfs = '';
            $telfs_fijo = $oPersona->telecos_persona($id_nom, "telf", " / ", "*", FALSE);
            $telfs_movil = $oPersona->telecos_persona($id_nom, "móvil", " / ", "*", FALSE);
            if (!empty($telfs_fijo) && !empty($telfs_movil)) {
                $telfs = $telfs_fijo . " / " . $telfs_movil;
            } else {
                $telfs .= $telfs_fijo ?? '';
                $telfs .= $telfs_movil ?? '';
            }
            $mails = $oPersona->telecos_persona($id_nom, "e-mail", " / ", "*", FALSE);

            $propio = $oAsistente->getPropio();
            $falta = $oAsistente->getFalta();
            $est_ok = $oAsistente->getEst_ok();
            $observ = $oAsistente->getObserv();
            $plaza = Asistente::PLAZA_PEDIDA;

            // contar plazas
            //if (ConfigGlobal::is_app_installed('actividadplazas') && !empty($dl)) {
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $plaza = empty($oAsistente->getPlaza()) ? Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();
                // las cuento todas y a la hora de enseñar miro si soy la dl org o no.
                // propiedad de la plaza:
                $propietario = $oAsistente->getPropietario();
                if ($propietario === NULL || $propietario === 'xxx') {
                    $this->msg_err .= _("ALERTA: asistente sin propiedad en la plaza") . ":<br>";
                    $this->msg_err .= "$nom(" . $oPersona->getId_tabla() . ")<br>";
                    $propietario = '';
                }
                $padre = strtok($propietario, '>');
                $child = strtok('>');
                $dl = $child;
                // 14.4.2021 para personas de paso, si no soy la dl organizadora, sólo muestro los que les he dado Yo la plaza.
                if ($obj_pau === 'PersonaEx' && $this->mi_dele != $this->dl_org) {
                    if ($oAsistente->getDl_responsable() != $this->mi_dele) {
                        continue;
                    }
                }
                //si es de otra dl no distingo cedidas.
                // no muestro ni cuento las que estén en estado distinto al asignado o confirmado (>3)
                if ($padre != $this->mi_dele) {
                    if ($plaza > Asistente::PLAZA_DENEGADA) {
                        $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                        if (!empty($child) && $child != $padre) {
                            $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                        }
                    } else {
                        if (!empty($child) && $child == $this->mi_dele) {
                            $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                        } elseif (!empty($padre)) {
                            continue;
                        }
                    }
                } else {  // En mi dl distingo las cedidas
                    // si no es de (la dl o de paso ) y no tiene la plaza asignada o confirmada no lo muestro
                    if ($child != $this->mi_dele) {
                        if ($plaza < Asistente::PLAZA_ASIGNADA) {
                            continue;
                        } else {
                            $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                            $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                        }
                    } else {
                        $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                    }
                }
            }

            if (is_true($propio)) {
                $chk_propio = _("sí");
            } else {
                $chk_propio = _("no");
            }
            is_true($falta) ? $chk_falta = _("sí") : $chk_falta = _("no");
            is_true($est_ok) ? $chk_est_ok = _("sí") : $chk_est_ok = _("no");

            if ($this->permiso == 3) {
                $a_val['sel'] = "$id_nom";
            } else {
                $a_val['sel'] = "";
            }

            $a_val['clase'] = 'plaza1';
            if (!empty($plaza)) {
                $a_val['clase'] = 'plaza' . $plaza;
            }

            $a_val[2] = "$nom  ($ctr_dl)";
            $a_val[3] = $dl_asistente;
            $a_val[4] = $chk_propio;
            $a_val[5] = $chk_est_ok;
            $a_val[6] = $chk_falta;
            $a_val[7] = $observ;
            $a_val[8] = $sacd;
            $a_val[9] = "$telfs";
            $a_val[10] = "$mails";
            $a_val[11] = "$nombre";
            $a_val[12] = "$apellidos";
            $a_val[13] = "$ctr_dl";

            $this->a_asistentes[$nom] = $a_val;
        }
        uksort($this->a_asistentes, "core\strsinacentocmp");

    }

    /**
     * Establece los textos:
     *    $this->leyenda_html
     *    $this->resumen_plazas
     *    $this->resumen_plazas2
     *
     * Incrementa
     * $this->a_plazas_resumen
     * $this->a_asistentes
     *
     */
    public function getLeyenda()
    {
        //leyenda colores
        $leyenda_html = '';
        // resumen plazas
        $disponibles = '';
        $resumen_plazas = '';
        $resumen_plazas2 = '';
        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            //leyenda colores
            $explicacion1 = _("plaza que contabiliza pero que las otras delegaciones no ven. Podría explicarse como una plaza que se desea pero no se puede conceder porque no hay sitio");
            $explicacion2 = _("como la plaza pedida, pero cuando ya se ha solicitado a la otra delegación que nos conceda ese plaza. Implica que por nuestra parte nos parece correcto que vaya pero necesitamos confirmación de que hay sitio");
            $explicacion4 = _("plaza ocupada en toda regla. Las delegaciones organizadoras ven a los nuestros. Si somos nosotros los organizadores, podemos ocupar más plazas de las previstas. Si son de otra delegación, no debería poder pasar a asignada si no hay plazas");
            $explicacion5 = _("como la anterior pero con el plus de que se ha comunicado al interesado y no hay cambio");

            $leyenda_html = '<p class="contenido">';
            $leyenda_html .= _("para seleccionar varios: 'Ctrl+Click' o bien 'Mays+Click'");
            $leyenda_html .= "<br><style>
				.box {
				display: inline;
				height: 1em;
				line-height: 3;
				padding: 0.3em;
				border-style: outset;
				cursor: pointer;
				}
				</style>
				";
            $oGesAsistente = new GestorAsistente();
            $aOpciones = $oGesAsistente->getOpcionesPosiblesPlaza();
            foreach ($aOpciones as $plaza => $plaza_txt) {
                $expl = "explicacion$plaza";
                $explicacion = $$expl;
                // No se puede poner 'this.form' com formulario, porque <div> no es un elemento de formulario
                $leyenda_html .= "<div class='box plaza$plaza' onCLick=fnjs_cmb_plaza('#frm_3101','$plaza') title=\"$explicacion\">$plaza_txt</div>  ";
            }
            $leyenda_html .= "</p>";
            ////////////////////////////////////////////////////////////////////
            // Si no está publicada no hace falta el resumen de plazas
            if ($this->publicado === true) {
                if (array_key_exists($this->mi_dele, $this->a_plazas_resumen)) {
                    foreach ($this->a_plazas_resumen as $padre => $aa) {
                        if ($padre === 'total') {
                            continue;
                        }
                        if ($padre != $this->mi_dele && $this->mi_dele != $this->dl_org) {
                            continue;
                        }
                        $calendario = empty($aa['calendario']) ? 0 : $aa['calendario']; // calendario.
                        $conseguidas = empty($aa['total_conseguidas']) ? 0 : $aa['total_conseguidas']; // conseguidas.
                        $a_conseguidas = empty($aa['conseguidas']) ? [] : $aa['conseguidas']; // conseguidas.
                        $total_cedidas = empty($aa['total_cedidas']) ? 0 : $aa['total_cedidas'];
                        $a_disponibles = empty($aa['disponibles']) ? [] : $aa['disponibles'];
                        $total_disponibles = empty($aa['total_disponibles']) ? 0 : $aa['total_disponibles'];
                        $a_cedidas = empty($aa['cedidas']) ? [] : $aa['cedidas'];
                        $json_cedidas = (count($a_cedidas) > 0) ? json_encode($a_cedidas) : '';
                        $a_ocupadas = empty($aa['ocupadas']) ? [] : $aa['ocupadas'];
                        $total = $total_disponibles;

                        $decidir = 0;
                        $espera = 0;
                        $ocupadas = 0;
                        $continuacion = false; //para indicar si hay que poner '+' al añadir texto.
                        $resumen_plazas .= "$padre: ";
                        // ocupadas por la dl padre
                        $ocupadas_calendario = empty($a_ocupadas[$padre]) ? 0 : $a_ocupadas[$padre];
                        if ($ocupadas_calendario > 0) {
                            $resumen_plazas .= "$ocupadas_calendario($padre)";
                        }
                        $ocupadas += $ocupadas_calendario;

                        // ocupadas por las dl cedidas
                        $i = 0;
                        $ocupadas_dl = 0;
                        foreach ($a_cedidas as $dl2 => $numCedidas) {
                            $i++;
                            if (!empty($ocupadas_dl)) {
                                $resumen_plazas .= ($continuacion) ? ' + ' : '';
                                $resumen_plazas .= "$ocupadas_dl($dl2)";
                            }
                            $this->a_plazas_resumen[$padre]['cedidas'][$dl2] = array('ocupadas' => $ocupadas_dl);
                            // pongo los de otras dl, que todavia no estan asignados como genéricos:
                            if ($this->mi_dele != $dl2 && $dl2 != $this->dl_org) {
                                $pl = empty($aCed[$dl2]) ? 0 : $aCed[$dl2];
                                if (!array_key_exists($dl2, $this->a_plazas_resumen)) {
                                    for ($i = $ocupadas_dl + 1; $i <= $pl; $i++) {
                                        $nom = "$dl2----$i";
                                        $a_val['sel'] = '';
                                        $a_val['clase'] = 'plaza4';
                                        $a_val[2] = $nom;
                                        $a_val[3] = '';
                                        $a_val[4] = '';
                                        $a_val[5] = '';
                                        $a_val[6] = '';

                                        $this->a_asistentes[$nom] = $a_val;
                                    }
                                }
                                $pl_relleno[$dl2] = $pl - $ocupadas_dl;
                            }
                        }
                        $ocupadas += $ocupadas_dl;
                        // Conseguidas
                        if (!empty($a_conseguidas)) {
                            $ocupadas_otra = 0;
                            // ocupadas por la dl padre
                            foreach ($a_conseguidas as $dl => $pl) {
                                $pl_ocu = $a_ocupadas[$dl];
                                $resumen_plazas .= ($continuacion) ? ' + ' : '';
                                $txt = sprintf(_("(de las %s cedidas por %s)"), $pl, $dl);
                                $resumen_plazas .= $pl_ocu . " " . $txt;
                                $ocupadas_otra += $pl_ocu;
                                $continuacion = true;
                            }
                            $ocupadas += $ocupadas_otra;
                        }

                        if (!empty($json_cedidas)) {
                            $resumen_plazas .= " [" . _("cedidas") . "=$total_cedidas $json_cedidas ]";
                        }
                        $resumen_plazas .= "  => " . _("ocupadas") . "=$ocupadas/($total_disponibles)";
                        $libres = $total_disponibles - $ocupadas;
                        if ($libres < 0) {
                            $resumen_plazas .= "<span style='background-color: red'> libres= $libres</span>";
                        } else {
                            $resumen_plazas .= " libres=$libres";
                        }
                        if ($this->mi_dele == $padre) {
                            if (!empty($espera)) {
                                $resumen_plazas .= " " . sprintf(_("(%s en espera)"), $espera);
                            }
                            if (!empty($decidir)) {
                                $resumen_plazas .= " " . sprintf(_("(%s por decidir)"), $decidir);
                            }
                        }
                        $resumen_plazas .= ";<br>";
                        // pongo los de otras dl, que todavia no estan asignados como genéricos:
                        if ($this->mi_dele != $padre && $padre != $this->dl_org) {
                            $ocu_relleno = $total - $libres;
                            for ($i = $ocu_relleno + 1; $i <= $total; $i++) {
                                $nom = "$padre-$i";
                                $a_val['sel'] = '';
                                $a_val['clase'] = 'plaza4';
                                $a_val[2] = $nom;
                                $a_val[3] = '';
                                $a_val[4] = '';
                                $a_val[5] = '';
                                $a_val[6] = '';

                                $this->a_asistentes[$nom] = $a_val;
                            }
                        }
                    }
                } elseif (array_key_exists($this->mi_dele, $this->a_plazas_conseguidas)) {  // No es una dl organizadora/colaboradora
                    $a_dl_plazas = $this->a_plazas_conseguidas[$this->mi_dele];
                    // ocupadas por la dl padre
                    $resumen_plazas2 = "$this->mi_dele: ";
                    $p = 0;
                    foreach ($a_dl_plazas as $dl2 => $pla) {
                        $plazas = empty($pla['ocupadas']) ? [] : $pla['ocupadas'];
                        $pla['cedidas'] = empty($pla['cedidas']) ? '?' : $pla['cedidas'];
                        foreach ($plazas as $dl => $pl) {
                            $p++;
                            $decidir = 0;
                            $espera = 0;
                            $ocupadas_dl = 0;
                            foreach ($pl as $plaza => $num) {
                                if ($plaza == Asistente::PLAZA_PEDIDA) {
                                    $decidir += $num;
                                }
                                if ($plaza == Asistente::PLAZA_EN_ESPERA) {
                                    $espera += $num;
                                }
                                if ($plaza > Asistente::PLAZA_DENEGADA) {
                                    $ocupadas_dl += $num;
                                }
                            }
                            $txt = sprintf(_("(de las %s cedidas por %s)"), $pla['cedidas'], $dl2);
                            $resumen_plazas2 .= ($p > 1) ? ' + ' : '';
                            $resumen_plazas2 .= $ocupadas_dl . " " . $txt;
                            if (!empty($espera)) {
                                $resumen_plazas2 .= " " . sprintf(_("(%s en espera)"), $espera);
                            }
                            if (!empty($decidir)) {
                                $resumen_plazas2 .= " " . sprintf(_("(%s por decidir)"), $decidir);
                            }
                        }
                    }
                    $resumen_plazas2 .= ";<br>";
                }
            }
        }
        $this->leyenda_html = $leyenda_html;
        $this->resumen_plazas = $resumen_plazas;
        $this->resumen_plazas2 = $resumen_plazas2;
    }

    public function getValores()
    {
        return $this->a_valores;
    }

    public function getTabla()
    {
        if (ConfigGlobal::is_app_installed('actividadcargos')) {
            $this->getCargos();
            $c = count($this->a_valores);
        } else {
            $c = 0;
            $this->num = 0;
            $this->a_valores = [];
        }

        $this->getAsistentes();
        $this->getLeyenda();

        $n = $c;
        foreach ($this->a_asistentes as $val) {
            $c++;
            $val[1] = "-";
            // sólo numero los asignados y confirmados
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                if ($val['clase'] === 'plaza4' || $val['clase'] === 'plaza5') {
                    $n++;
                    $val[1] = "$n.-";
                }
            } else {
                $n++;
                $val[1] = "$n.-";
            }
            // Los añado a los cargos
            $this->a_valores[$c] = $val;
        }
        if (!empty($this->a_valores)) {
            // Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
            // las pongo al final, porque al contar los valores del array se despista.
            if (!empty($this->Qid_sel)) {
                $this->a_valores['select'] = $this->Qid_sel;
            }
            if (!empty($this->Qscroll_id)) {
                $this->a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }
    }


    public function getHtml()
    {
        $this->msg_err = '';
        $this->txt_eliminar = _("¿Está seguro que desea borrar a esta persona de esta actividad?");

        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            $this->contarPlazas();
        }
        $this->getTabla(); // antes debe estar el contarPlazas

        $oTabla = new Lista();
        $oTabla->setId_tabla('sql_3101');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());


        $oHash = new Hash();
        $oHash->setCamposForm('');
        $oHash->setCamposNo('sel!scroll_id!mod!que!refresh');
        $a_camposHidden = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'id_dossier' => $this->id_dossier,
            'queSel' => $this->queSel,
            'permiso' => 3,
        );

        $oHash->setArraycamposHidden($a_camposHidden);

        // para el hash de las matrículas. Hago otro formulario, pues cambio demasiadas cosas
        $oHash1 = new Hash();
        $oHash1->setCamposForm('');
        $oHash1->setCamposNo('sel!scroll_id!mod');
        $a_camposHidden = array(
            'queSel' => 'matriculas',
            'pau' => 'p',
            'obj_pau' => 'Persona',
            'id_dossier' => 1303,
            'permiso' => 3,
            'id_activ' => $this->id_pau,
        );
        $oHash1->setArraycamposHidden($a_camposHidden);

        $url = ConfigGlobal::getWeb() . "/apps/dossiers/controller/dossiers_ver.php";
        $oHash2 = new Hash();
        $oHash2->setUrl($url);
        $oHash2->setCamposForm('depende!pau!obj_pau!id_pau!id_dossier!permiso');
        $h = $oHash2->linkSinVal();

        $oHash3 = new Hash();
        $oHash3->setUrl(ConfigGlobal::getWeb() . "/apps/asistentes/controller/form_mover.php");
        $oHash3->setCamposForm('id_pau!id_activ');
        $h3 = $oHash3->linkSinVal();

        $oHash4 = new Hash();
        $oHash4->setUrl(ConfigGlobal::getWeb() . "/apps/asistentes/controller/update_3101.php");
        $oHash4->setCamposForm('mod!plaza!lista_json!id_activ');
        $h4 = $oHash4->linkSinVal();

        $this->setLinksInsert();

        /* ---------------------------------- html --------------------------------------- */
        $a_campos = [
            'oTabla' => $oTabla,
            'oHash' => $oHash,
            'id_pau' => $this->id_pau,
            'h4' => $h4,
            'h3' => $h3,
            'oHash1' => $oHash1,
            'plazas_txt' => $this->plazas_txt,
            'resumen_plazas' => $this->resumen_plazas,
            'resumen_plazas2' => $this->resumen_plazas2,
            'leyenda_html' => $this->leyenda_html,
            'aLinks_dl' => $this->aLinks_dl,
            'msg_err' => $this->msg_err,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
        ];

        $oView = new ViewPhtml(__NAMESPACE__);

        $oView->renderizar('select3101.phtml', $a_campos);

    }

    public function setLinksInsert()
    {
        $this->aLinks_dl = [];
        $ref_perm = $this->a_ref_perm;
        if (empty($ref_perm) || $this->permiso < 2 || ConfigGlobal::mi_ambito() === 'rstgr') { // si es nulo, no tengo permisos de ningún tipo
            return '';
        }
        $mi_dele = ConfigGlobal::mi_delef();
        reset($ref_perm);
        foreach ($ref_perm as $val) {
            $permis = $val["perm"];
            $obj_pau = $val["obj"];
            $nom = $val["nom"];
            if (!empty($permis)) {
                $aQuery = array('mod' => 'nuevo',
                    'que_dl' => $mi_dele,
                    'pau' => $this->pau,
                    'obj_pau' => $obj_pau,
                    'id_dossier' => $this->id_dossier, //Para que al volver a la pagina 'dossiers_ver' sepa cual mostrar.
                    'id_pau' => $this->id_pau);
                // el hppt_build_query no pasa los valores null
                if (is_array($aQuery)) {
                    array_walk($aQuery, 'core\poner_empty_on_null');
                }
                $pagina = Hash::link('apps/asistentes/controller/form_3101.php?' . http_build_query($aQuery));
                $this->aLinks_dl[$nom] = $pagina;
            }
        }
    }


    public function getId_dossier()
    {
        return $this->id_dossier;
    }

    public function getPau()
    {
        return $this->pau;
    }

    public function getObj_pau()
    {
        return $this->obj_pau;
    }

    public function getId_pau()
    {
        return $this->id_pau;
    }

    public function getPermiso()
    {
        return $this->permiso;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setId_dossier($Qid_dossier)
    {
        $this->id_dossier = $Qid_dossier;
    }

    public function setPau($Qpau)
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau($Qobj_pau)
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau($Qid_pau)
    {
        $this->id_pau = $Qid_pau;
        $this->mi_dele = ConfigGlobal::mi_delef();
        $this->getDatosActividad();
    }

    public function setPermiso($Qpermiso)
    {
        $this->permiso = $Qpermiso;
    }

    public function setStatus($Qstatus)
    {
        $this->status = $Qstatus;
    }

    public function setQid_sel($Qid_sel)
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id($Qscroll_id)
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque($bloque)
    {
        $this->bloque = $bloque;
    }

    public function setQueSel($queSel)
    {
        $this->queSel = $queSel;
    }


}
