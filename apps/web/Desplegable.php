<?php

namespace web;

class Desplegable
{
    protected $aPrimary_key;
    protected $sNombre;
    protected $oOpciones;
    protected $sOpcion_sel;
    protected $aOpcion_no;
    protected $bBlanco;
    protected $valorBlanco;
    protected $sAction;
    protected $iSize;
    protected $bMultiple;
    protected $iTabIndex;
    protected $sClase;
    protected $aChecked;

    /* CONSTRUCTOR ------------------------------ */
    function __construct($sNombre = '', $oOpciones = '', $sOpcion_sel = '', $bBlanco = '')
    {
        if (is_array($sNombre)) { //le puedo pasar los parámetros que quiera por el array
            $this->aPrimary_key = $sNombre;
            foreach ($sNombre as $nom_id => $val_id) {
                if ($val_id !== '') $this->$nom_id = $val_id;
            }
        } else {
            if (isset($sNombre) && $sNombre !== '') $this->sNombre = $sNombre;
            if (isset($oOpciones) && $oOpciones !== '') $this->oOpciones = $oOpciones;
            if (isset($sOpcion_sel) && $sOpcion_sel !== '') $this->sOpcion_sel = $sOpcion_sel;
            if (isset($bBlanco) && $bBlanco !== '') $this->bBlanco = $bBlanco;
        }
    }

    public function export()
    {
        $multiple = $this->bMultiple ?? false;
        $size = $this->iSize ?? 0;
        $clase = $this->sClase ?? '';
        $action = $this->sAction ?? '';
        $nombre = $this->sNombre ?? '';
        $blanco = $this->bBlanco ?? false;
        $valorBlanco = $this->valorBlanco ?? '';
        $opcion_sel = $this->sOpcion_sel?? '';
        $options = $this->getArrayOpciones();


        return [
            'multiple' => $multiple,
            'size' => $size,
            'clase' => $clase,
            'action' => $action,
            'nombre' => $nombre,
            'blanco' => $blanco,
            'valorBlanco' => $valorBlanco,
            'opcion_sel' => $opcion_sel,
            'options' => $options,
        ];
    }

    public function import($data)
    {
        $this->bMultiple = $data['multiple'] ?? false;
        $this->iSize = $data['size'];
        $this->sClase = $data['clase'];
        $this->sAction = $data['action'];
        $this->sNombre = $data['nombre'];
        $this->bBlanco = $data['blanco'];
        $this->valorBlanco = $data['valorBlanco'];
        $this->sOpcion_sel = $data['opcion_sel'];
        $this->oOpciones = $data['options'];
    }

    public function desplegable()
    {
        $multiple = empty($this->bMultiple) ? '' : 'multiple';
        $tab_index = empty($this->iTabIndex) ? '' : 'tabindex="' . $this->iTabIndex . '"';
        $size = empty($this->iSize) ? '' : 'size="' . $this->iSize . '"';
        $clase = empty($this->sClase) ? '' : 'class="' . $this->sClase . '"';
        if (empty($this->sAction)) {
            $sHtml = "<select $multiple $tab_index id=\"$this->sNombre\" name=\"$this->sNombre\" $clase $size>";
        } else {
            $sHtml = "<select $multiple $tab_index id=\"$this->sNombre\" name=\"$this->sNombre\" $clase $size onChange=\"$this->sAction\" >";
        }
        $sHtml .= $this->options();
        $sHtml .= '</select>';
        return $sHtml;
    }

    public function cuadros_check()
    {

        $txt = '';

        $camp = $this->sNombre . "[]";

        if (is_object($this->oOpciones)) {
            $this->oOpciones->execute();
            foreach ($this->oOpciones as $row) {
                if (!isset($row[1])) {
                    $a = 0;
                } else {
                    $a = 1;
                } // para el caso de sólo tener un valor.
                if (in_array($row[0], $this->aChecked)) {
                    $chk = 'checked';
                } else {
                    $chk = '';
                }

                if (!empty($this->aOpcion_no) && is_array($this->aOpcion_no) && in_array($row[0], $this->aOpcion_no)) continue;
                $txt .= "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"$row[0]\" $chk>$row[$a]";
            }
        } else if (is_array($this->oOpciones)) {
            reset($this->oOpciones);
            foreach ($this->oOpciones as $key => $val) {
                if (in_array($key, $this->aChecked)) {
                    $chk = 'checked';
                } else {
                    $chk = '';
                }

                if (!empty($this->aOpcion_no) && is_array($this->aOpcion_no) && in_array($row[0], $this->aOpcion_no)) continue;
                $txt .= "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"$key\" $chk>$val";
            }
        } else {
            $msg_err = _("tiene que ser un array") . ": " . __FILE__ . ": line " . __LINE__;
            exit ($msg_err);
        }

        return $txt;
    }

