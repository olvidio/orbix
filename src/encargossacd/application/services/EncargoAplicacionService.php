<?php

namespace src\encargossacd\application\services;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class EncargoAplicacionService
{
    protected $a_txt = [];

    public function getArrayTraducciones($idioma)
    {
        $idioma = empty($idioma) ? 'es' : $idioma;
        if (empty($this->a_txt[$idioma])) {
            $EncargoTextoRepository = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);
            $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos();
            foreach ($cEncargoTextos as $oEncargoTexto) {
                $clave = $oEncargoTexto->getClaveVo()->value();
                $idioma_x = $oEncargoTexto->getIdiomaVo()->value();
                $texto = $oEncargoTexto->getTextoVo()->value();
                $this->a_txt[$idioma_x][$clave] = $texto;
            }
        }
        if (empty($this->a_txt[$idioma])) {
            $rta = sprintf(_("No existe text para el idioma: %s"), $idioma);
        } else {
            $rta = $this->a_txt[$idioma];
        }
        return $rta;
    }

    public function getTraduccion($clave, $idioma)
    {
        $a_traduccion = $this->getArrayTraducciones($idioma);
        if (!empty($a_traduccion[$clave])) {
            $txt_traduccion = $a_traduccion[$clave];
        } else {
            // El idioma por defecto (es) debería existir siempre
            $a_traduccion = $this->getArrayTraducciones('es');
            if (!empty($a_traduccion[$clave])) {
                $txt_traduccion = $a_traduccion[$clave];
            } else {
                echo sprintf(_("falta definir el texto %s en este idioma: %s"), $clave, $idioma);
            }
        }
        return $txt_traduccion;
    }

    public function getLugar_dl()
    {
        $oGesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $oGesCentrosDl->getCentros(['tipo_ctr' => 'dl']);
        $num_dl = count($cCentros);
        switch ($num_dl) {
            case 0:
                // Puede ser el nombre de la región
                $cCentros = $oGesCentrosDl->getCentros(['tipo_ctr' => 'cr']);
                if (!empty($cCentros)) {
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

    public function getF_ini()
    {
        return new DateTimeLocal(date('Y-m-d')); // sólo el dia, sin la hora
    }

    public function getF_fin()
    {
        return new DateTimeLocal(date('Y-m-d')); // sólo el dia, sin la hora
    }

    public function getArraySeccion()
    {
        if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
            $array_seccion = [
                '1' => "sv",
                '2' => "sf",
                '3' => "sss+",
                '4' => "igl",
                '5' => "cgi/oc",
                '8' => "zonas",
            ];
        } else {
            $array_seccion = [
                '1' => "sv",
                '3' => "sss+",
                '4' => "igl",
                '5' => "cgi/oc",
                '8' => "zonas",
            ];
        }
        return $array_seccion;
    }

    public function getTxtDedicacion($cEncargoHorarios, $idioma = '')
    {
        $dedicacion_m_txt = "";
        $dedicacion_t_txt = "";
        $dedicacion_v_txt = "";
        foreach ($cEncargoHorarios as $oEncargoHorario) {
            $dia_ref = $oEncargoHorario->getDiaRefVo()->value();
            $dia_inc = $oEncargoHorario->getDia_inc();
            switch ($dia_ref) {
                case "m":
                    if ($dia_inc > 1) {
                        $txt = $this->getTraduccion('t_mañana', $idioma);
                        $dedicacion_m_txt = $dia_inc . " " . $txt;
                    } else {
                        $txt = $this->getTraduccion('t_mañanas', $idioma);
                        $dedicacion_m_txt = $dia_inc . " " . $txt;
                    }
                    break;
                case "t":
                    if ($dia_inc > 1) {
                        $txt = $this->getTraduccion('t_tarde1', $idioma);
                        $dedicacion_t_txt = $dia_inc . " " . $txt;
                    } else {
                        $txt = $this->getTraduccion('t_tardes1', $idioma);
                        $dedicacion_t_txt = $dia_inc . " " . $txt;
                    }
                    break;
                case "v":
                    if ($dia_inc > 1) {
                        $txt = $this->getTraduccion('t_tarde2', $idioma);
                        $dedicacion_v_txt = $dia_inc . " " . $txt;
                    } else {
                        $txt = $this->getTraduccion('t_tardes2', $idioma);
                        $dedicacion_v_txt = $dia_inc . " " . $txt;
                    }
                    break;
            }
        }
        $dedicacion_txt = "($dedicacion_m_txt, $dedicacion_t_txt, $dedicacion_v_txt)";
        $dedicacion_txt = str_replace(", , ", ", ", $dedicacion_txt);
        $dedicacion_txt = str_replace("(, ", "(", $dedicacion_txt);
        $dedicacion_txt = str_replace(", )", ")", $dedicacion_txt);
        if ($dedicacion_txt === "()") $dedicacion_txt = "";

        return $dedicacion_txt;
    }

    public function dedicacion_ctr($id_ubi, $id_enc, $idioma = '')
    {
        $EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        $aWhere['id_enc'] = $id_enc;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoHorarios = $EncargoHorarioRepository->getEncargoHorarios($aWhere, $aOperador);

        if (is_array($cEncargoHorarios) && count($cEncargoHorarios) == 0) {
            return false;
        }

        $dedicacion_txt = $this->getTxtDedicacion($cEncargoHorarios, $idioma);
        return $dedicacion_txt;
    }

    public function dedicacion($id_nom, $id_enc, $idioma = '')
    {
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        if (is_array($cEncargoSacdHorario) && count($cEncargoSacdHorario) == 0) {
            return false;
        }

        $dedicacion_txt = $this->getTxtDedicacion($cEncargoSacdHorario, $idioma);
        return $dedicacion_txt;
    }

    public function insert_horario_ctr($id_enc, $modulo, $dedicacion, $n_sacd)
    {
        if (empty($n_sacd)) {
            $n_sacd = 1;
        }
        $EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        $newId_item = $EncargoHorarioRepository->getNewId();
        $oEncargoHorario = new EncargoHorario();
        $EncargoHorarioRepository->setId_item_h($newId_item);
        $oEncargoHorario->setid_enc($id_enc);
        $oEncargoHorario->setF_ini($this->getF_ini());
        $oEncargoHorario->setF_fin(NULL);
        $oEncargoHorario->setDiaRefVo($modulo);
        $oEncargoHorario->setDia_inc($dedicacion);
        $oEncargoHorario->setN_sacd($n_sacd);
        if ($EncargoHorarioRepository->Guardar($oEncargoHorario) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $EncargoHorarioRepository->getErrorTxt();
        }
    }

    public function modificar_horario_ctr($id_enc, $modulo, $dedicacion, $n_sacd)
    {
        if (empty($n_sacd)) {
            $n_sacd = 1;
        }

        $EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        $aWhere['id_enc'] = $id_enc;
        $aWhere['dia_ref'] = $modulo;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoHorarios = $EncargoHorarioRepository->getEncargoHorarios($aWhere, $aOperador);
        if (is_array($cEncargoHorarios) && count($cEncargoHorarios) == 0) {
            if (!empty($dedicacion)) {
                $this->insert_horario_ctr($id_enc, $modulo, $dedicacion, $n_sacd);
            }
        } else {
            $oEncargoHoraio = $cEncargoHorarios[0];
            $dia_inc = $oEncargoHoraio->getDia_inc();
            $oEncargoHoraio->setDia_inc($dedicacion);
            $oEncargoHoraio->setN_sacd($n_sacd);
            if ($EncargoHorarioRepository->Guardar($oEncargoHoraio) === false) {
                echo _("hay un error, no se ha guardado");
            }

            if (!empty($dedicacion) && $dia_inc != $dedicacion) {
                $this->insert_horario_ctr($id_enc, $modulo, $dedicacion, $n_sacd);
            }
        }
    }

    public function insert_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, $modulo, $dedicacion)
    {
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $newId_item = $EncargoSacdHorarioRepository->getNewId();
        $oEncargoSacdHorario = new EncargoSacdHorario();
        $oEncargoSacdHorario->setId_item($newId_item);
        $oEncargoSacdHorario->setId_enc($id_enc);
        $oEncargoSacdHorario->setId_nom($id_nom);
        $oEncargoSacdHorario->setF_ini($this->getF_fin());
        $oEncargoSacdHorario->setF_fin(NULL);
        $oEncargoSacdHorario->setDiaRefVo($modulo);
        $oEncargoSacdHorario->setDia_inc($dedicacion);
        $oEncargoSacdHorario->setId_item_tarea_sacd($id_item_t_sacd);
        if ($EncargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $EncargoSacdHorarioRepository->getErrorTxt();
        }
    }

    public function finalizar_horario_sacd($id_enc, $id_nom, $f_fin)
    {
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        foreach ($cEncargoSacdHorario as $oEncargoSacdHorario) {
            $oEncargoSacdHorario->setF_fin($f_fin);
            if ($EncargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $EncargoSacdHorarioRepository->getErrorTxt();
            }
        }
    }

    public function modificar_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, $modulo, $dedicacion)
    {
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['dia_ref'] = $modulo;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        if (is_array($cEncargoSacdHorario) && empty($cEncargoSacdHorario)) {
            if (!empty($dedicacion)) {
                $this->insert_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, $modulo, $dedicacion);
            }
        } else {
            $oEncargoSacdHorario = $cEncargoSacdHorario[0];
            $dia_inc = $oEncargoSacdHorario->getDia_inc();
            if (!empty($dedicacion)) {
                if ($dia_inc != $dedicacion) {
                    if ($oEncargoSacdHorario->getF_ini() == $this->getF_ini()) {
                        $oEncargoSacdHorario->setDia_inc($dedicacion);
                        if ($EncargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                            echo _("hay un error, no se ha guardado");
                        }
                    } else {
                        $oEncargoSacdHorario->setF_fin($this->getF_fin());
                        if ($EncargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                            echo _("hay un error, no se ha guardado");
                        }
                        $this->insert_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, $modulo, $dedicacion);
                    }
                } else {
                    $oFactual_f_fin = $oEncargoSacdHorario->getF_fin();
                    if ($oFactual_f_fin == $this->getF_fin()) {
                        $oEncargoSacdHorario->setF_fin(NULL);
                        if ($EncargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                            echo _("hay un error, no se ha guardado");
                        }
                    }
                }
            } else {
                $oEncargoSacdHorario->setDia_inc(NULL);
                $oEncargoSacdHorario->setF_fin($this->getF_fin());
                if ($EncargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                    echo _("hay un error, no se ha guardado");
                }
            }
        }
    }

    public function insert_sacd($id_enc, $id_sacd, $modo)
    {
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(array('id_enc' => $id_enc, 'id_nom' => $id_sacd, 'modo' => $modo));
        $flag = 0;
        $oEncargoSacd = null;
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $oFactual_f_fin = $oEncargoSacd->getF_fin();
            $oFactual_f_ini = $oEncargoSacd->getF_ini();
            if ($oFactual_f_fin == $this->getF_fin() || $oFactual_f_ini == $this->getF_ini()) {
                $oEncargoSacd->setF_fin(NULL);
                if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $EncargoSacdRepository->getErrorTxt();
                }
                $flag = 1;
            }
            if (empty($oFactual_f_fin)) {
                $flag = 1;
            }
        }
        if (empty($flag)) {
            $newId_item = $EncargoSacdRepository->getNewId();
            $oEncargoSacd = new EncargoSacd();
            $oEncargoSacd->setId_item($newId_item);
            $oEncargoSacd->setId_enc($id_enc);
            $oEncargoSacd->setId_nom($id_sacd);
            $oEncargoSacd->setModo($modo);
            $oEncargoSacd->setF_ini($this->getF_ini());
            $oEncargoSacd->setF_fin(NULL);
            if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $EncargoSacdRepository->getErrorTxt();
            }
        }
        return $oEncargoSacd;
    }

    public function finalizar_sacd($id_enc, $id_sacd, $modo, $f_fin)
    {
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(array('id_enc' => $id_enc, 'id_nom' => $id_sacd, 'modo' => $modo));
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $oEncargoSacd->setF_fin($f_fin);
            if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $EncargoSacdRepository->getErrorTxt();
            }
        }
    }

    public function delete_sacd($id_enc, $id_sacd, $modo)
    {
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(array('id_enc' => $id_enc, 'id_nom' => $id_sacd, 'modo' => $modo));
        foreach ($cEncargosSacd as $oEncargoSacd) {
            if ($EncargoSacdRepository->Eliminar($oEncargoSacd) === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $EncargoSacdRepository->getErrorTxt();
            }
        }
    }

    public function crear_encargo($id_tipo_enc, $sf_sv, $id_ubi, $id_zona, $desc_enc, $idioma_enc, $desc_lugar, $observ)
    {
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $newId_enc = $EncargoRepository->getNewId();
        $oEncargo = new Encargo();
        $oEncargo->setId_enc($newId_enc);
        $oEncargo->setId_tipo_enc($id_tipo_enc);
        $oEncargo->setSf_sv($sf_sv);
        $oEncargo->setId_ubi($id_ubi);
        $oEncargo->setId_zona($id_zona);
        $oEncargo->setDesc_enc($desc_enc);
        $oEncargo->setIdioma_enc($idioma_enc);
        $oEncargo->setDesc_lugar($desc_lugar);
        $oEncargo->setObservVo($observ);
        if ($EncargoRepository->Guardar($oEncargo) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $EncargoRepository->getErrorTxt();
        }

        return $oEncargo->getId_enc();
    }
}
