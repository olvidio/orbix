<?php

namespace actividadessacd\model;

use actividadcargos\model\entity\GestorActividadCargo;
use actividadcargos\model\entity\GestorCargo;
use actividades\model\entity\Actividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use config\model\entity\ConfigSchema;
use core\ConfigGlobal;
use core\ValueObject\Uuid;
use personas\model\entity\PersonaDl;
use shared\domain\ColaMailId;
use shared\domain\entity\ColaMail;
use shared\domain\repositories\ColaMailRepository;
use ubis\model\entity\CentroDl;
use ubis\model\entity\Ubi;
use usuarios\model\entity\GestorUsuario;
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
                $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];

                $_SESSION['oPermActividades']->setId_activ($id_activ);

                if (!is_true($this->propuesta) && ConfigGlobal::is_app_installed('procesos')) {
                    // Si tiene cargo sacd (se supone que comunicaractidvidadessacd sólo es para los sacd), que la fase 'ok_sacd' esté completada
                    // Si es asistente, que la fase ok_asistente esté completada.
                    $permiso_ver = $_SESSION['oPermActividades']->havePermisoSacd($id_cargo, $propio);
                } else {
                    $permiso_ver = TRUE;
                }

                if ($permiso_ver === FALSE) {
                    continue;
                }

                $oActividad = new Actividad($id_activ);
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
                    $nombre_ubi = $oCasa->getNombre_ubi();
                } else {
                    $nombre_ubi = $lugar_esp;
                }

                // ctr que organiza:
                $GesCentroEncargado = new GestorCentroEncargado();
                $ctrs = '';
                foreach ($GesCentroEncargado->getCentrosEncargadosActividad($id_activ) as $oCentro) {
                    if (!empty($ctrs)) { $ctrs .= ", "; }
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
            $ord_activ = array();
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

        $asunto = _("actividades");
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
        $gesUsuarios = new GestorUsuario();
        $cUsuarios = $gesUsuarios->getUsuarios(['usuario' => $jefe_calendario]);
        $oUsuarioJefe = $cUsuarios[0];
        $e_mail_jefe = $oUsuarioJefe->getEmail();

        $style = "<style>
    @page {
      size: A4 landscape;
    }

    div.salta_pag {
        clear: both;
        display: block;
        width: 25cm;
        page-break-after: always;
        font-family: 'Arial', sans-serif;
        font-size: 12pt;
    }

    table {
        width: 25cm;
        border: 1px outset grey;
        padding: 1px 
    }

    td { 
            border: thin inset grey;
            margin: 1px;
    }

    td.cabecera {
        text-align: left;
        font-weight: bold;
    }

    table.enc, table.enc td {
        border: 1px solid #000;
        border-collapse: collapse;
        margin: 0;
        margin-bottom: 1em;
        padding: 6px;
    }

    table.enc td.cabecera {
        text-align: center;
        font-weight: bold;
    }

    table.enc td.cabecera_izq {
        text-align: left;
        font-weight: bold;
    }

    td.centro {
        text-align: center;
    }

    p {
        margin-left: 0;
        font-weight: bold;
    }

    comunicacion {
        display: block;
        padding-left: 15mm;
        padding-right: 10mm;
        padding-top: 10mm;
        padding-bottom: 10mm;
    }

    izquierda {
        display: block;
        float: left;
        text-align: left;
    }

    derecha {
        display: block;
        float: right;
        text-align: right;
    }

    cabecera {
        display: block;
    }

    pie {
        display: block;
    }
</style>";

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

            $email = "$e_mail_jefe, $e_mail_sacd, $e_mail_ctr";

            $body = "<div class=salta_pag id=\"$i\">
                <br><!-- si no pongo esta linea, no me imprime el nombre (a partir de la 2ª página -->
                <cabecera>
                    <izquierda>$nom_ap</izquierda>";
            $body .= "<derecha>vc-$mi_dele</derecha>";
            $body .= "</cabecera>
                    <comunicacion>$txt</comunicacion>";

            $body .= " <!-- Actividades -->
                <table class=enc>
                    <tr>
                        <td class=cabecera_izq>$f_ini</td>
                        <td class=cabecera_izq>$f_fin</td>
                        <td class=cabecera>$nombre_ubi</td>
                        <td class=cabecera>$sfsv</td>
                        <td class=cabecera>$actividad</td>
                        <td class=cabecera>$asistentes</td>
                        <td class=cabecera>$encargado</td>
                        <td class=cabecera>$observ</td>
                        <td class=cabecera>$nom_tipo</td>
                    </tr>";
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
                    $body .= "<tr>
                        <td>$marca " . $act['f_ini'] . "</td>
                        <td>" . $act['f_fin'] . "</td>
                        <td class=centro>" . $act['nombre_ubi'] . "</td>
                        <td class=centro>" . $act['sfsv'] . "</td>
                        <td class=centro>" . $act['actividad'] . "</td>
                        <td class=centro>" . $act['asistentes'] . "</td>
                        <td class=centro>" . $act['encargado'] . "</td>
                        <td class=centro>" . $cargo_observ . "</td>
                        <td class=centro>" . $act['nom_tipo'] . "</td>
                    </tr>";
                }
            }
            $body .= "</table>
                    <pie>
                        <izquierda>*) $propio</izquierda>
                        <derecha>$lugar_fecha</derecha>
                    </pie>
                </div>";

            // mail

            //Envío en formato HTML
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";

            //Dirección del remitente
            $headers .= "From: Aquinate <no-Reply@moneders.net>\r\n";
            //Dirección de respuesta
            $headers .= "Reply-To: no-Reply@moneders.net\r\n";
            //Ruta del mensaje desde origen a destino
            $headers .= "Return-path: aquinate@moneders.net\r\n";

            $cuerpo = "<html lang=\"$idioma\">$style<body>$body</body></html>";
            //echo "($email<br>$asunto<br>$cuerpo<br>$headers)<br>";
            //mail($email, $asunto, $cuerpo, $headers);
            /* dado que desde dentro no puedo enviar mails,
            guardo la información en una tabla (cola_mails) en db=comun
            para poder acceder desde el servidor exterior, y con un cron
            o algo ir enviando los mails */
            $write_by = basename(__FILE__);
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
        }
    }
}