<?php

namespace actividadessacd\model;

use actividadcargos\model\entity\GestorActividadCargo;
use actividadcargos\model\entity\GestorCargo;
use actividades\model\entity\ActividadAll;
use actividadescentro\model\entity\GestorCentroEncargado;
use config\model\entity\ConfigSchema;
use core\ConfigGlobal;
use core\ValueObject\Uuid;
use personas\model\entity\PersonaDl;
use shared\domain\ColaMailId;
use shared\domain\entity\ColaMail;
use shared\domain\repositories\ColaMailRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use ubis\model\entity\CentroDl;
use ubis\model\entity\Ubi;
use web\DateTimeLocal;
use web\TiposActividades;
use function core\is_true;

class ComunicarActividadesSacd
{

    /* ATRIBUTOS ----------------------------------------------------------------- */
    private $cPersonas;
    private $inicioIso;
    private $finIso;
    private $propuesta;
    private $soloCargos = FALSE;
    private $quitarInactivos = FALSE;


    /* CONSTRUCTOR -------------------------------------------------------------- */


    public function setPersonas($cPersonas)
    {
        $this->cPersonas = $cPersonas;
    }

    public function setInicioIso($inicioIso)
    {
        $this->inicioIso = $inicioIso;
    }

    public function setFinIso($finIso)
    {
        $this->finIso = $finIso;
    }

    public function setPropuesta($propuesta)
    {
        $this->propuesta = $propuesta;
    }

    public function setSoloCargos($soloCargos)
    {
        $this->soloCargos = $soloCargos;
    }

    public function setQuitarInactivos($quitarInactivos)
    {
        $this->quitarInactivos = $quitarInactivos;
    }

