<?php

namespace pasarela\model;

use pasarela\model\entity\GestorPasarelaConfig;
use pasarela\model\entity\PasarelaConfig;
use stdClass;
use web\TiposActividades;

class ContribucionNoDuerme
{
    const PARAMETRO = 'contribucion_no_duerme';

    private $default;
    private $a_excepciones;

    public function __construct()
    {
        $this->get();
    }

    public function delContribucionNoDuerme($id_tipo_activ)
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addContribucionNoDuerme($id_tipo_activ, $contribucion_no_duerme)
    {
        $this->a_excepciones[$id_tipo_activ] = $contribucion_no_duerme;
        $this->guardar();
    }

    public function getLista()
    {
        $html = '<table>';
        $html .= "<tr><td>"._("por defecto")."</td><td>";
        $html .= "<span class=\"link\" onCLick=\"fnjs_modificar_default()\" >";
        $html .= "$this->default</span></td></tr>";
        $html .= '</table><table>';
        foreach ($this->a_excepciones as $id_tipo_activ => $contribucion_no_duerme) {
            $oActividadTipo = new TiposActividades($id_tipo_activ);
            $tipo_txt = $oActividadTipo->getNom();

            $html .= "<tr><td>$tipo_txt</td><td>";
            $html .= "<span class=\"link\" onCLick=\"fnjs_modificar($id_tipo_activ,'$contribucion_no_duerme')\" >";
            $html .= "$contribucion_no_duerme</span></td></tr>";
        }
        $html .= '</table>';
        return $html;
    }

    public function setExcepciones($a_excepciones)
    {
        $this->a_excepciones = $a_excepciones;
    }

    public function getExcepciones(): array
    {
        return $this->a_excepciones;
    }

    public function setDefault($default)
    {
        $this->default = (int)$default;
        $this->guardar();
    }

    public function getDefault()
    {
        return $this->default;
    }

    private function get()
    {
        $oPasarelaConfig = new PasarelaConfig(self::PARAMETRO);
        $json_contribucion_no_duerme = $oPasarelaConfig->getJson_valor();
        if (empty((array)$json_contribucion_no_duerme)) {
            $this->default = 85;
            $this->a_excepciones = [111000 => 45, 111001 => 63];
        } else {
            $contribucion_no_duerme = json_decode($json_contribucion_no_duerme);
            $aaa = $contribucion_no_duerme->excepciones;
            $this->a_excepciones = (array)$aaa;
            $this->default = $contribucion_no_duerme->default;
        }

    }

    private function guardar()
    {
        // tipo json: {'default':85,'excepciones': [111000:45, 111001: 63 ...]}
        // pasarlo a json:
        $contribucion_no_duerme = new stdClass();
        // default
        $contribucion_no_duerme->default = $this->default;
        // excepciones
        $contribucion_no_duerme->excepciones = $this->a_excepciones;
        // guardarlo
        $oPasarelaConfig = new PasarelaConfig();
        $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        $oPasarelaConfig->setJson_valor($contribucion_no_duerme);
        $oPasarelaConfig->DBGuardar();
    }
}