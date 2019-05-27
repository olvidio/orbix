<?php
namespace encargossacd\model;

use core\ConfigGlobal;
use encargossacd\model\entity\DatosCgi;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoHorario;
use encargossacd\model\entity\EncargoSacd;
use encargossacd\model\entity\EncargoSacdHorario;
use encargossacd\model\entity\GestorEncargoHorario;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use encargossacd\model\entity\GestorEncargoTexto;
use ubis\model\entity\GestorCentroDl;
use web\DateTimeLocal;

Trait EncargoFuncionesTrait {
    
    /**
     * array con las traducciones de claves por idiomas ([idoma,[claves]])
     * 
     * @var array
     */
    protected $a_txt = [];
    
    function getArrayTraducciones($idioma) {
        $idioma = empty($idioma)? 'es' : $idioma;
        if (empty($this->a_txt[$idioma])) {
            $oGesEncargoTextos = new GestorEncargoTexto();
            $cEncargoTextos = $oGesEncargoTextos->getEncargoTextos();
            foreach ($cEncargoTextos as $oEncargoTexto) {
                $clave = $oEncargoTexto->getClave();
                $idioma = $oEncargoTexto->getIdioma();
                $texto = $oEncargoTexto->getTexto();
                $this->a_txt[$idioma][$clave] = $texto;
            }
        }
        return $this->a_txt[$idioma];
    }
        
    function getTraduccion($clave,$idioma) {
        $a_traduccion = $this->getArrayTraducciones($idioma);
        if (!empty($a_traduccion[$clave])) {
            $txt_traduccion = $a_traduccion[$clave];
        } else {
            // El idioma por defecto (es) debería existir siempre
            $a_traduccion = $this->getArrayTraducciones('es');
            if (!empty($a_traduccion[$clave])) {
                $txt_traduccion = $a_traduccion[$clave];
            } else {
                echo sprintf(_("falta definir el texto %s en este idioma: %s"),$clave,$idioma);
            }
        }
        return $txt_traduccion;
    }
    
    function getLugar_dl() {
        $oGesCentrosDl = new GestorCentroDl();
        $cCentros = $oGesCentrosDl->getCentros(['tipo_ctr' => 'dl']);
        $num_dl = count($cCentros);
        switch ($num_dl) {
            case 0:
                // Puede ser el nombre de la región
                $cCentros = $oGesCentrosDl->getCentros(['tipo_ctr' => 'cr']);
                if (count($cCentros) > 0) {
                    $oCentro = $cCentros[0];
                } else {
                    // No existe el nombre de la delegacion ni región.
                    return '?';
                }
                break;
            case 1:
                $oCentro = $cCentros[0];
                break;
            default:
                // más de una dl?
                exit (_("Más de un centro definido como dl"));
                break;
        }
        // Buscar la direccion
        $cDirecciones = $oCentro->getDirecciones();
        
        $poblacion = '';
        if (is_array($cDirecciones) & !empty($cDirecciones)) {
            $d = 0;
            foreach ($cDirecciones as $oDireccion) {
                $d++;
                if ($d > 1) {
                    $poblacion .= '<br>';
                }
                $poblacion .= $oDireccion->getPoblacion();
            }
        } else {
            exit (_("falta poner la dirección a la dl"));
        }
        return $poblacion; 
    }
    
    /* TODO
     * fase real/pruebas
     */
    
    function getF_ini() {
        $oDate = new DateTimeLocal(date('Y-m-d')); // sólo el dia, sin la hora
        return $oDate;
    }
    
    function getF_fin() {
        $oDate = new DateTimeLocal(date('Y-m-d')); // sólo el dia, sin la hora
        return $oDate;
    }
    
	/*
				// create new ConfigMagik-Object
				$Config = new ConfigMagik( "encargos.ini", true, true);
				$Config->SYNCHRONIZE      = false;

				$f_ini_proves=$Config->get("f_ini_proves","encargos");
				$f_fin_proves=$Config->get("f_fin_proves","encargos");

				if (!empty($f_ini_proves)) {
					$oFiniProves = DateTime::createFromFormat('j/m/Y', $f_ini_proves);
					$ts_f_ini_proves=$oFiniProves->getTimestamp();
				}
				if (!empty($f_fin_proves)) {
					$oFfinProves = DateTime::createFromFormat('j/m/Y', $f_fin_proves);
					$ts_f_fin_proves=$oFfinProves->getTimestamp();
				}
				$ts_hoy=time();
				$hoy=date('d/m/Y');

				$pruebas=0;
				$fase= _("En fase real");
				if (empty($f_ini_proves) && empty($f_fin_proves)) {
					$pruebas=0;
					$fase= _("En fase real");
				} elseif ($ts_hoy>=$ts_f_ini_proves && $ts_hoy<$ts_f_fin_proves) {
					$pruebas=1;
					$fase= _("En fase de pruebas");
				}

				// Durante el periodo de propuestas:
				if ($pruebas==1) {
					$f_ini=$f_fin_proves;
				} else {
					$f_ini=$hoy;
				}

				$f_fin=$hoy;

				// para la variable del curso actual
				if (date("m")>6) { 
					$any1=date("Y");
					$any2=$any1+1;
				} else {
					$any2=date("Y");
					$any1=$any2-1;
				}
				$curso="$any1-$any2";
	 */

	// permiso para sf:
	function getArraySeccion() {
		if (($_SESSION['oPerm']->have_perm("des")) or ($_SESSION['oPerm']->have_perm("vcsd"))) {
			$array_seccion = [
						//'0'=>"personal",
						'1'=>"sv",
						'2'=>"sf",
						'3'=>"sss+",
						'4'=>"igl",
						'5'=>"cgi/oc"
					];
		} else {
			$array_seccion = [
						//'0'=>"personal",
						'1'=>"sv",
						'3'=>"sss+",
						'4'=>"igl",
						'5'=>"cgi/oc"
					];
		}
		return $array_seccion;
	}

    // para grabar los datos del número de alumnos (si es un cgi).
    function grabar_alumnos($id_ubi,$num_alum) {
        $oDatosCgi = new DatosCgi(array('id_ubi'=>$id_ubi,'curso_ini_any'=>$_SESSION['any1']));
        $oDatosCgi->DBcarregar();

        if (!empty($num_alum)) {
            $oDatosCgi->setNum_alum($num_alum);
            if ($oDatosCgi->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
            }
        } else {
            if ($oDatosCgi->DBEliminar() === false) {
                echo _("hay un error, no se ha eliminado");
            }
        }
    }

    function dedicacion_horas($id_nom,$id_enc,$idioma=""){
        $GesEncargoSacdHorario = new GestorEncargoSacdHorario();
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $GesEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);
        if (is_array($cEncargoSacdHorario ) && count($cEncargoSacdHorario ) == 0) { return false; }
        $dedic_h = 0;
        foreach ($cEncargoSacdHorario as $oEncargoSacdHorario) {
            $dia_inc = $oEncargoSacdHorario->getDia_inc();
            switch ($oEncargoSacdHorario->getDia_ref()) {
                case "m":
                    // supongo que la mañana es de 5 horas (para que dé 35 h/semana)
                    $dedic_h+=$dia_inc*5;
                    break;
                case "t":
                    $dedic_h+=$dia_inc*2;
                    break;
                case "v":
                    $dedic_h+=$dia_inc*3;
                    break;
            }
        }
        return $dedic_h;
    }

    function getTxtDedicacion($cEncargoHorarios,$idioma='') {
        $s=0;
        $dedicacion_m_txt="";
        $dedicacion_t_txt="";
        $dedicacion_v_txt="";
        foreach ($cEncargoHorarios as $oEncargoHorario) {
            $s++;
            $dia_ref = $oEncargoHorario->getDia_ref();
            $dia_inc = $oEncargoHorario->getDia_inc();
            //echo "n: $id_nom, e: $id_enc, r: $dia_ref, i: $dia_inc<br>";
            switch ($dia_ref) {
                case "m":
                    if ($dia_inc > 1) { 
                        $txt = $this->getTraduccion('t_mañana', $idioma);
                        $dedicacion_m_txt=$dia_inc." ".$txt;
                    } else {
                        $txt = $this->getTraduccion('t_mañanas', $idioma);
                        $dedicacion_m_txt=$dia_inc." ".$txt;
                    }
                    break;
                case "t":
                    if ($dia_inc > 1) {
                        $txt = $this->getTraduccion('t_tarde1', $idioma);
                        $dedicacion_m_txt=$dia_inc." ".$txt;
                    } else {
                        $txt = $this->getTraduccion('t_tardes1', $idioma);
                        $dedicacion_m_txt=$dia_inc." ".$txt;
                    }
                    break;
                case "v":
                    if ($dia_inc > 1) { 
                        $txt = $this->getTraduccion('t_tarde2', $idioma);
                        $dedicacion_m_txt=$dia_inc." ".$txt;
                    } else {
                        $txt = $this->getTraduccion('t_tardes2', $idioma);
                        $dedicacion_m_txt=$dia_inc." ".$txt;
                    }
                    break;
            }
        }
        $dedicacion_txt="($dedicacion_m_txt, $dedicacion_t_txt, $dedicacion_v_txt)";
        $dedicacion_txt=str_replace(", , ",", ",$dedicacion_txt);
        $dedicacion_txt=str_replace("(, ","(",$dedicacion_txt);
        $dedicacion_txt=str_replace(", )",")",$dedicacion_txt);
        if ($dedicacion_txt=="()") $dedicacion_txt="";
        
        return $dedicacion_txt;
    }

    function dedicacion_ctr($id_ubi,$id_enc,$idioma=''){
        $GesEncargoHorario = new GestorEncargoHorario();
        $aWhere['id_enc'] = $id_enc;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoHorarios = $GesEncargoHorario->getEncargoHorarios($aWhere,$aOperador);

        if (is_array($cEncargoHorarios ) && count($cEncargoHorarios ) == 0) { return false; }
        
        $dedicacion_txt = $this->getTxtDedicacion($cEncargoHorarios,$idioma);
        return $dedicacion_txt;
    }

    function dedicacion($id_nom,$id_enc,$idioma=''){
        $GesEncargoSacdHorario = new GestorEncargoSacdHorario();
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $GesEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);
        if (is_array($cEncargoSacdHorario ) && count($cEncargoSacdHorario ) == 0) { return false; }

        $dedicacion_txt = $this->getTxtDedicacion($cEncargoSacdHorario,$idioma);
        return $dedicacion_txt;
    }

    function insert_horario_ctr($id_enc,$modulo,$dedicacion,$n_sacd) {
        if (empty($n_sacd)) $n_sacd=1;
        $oEncargoHorario = new EncargoHorario();
        $oEncargoHorario->setid_enc($id_enc);
        $oEncargoHorario->setF_ini($this->getF_ini());
        $oEncargoHorario->setF_fin(NULL);
        $oEncargoHorario->setDia_ref($modulo);
        $oEncargoHorario->setDia_inc($dedicacion);
        $oEncargoHorario->setN_sacd($n_sacd);
        if ($oEncargoHorario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
        }
    }
    function modificar_horario_ctr($id_enc,$modulo,$dedicacion,$n_sacd) {
        if (empty($n_sacd)) $n_sacd=1;

        $GesEncargoHorario = new GestorEncargoHorario();
        $aWhere['id_enc'] = $id_enc;
        $aWhere['dia_ref'] = $modulo;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoHorarios = $GesEncargoHorario->getEncargoHorarios($aWhere,$aOperador);
        if (is_array($cEncargoHorarios ) && count($cEncargoHorarios ) == 0) {
            if (!empty($dedicacion)) $this->insert_horario_ctr($id_enc,$modulo,$dedicacion,$n_sacd);
        } else {
            $cEncargoHorarios[0]->DBCarregar(); 
            $dia_inc = $cEncargoHorarios[0]->getDia_inc();
            $cEncargoHorarios[0]->setDia_inc($dedicacion);
            $cEncargoHorarios[0]->setN_sacd($n_sacd);
            //???? $cEncargoHorarios[0]->setF_fin($this->f_fin);
            if ($cEncargoHorarios[0]->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
            }

            if (!empty($dedicacion) && $dia_inc!=$dedicacion) {
                $this->insert_horario_ctr($id_enc,$modulo,$dedicacion,$n_sacd);
            }
        }
    }
    function insert_horario_sacd($id_item_t_sacd,$id_enc,$id_nom,$modulo,$dedicacion) {
        $oEncargoSacdHorario = new EncargoSacdHorario();
        $oEncargoSacdHorario->setId_enc($id_enc);
        $oEncargoSacdHorario->setId_nom($id_nom);
        $oEncargoSacdHorario->setF_ini($this->getF_fin());
        $oEncargoSacdHorario->setF_fin(NULL);
        $oEncargoSacdHorario->setDia_ref($modulo);
        $oEncargoSacdHorario->setDia_inc($dedicacion);
        $oEncargoSacdHorario->setId_item_tarea_sacd($id_item_t_sacd);
        if ($oEncargoSacdHorario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
        }
    }
    function finalizar_horario_sacd($id_enc,$id_nom,$f_fin) {
        $GesEncargoSacdHorario = new GestorEncargoSacdHorario();
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $GesEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);
        foreach ($cEncargoSacdHorario as $oEncargoSacdHorario) {
            $oEncargoSacdHorario->DBCarregar();
            $oEncargoSacdHorario->setF_fin($f_fin);
            if ($oEncargoSacdHorario->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
            }
        }
    }
    function modificar_horario_sacd($id_item_t_sacd,$id_enc,$id_nom,$modulo,$dedicacion) {
        $GesEncargoSacdHorario = new GestorEncargoSacdHorario();
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['dia_ref'] = $modulo;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $GesEncargoSacdHorario->getEncargoSacdHorarios($aWhere,$aOperador);
        if (is_array($cEncargoSacdHorario ) && count($cEncargoSacdHorario ) == 0) {
            if (!empty($dedicacion)) $this->insert_horario_sacd($id_item_t_sacd,$id_enc,$id_nom,$modulo,$dedicacion);
        } else {
            $id_item = $cEncargoSacdHorario[0]->getId_item(); 
            $dia_inc = $cEncargoSacdHorario[0]->getDia_inc();
            if (!empty($dedicacion)) {
                if ($dia_inc!=$dedicacion) {
                    $cEncargoSacdHorario[0]->setF_fin($this->getF_fin());
                    if ($cEncargoSacdHorario[0]->DBGuardar() === false) {
                        echo _("hay un error, no se ha guardado");
                    }
                    $this->insert_horario_sacd($id_item_t_sacd,$id_enc,$id_nom,$modulo,$dedicacion);
                } else {
                    $oFactual_f_fin = $cEncargoSacdHorario[0]->getF_fin();
                    if ($oFactual_f_fin == $this->getF_fin()) {
                        $cEncargoSacdHorario[0]->setF_fin(NULL);
                        if ($cEncargoSacdHorario[0]->DBGuardar() === false) {
                            echo _("hay un error, no se ha guardado");
                        }
                    }
                } 
            } else {
                $cEncargoSacdHorario[0]->setDia_inc(NULL);
                $cEncargoSacdHorario[0]->setF_fin($this->getF_fin());
                if ($cEncargoSacdHorario[0]->DBGuardar() === false) {
                    echo _("hay un error, no se ha guardado");
                }
            }
        }
    }
    function insert_sacd($id_enc,$id_sacd,$modo){
        $GesEncargoSacd = new GestorEncargoSacd();
        $cEncargosSacd = $GesEncargoSacd->getEncargosSacd(array('id_enc'=>$id_enc,'id_nom'=>$id_sacd,'modo'=>$modo));
        foreach ($cEncargosSacd as $oEncargoSacd) { // aunque sólo debería haber una.
            $oFactual_f_fin = $oEncargoSacd->getF_fin();
            $oFactual_f_ini = $oEncargoSacd->getF_ini();
            if ($oFactual_f_fin == $this->getF_fin() || $oFactual_f_ini == $this->getF_ini()) {
                $oEncargoSacd->setF_fin(NULL);
                if ($oEncargoSacd->DBGuardar() === false) {
                    echo _("hay un error, no se ha guardado");
                }
                $flag=1; 
            }
            if (empty($actual_f_fin)) { $flag=1; }
        }
        if (empty($flag)) { //nuevo
            $oEncargoSacd = new EncargoSacd();
            $oEncargoSacd->setId_enc($id_enc);
            $oEncargoSacd->setId_nom($id_sacd);
            $oEncargoSacd->setModo($modo);
            $oEncargoSacd->setF_ini($this->getF_ini());
            $oEncargoSacd->setF_fin(NULL);
            if ($oEncargoSacd->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
            }
        }
        $oEncargoSacd->DBCarregar();
        return $oEncargoSacd;
    }

    function finalizar_sacd($id_enc,$id_sacd,$modo,$f_fin){
        $GesEncargoSacd = new GestorEncargoSacd();
        $cEncargosSacd = $GesEncargoSacd->getEncargosSacd(array('id_enc'=>$id_enc,'id_nom'=>$id_sacd,'modo'=>$modo));
        foreach ($cEncargosSacd as $oEncargoSacd) { // aunque sólo debería haber una.
            $oEncargoSacd->DBCarregar();
            $oEncargoSacd->setF_fin($f_fin);
            if ($oEncargoSacd->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
            }
        }
    }
    function delete_sacd($id_enc,$id_sacd,$modo){
        $GesEncargoSacd = new GestorEncargoSacd();
        $cEncargosSacd = $GesEncargoSacd->getEncargosSacd(array('id_enc'=>$id_enc,'id_nom'=>$id_sacd,'modo'=>$modo));
        foreach ($cEncargosSacd as $oEncargoSacd) { // aunque sólo debería haber una.
            if ($oEncargoSacd->DBEliminar() === false) {
                echo _("hay un error, no se ha eliminado");
            }
        }
    }

    function crear_encargo($id_tipo_enc,$sf_sv,$id_ubi,$id_zona,$desc_enc,$idioma_enc,$desc_lugar,$observ) {
        $oEncargo = new Encargo();
        $oEncargo->setId_tipo_enc($id_tipo_enc);
        $oEncargo->setSf_sv($sf_sv);
        $oEncargo->setId_ubi($id_ubi);
        $oEncargo->setId_zona($id_zona);
        $oEncargo->setDesc_enc($desc_enc);
        $oEncargo->setIdioma_enc($idioma_enc);
        $oEncargo->setDesc_lugar($desc_lugar);
        $oEncargo->setObserv($observ);
        if ($oEncargo->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
        }

        return $oEncargo->getId_enc();
    }

    // -------------------------------------------- complejo ----------------------------

    function calcular_dia($mas_menos,$dia_ref,$dia_inc) {
        $dia = empty($mas_menos)? $dia_ref: '';
        if (!empty($dia_inc) && empty($dia)) {
            if ($mas_menos=="-") { $dia=$dia_ref-$dia_inc; if ($dia<0) { $dia=7+$dia; } } 
            if ($mas_menos=="+") { $dia=$dia_ref+$dia_inc; if ($dia>7) { $dia=$dia-7; } } 
        }
        return $dia;
    }

    function db_txt_h_sacd($id_enc,$id_nom) {
        $oDB = $_SESSION['oDB'];
        $sql="SELECT * FROM t_horario_sacd WHERE id_enc=$id_enc AND id_nom=$id_nom";
        $oDBSt_q_h=$oDB->query($sql);
        $txt="";
        $h=0;
        foreach ($oDBSt_q_h->fetchAll() as $row_h) {
        $h++;
        extract($row_h);
        if ($h>1) $txt.=" "._("y")." ";
        $txt.=$this->texto_horario($mas_menos,$dia_ref,$dia_inc,$dia_num,$h_ini,$h_fin,$n_sacd);
        }
        return $txt;
    }

    function texto_horario($mas_menos,$dia_ref,$dia_inc,$dia_num,$h_ini,$h_fin,$n_sacd='') {
        $texto_horario = '';
        // texto que describe el horario orgiginal
        $dia_txt='';
        $dia=$this->calcular_dia($mas_menos,$dia_ref,$dia_inc);
        if (empty($mas_menos)) {
            if (!empty($dia_num)) {
                $dia_txt=_("el")." ".EncargoConstants::OPCIONES_ORDINALES[$dia_num]." ";
            }
            $dia_txt .= EncargoConstants::OPCIONES_DIA_SEMANA[$dia];
        } else {
            if ($mas_menos=="-") {
                $dia_txt = EncargoConstants::OPCIONES_DIA_SEMANA[$dia]." ". _("antes del")." ".EncargoConstants::OPCIONES_ORDINALES[$dia_num]." ".EncargoConstants::OPCIONES_DIA_REF[$dia_ref];
            }
            if ($mas_menos=="+") {
                $dia_txt = EncargoConstants::OPCIONES_DIA_SEMANA[$dia]." ". _("después del")." ".EncargoConstants::OPCIONES_ORDINALES[$dia_num]." ".EncargoConstants::OPCIONES_DIA_REF[$dia_ref];
            }
        }
        if (!empty($dia_txt)) $texto_horario=$dia_txt.", de ".$h_ini. " a ".$h_fin;
        if (!empty($n_sacd)) $texto_horario.=" (".$n_sacd." sacd)";

        return $texto_horario;
    }

    function texto_horario_ex($mes,$f_ini,$f_fin,$horario,$mas_menos,$dia_ref,$dia_inc,$dia_num,$h_ini,$h_fin,$n_sacd) {
        //igual que la anterior para las excepciones
        if (!empty($mes)) {
        $txt=sprintf(_("excepto el mes de %s"),EncargoConstants::OPCIONES_MES[$mes]);
        } else {
        $txt=sprintf(_("excpeto del %s al %s"),$f_ini,$f_fin);
        }

        if ($horario=="t") {
        $texto_h=$this->texto_horario($mas_menos,$dia_ref,$dia_inc,$dia_num,$h_ini,$h_fin,$n_sacd);
        $txt.= " "._("que se cambia a").": ".$texto_h; 
        } else {
        $txt.= " "._("que se anula");
        }
        return $txt;
    }

}