    public function getArrayComunicacion()
    {
        // valores del id_cargo de tipo_cargo = sacd:
        $gesCargos = new GestorCargo();
        $aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');

        $oActividadesSacdFunciones = new ActividadesSacdFunciones();
        $s = 0;
        $array_actividades = [];
        // busco los datos de las actividades
        $aWhereAct = [];
        $aOperadorAct = [];
        $aWhereAct['f_ini'] = "'$this->finIso'";
        $aOperadorAct['f_ini'] = '<=';
        $aWhereAct['f_fin'] = "'$this->inicioIso'";
        $aOperadorAct['f_fin'] = '>=';
        $aWhereAct['status'] = '2';
        foreach ($this->cPersonas as $oPersona) {
            $s++;
            $id_nom = $oPersona->getId_nom();
            $nom_ap = $oPersona->getPrefApellidosNombre();
            $idioma = $oPersona->getLengua();

            $array_actividades[$id_nom]['txt']['com_sacd'] = $oActividadesSacdFunciones->getTraduccion('com_sacd', $idioma);
            $array_actividades[$id_nom]['txt']['t_propio'] = $oActividadesSacdFunciones->getTraduccion('t_propio', $idioma);
            $array_actividades[$id_nom]['txt']['t_f_ini'] = $oActividadesSacdFunciones->getTraduccion('t_f_ini', $idioma);
            $array_actividades[$id_nom]['txt']['t_f_fin'] = $oActividadesSacdFunciones->getTraduccion('t_f_fin', $idioma);
            $array_actividades[$id_nom]['txt']['t_nombre_ubi'] = $oActividadesSacdFunciones->getTraduccion('t_nombre_ubi', $idioma);
            $array_actividades[$id_nom]['txt']['t_sfsv'] = $oActividadesSacdFunciones->getTraduccion('t_sfsv', $idioma);
            $array_actividades[$id_nom]['txt']['t_actividad'] = $oActividadesSacdFunciones->getTraduccion('t_actividad', $idioma);
            $array_actividades[$id_nom]['txt']['t_asistentes'] = $oActividadesSacdFunciones->getTraduccion('t_asistentes', $idioma);
            $array_actividades[$id_nom]['txt']['t_encargado'] = $oActividadesSacdFunciones->getTraduccion('t_encargado', $idioma);
            $array_actividades[$id_nom]['txt']['t_observ'] = $oActividadesSacdFunciones->getTraduccion('t_observ', $idioma);
            $array_actividades[$id_nom]['txt']['t_nom_tipo'] = $oActividadesSacdFunciones->getTraduccion('t_nom_tipo', $idioma);

            $array_actividades[$id_nom]['nom_ap'] = $nom_ap;

            $aWhere = ['id_nom' => $id_nom];
            $aOperador = [];

            $oGesActividadCargo = new GestorActividadCargo();

            if ($this->soloCargos === TRUE) {
                $cAsistentes = $oGesActividadCargo->getCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
            } else {
                $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
            }

            $ord_activ = [];
            foreach ($cAsistentes as $aAsistente) {
                $id_activ = $aAsistente['id_activ'];
                $propio = $aAsistente['propio'];
                //$plaza = $aAsistente['plaza'];
                $id_cargo = empty($aAsistente['id_cargo']) ? NULL : $aAsistente['id_cargo'];

                $_SESSION['oPermActividades']->setId_activ($id_activ);

                if (!is_true($this->propuesta) && ConfigGlobal::is_app_installed('procesos')) {
                    // Si tiene cargo sacd (se supone que comunicaractidvidadessacd sólo es para los sacd), que la fase 'ok_sacd' esté completada
                    // Si es asistente, que la fase ok_asistente esté completada.
                    $permiso_ver = $_SESSION['oPermActividades']->havePermisoSacd($id_cargo, $propio);
                } else {
                    $permiso_ver = TRUE;
                }

                if (!is_true($permiso_ver)) {
                    continue;
                }

                $oActividad = new ActividadAll($id_activ);
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $id_ubi = $oActividad->getId_ubi();
                $lugar_esp = $oActividad->getLugar_esp();
                $oF_ini = $oActividad->getF_ini();
                $oF_fin = $oActividad->getF_fin();
                $h_ini = $oActividad->getH_ini();
                $h_fin = $oActividad->getH_fin();
                $observ = $oActividad->getObserv();

                $f_ini = $oF_ini->formatRoman();
                $f_fin = $oF_fin->formatRoman();

                if (!empty($h_ini)) {
                    $h_ini = preg_replace('/(\d{2}):(\d{2}):(\d{2})/', '\1:\2', $h_ini);
                    $f_ini .= " ($h_ini)";
                }
                if (!empty($h_fin)) {
                    $h_fin = preg_replace('/(\d{2}):(\d{2}):(\d{2})/', '\1:\2', $h_fin);
                    $f_fin .= " ($h_fin)";
                }

                $oTipoActiv = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActiv->getSfsvText();
                $sasistentes = $oTipoActiv->getAsistentesText();
                $sactividad = $oTipoActiv->getActividadText();
                $snom_tipo = $oTipoActiv->getNom_tipoText();
                // lugar
                if (empty($lugar_esp)) {
                    $oCasa = Ubi::NewUbi($id_ubi);
                    $nombre_ubi = $oCasa?->getNombre_ubi()?? '?';
                } else {
                    $nombre_ubi = $lugar_esp;
                }

                // ctr que organiza:
                $GesCentroEncargado = new GestorCentroEncargado();
                $ctrs = '';
                foreach ($GesCentroEncargado->getCentrosEncargadosActividad($id_activ) as $oCentro) {
                    if (!empty($ctrs)) {
                        $ctrs .= ", ";
                    }
                    $ctrs .= $oCentro->getNombre_ubi();
                }

                $cargo = '';
                if (!empty($id_cargo) && !array_key_exists($id_cargo, $aIdCargos_sacd)) {
                    $cargo = 'te carrec';
                }
                $array_act = array("propio" => $propio,
                    "f_ini" => $f_ini,
                    "f_fin" => $f_fin,
                    "nombre_ubi" => $nombre_ubi,
                    "id_activ" => $id_activ,
                    "sfsv" => $ssfsv,
                    "asistentes" => $sasistentes,
                    "actividad" => $sactividad,
                    "nom_tipo" => $snom_tipo,
                    "observ" => $observ,
                    "cargo" => $cargo,
                    "encargado" => $ctrs
                );
                //if (!empty($id_activ)) { $array_actividades[$id_nom]['actividades'][]= $array_act; }
                // para ordenar por fecha_ini
                $f_ord = $oF_ini->format('Ymd');
                // ojo. Si hay más de una actividad que empieza el mismo día, hay que poner algo para distinguirlas: les sumo un dia.
                if (isset($ord_activ) && array_key_exists($f_ord, $ord_activ)) {
                    $f_ord++;
                    $ord_activ[$f_ord] = $array_act;
                } else {
                    $ord_activ[$f_ord] = $array_act;
                }
            }
            if (!empty($ord_activ)) {
                ksort($ord_activ);
                $array_actividades[$id_nom]['actividades'] = $ord_activ;
            } else {
                $array_actividades[$id_nom]['actividades'] = '';
                // No pongo a los sacd de paso, si no tienen actividades
                if ($this->quitarInactivos === TRUE) {
                    unset($array_actividades[$id_nom]);
                }
            }
            $ord_activ = [];
        } // fin del while de los sacd


        return $array_actividades;
    }