    public function getArrayOpciones()
    {
        $a_options = [];
        if (is_object($this->oOpciones)) {
            $this->oOpciones->execute();
            foreach ($this->oOpciones as $row) {
                if (!isset($row[1])) {
                    $a = 0;
                } else {
                    $a = 1;
                }

                $a_options[$row[0]] = $row[$a];
            }
        } elseif (is_array($this->oOpciones)) {
            $a_options = $this->oOpciones;
        }
        return $a_options;
    }

    public function options()
    {
        $txt = '';
        if (!empty($this->bBlanco)) {
            if (!empty($this->valorBlanco)) {
                $txt .= "<option value=\"$this->valorBlanco\"></option>";
            } else {
                $txt .= '<option></option>';
            }
        }
        $a_opciones = $this->getArrayOpciones();
        reset($a_opciones);
        foreach ($a_opciones as $key => $val) {
            if ((string)$key === (string)$this->sOpcion_sel) {
                $sel = 'selected';
            } else {
                $sel = '';
            }
            if (!empty($this->aOpcion_no) && is_array($this->aOpcion_no) && in_array($key, $this->aOpcion_no)) continue;
            $txt .= "<option value=\"$key\" $sel>$val</option>";
        }
        return $txt;
    }

    /*
    public function options_old()
    {
        $txt = '';
        if (!empty($this->bBlanco)) {
            if (!empty($this->valorBlanco)) {
                $txt .= "<option value=\"$this->valorBlanco\"></option>";
            } else {
                $txt .= '<option></option>';
            }
        }
        if (is_object($this->oOpciones)) {
            $this->oOpciones->execute();
            foreach ($this->oOpciones as $row) {
                if (!isset($row[1])) {
                    $a = 0;
                } else {
                    $a = 1;
                } // para el caso de sólo tener un valor.
                if ($row[0] == $this->sOpcion_sel) {
                    $sel = 'selected';
                } else {
                    $sel = '';
                }
                if (!empty($this->aOpcion_no) && is_array($this->aOpcion_no) && in_array($row[0], $this->aOpcion_no)) continue;
                $txt .= "<option value=\"$row[0]\" $sel>$row[$a]</option>";
            }
        } else if (is_array($this->oOpciones)) {
            reset($this->oOpciones);
            foreach ($this->oOpciones as $key => $val) {
                if ((string)$key === (string)$this->sOpcion_sel) {
                    $sel = 'selected';
                } else {
                    $sel = '';
                }
                if (!empty($this->aOpcion_no) && is_array($this->aOpcion_no) && in_array($row[0], $this->aOpcion_no)) continue;
                $txt .= "<option value=\"$key\" $sel>$val</option>";
            }
        } else {
            $msg_err = _("tiene que ser un array") . ": " . __FILE__ . ": line " . __LINE__;
            exit ($msg_err);
        }
        return $txt;
    }
    */

    public function setNombre($sNombre)
    {
        $this->sNombre = $sNombre;
    }

    public function setOpciones($aOpciones)
    {
        $this->oOpciones = $aOpciones;
    }

    public function getOpciones()
    {
        return $this->oOpciones;
    }

    public function setOpcion_sel($sOpcion_sel)
    {
        $this->sOpcion_sel = $sOpcion_sel;
    }

    public function setChecked($aChecked)
    {
        $this->aChecked = $aChecked;
    }

    public function setOpcion_no($aOpcion_no)
    {
        $this->aOpcion_no = $aOpcion_no;
    }

    public function setBlanco($bBlanco)
    {
        $this->bBlanco = $bBlanco;
    }

    public function setValBlanco($valorBlanco)
    {
        $this->valorBlanco = $valorBlanco;
    }

    public function setAction($sAction)
    {
        $this->sAction = $sAction;
    }

    public function setSize($iSize)
    {
        $this->iSize = $iSize;
    }

    public function setMultiple($bMultiple)
    {
        $this->bMultiple = $bMultiple;
    }

    public function setClase($sClase)
    {
        $this->sClase = $sClase;
    }
}