    public function envairMails($array_actividades)
    {
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        // ciudad de la dl
        $oActividadesSacdFunciones = new ActividadesSacdFunciones();
        $poblacion = $oActividadesSacdFunciones->getLugar_dl();
        $lugar_fecha = "$poblacion, $hoy_local";

        $asunto = _("atención actividades");
        $mi_dele = ConfigGlobal::mi_dele();

        // e-mail jefe calendario
        $parametro = 'jefe_calendario';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();

        if (empty($valor)) {
            $error_txt = _("falta el definir el jefe de calendario");
            //exit ($this->msg);
        }

        // pasar el valor de nombres separados por coma a array:
        $a_jefes_calendario = explode(',', $valor);
        $jefe_calendario = $a_jefes_calendario[0];
        $UsuarioRepository = new UsuarioRepository();
        $cUsuarios = $UsuarioRepository->getUsuarios(['usuario' => $jefe_calendario]);
        $oUsuarioJefe = $cUsuarios[0];
        $e_mail_jefe = $oUsuarioJefe->getEmailAsString();
        if (empty($e_mail_jefe)) {
            exit(_("No hay un mail (jefe calendario) para enviar los errores. No se procesan los mails."));
        }

        $i = 0;
        foreach ($array_actividades as $id_nom => $vector) {
            $i++;
            $nom_ap = $vector['nom_ap'];

            $propio = $vector['txt']['t_propio'];
            $f_ini = $vector['txt']['t_f_ini'];
            $f_fin = $vector['txt']['t_f_fin'];
            $nombre_ubi = $vector['txt']['t_nombre_ubi'];
            $sfsv = $vector['txt']['t_sfsv'];
            $actividad = $vector['txt']['t_actividad'];
            $asistentes = $vector['txt']['t_asistentes'];
            $encargado = $vector['txt']['t_encargado'];
            $observ = $vector['txt']['t_observ'];
            $nom_tipo = $vector['txt']['t_nom_tipo'];

            $txt = $vector['txt']['com_sacd'];
            $a_actividades = $vector['actividades'];

            if (empty($a_actividades)) {
                continue; // me salto los que no tienen actividades.
            }
            // buscar el mail
            $oPersona = new PersonaDl($id_nom);
            $e_mail_sacd = $oPersona->emailPrincipalOPrimero($id_nom);
            $idioma = $oPersona->getLengua();

            // buscar el mail del ctr
            $id_ctr = $oPersona->getId_ctr();
            $oCentroDl = new CentroDl($id_ctr);
            $e_mail_ctr = $oCentroDl->emailPrincipalOPrimero();

            $email = $e_mail_jefe;
            $email .= empty($e_mail_sacd) ? '' : ", $e_mail_sacd";

            $body_cabecera = "<div style='clear: both; display: block; width: 25cm; page-break-after: always; font-family: sans-serif; font-size: 12pt;'>
                <br><!-- si no pongo esta linea, no me imprime el nombre (a partir de la 2ª página -->
                  <div style='display: block;width: 25cm;'>";
            $body_cabecera .= "<div style = 'display: block; float: left; text-align: left; width: 10cm;'>$nom_ap</div>";
            $body_cabecera .= "<div style='display: block; float: right; text-align: right; width: 10cm'>vc-$mi_dele</div>";
            $body_cabecera .= "</div>";
            $body_cabecera .= "<div style = 'display: block; padding-left: 15mm; padding-right: 10mm; padding-top: 10mm; padding-bottom: 10mm;'>$txt</div>";

            $body_cabecera .= " <!-- Actividades -->
                <table  style='border: 1px solid #000; border-collapse: collapse; margin: 0; margin-bottom: 1em; padding: 6px;'>
                    <tr>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$f_ini</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$f_fin</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$nombre_ubi</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$sfsv</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$actividad</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$asistentes</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$encargado</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$observ</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$nom_tipo</td>
                    </tr>";
            $body_sacd = '';
            $body_ctr = '';
            if (is_array($a_actividades)) {
                foreach ($a_actividades as $act) {
                    if (is_true($act['propio'])) {
                        $marca = '*';
                    } else {
                        $marca = '';
                    }
                    if (!empty($act['cargo'])) {
                        $cargo_observ = $act['cargo'] . '. ' . $act['observ'];
                    } else {
                        $cargo_observ = $act['observ'];
                    }
                    $body_sacd .= "<tr>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>$marca " . $act['f_ini'] . "</td>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>" . $act['f_fin'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['nombre_ubi'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['sfsv'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['actividad'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['asistentes'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['encargado'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $cargo_observ . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['nom_tipo'] . "</td>
                    </tr>";

                    $body_ctr .= "<tr>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>$marca " . $act['f_ini'] . "</td>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>" . $act['f_fin'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['nombre_ubi'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['sfsv'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['actividad'] . "</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['asistentes'] . "</td>
                        ";
                    if ($act['sfsv'] === "sf") {
                        $body_ctr .= "<td style='text-align: center; padding: 1rem; outline: 1px solid black;'></td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'></td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'></td>
                        </tr>";
                    } else {
                        $body_ctr .= "<td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['encargado'] . "</td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $cargo_observ . "</td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>" . $act['nom_tipo'] . "</td>
                        </tr>";
                    }
                }
            }
            $body_pie = "</table>";
            $body_pie .= "<div style = 'display: block;width: 25cm;'>
                        <div style = 'display: block; float: left; text-align: left;width: 10cm'>*) $propio</div>
                        <div style='display: block; float: right; text-align: right;width: 10cm'>$lugar_fecha</div>
                    </div>";
            $body_pie .= "</div>";

            // mail. Distingo para el sacd y para el ctr:
            $body_sacd = $body_cabecera . $body_sacd . $body_pie;
            $body_ctr = $body_cabecera . $body_ctr . $body_pie;

            //Envío en formato HTML
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";

            //Dirección del remitente
            $headers .= "From: Aquinate <no-Reply@moneders.net>\r\n";
            // Por cambios en la política de gmail, para evitar conflictos con
            // SPF y DKIM, el From debe ser igual que el Return-path.
            // Parece que el no-Reply también lo acepta.

            //Dirección de respuesta
            $headers .= "Reply-To: $e_mail_jefe\r\n";
            //Ruta del mensaje desde origen a destino
            // El exim4 escribe su return-path, y por tanto esta linea no
            // tiene ningún efecto. Por defecto es root@actividades.moneders.net,
            // se cambia en /etc/email-addresses:
            //  root: aquinate@moneders.net
            $headers .= "Return-path: $e_mail_jefe\r\n";
            // la idea es crear una regla de redirección en aquinate@moneders.net
            // que redirija a From.

            /* dado que desde dentro no puedo enviar mails,
            guardo la información en una tabla (cola_mails) en db=comun
            para poder acceder desde el servidor exterior, y con un cron
            o algo ir enviando los mails */
            $write_by = basename(__FILE__);
            $cuerpo = "<html lang=\"$idioma\"><body>$body_sacd</body></html>";
            $ColaMailId = new ColaMailId(Uuid::random());
            $oColaMail = new ColaMail();
            $oColaMail->setUuid_item($ColaMailId);
            $oColaMail->setMail_to($email);
            $oColaMail->setHeaders($headers);
            $oColaMail->setMessage($cuerpo);
            $oColaMail->setSubject($asunto);
            $oColaMail->setWrited_by($write_by);
            $ColaMailRepository = new ColaMailRepository();
            $ColaMailRepository->Guardar($oColaMail);

            // mail para el ctr. No se envía copia al jefe de calendario
            if (!empty($e_mail_ctr)) {
                $cuerpo = "<html lang=\"$idioma\"><body>$body_ctr</body></html>";
                $ColaMailId = new ColaMailId(Uuid::random());
                $oColaMail = new ColaMail();
                $oColaMail->setUuid_item($ColaMailId);
                $oColaMail->setMail_to($e_mail_ctr);
                $oColaMail->setHeaders($headers);
                $oColaMail->setMessage($cuerpo);
                $oColaMail->setSubject($asunto);
                $oColaMail->setWrited_by($write_by);
                $ColaMailRepository = new ColaMailRepository();
                $ColaMailRepository->Guardar($oColaMail);
            }

        }
    }
}