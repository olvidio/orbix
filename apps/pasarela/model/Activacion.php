<?php

namespace pasarela\model;

use pasarela\model\entity\PasarelaConfig;
use web\TiposActividades;
use stdClass;

class Activacion
{
    const PARAMETRO = 'fecha_activacion';

    private $default;
    private $a_excepciones;

    public function __construct()
    {
        $this->get();
    }

    public function delActivacion($id_tipo_activ)
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addActivacion($id_tipo_activ, $activacion)
    {
        $this->a_excepciones[$id_tipo_activ] = $activacion;
        $this->guardar();
    }

    public function getLista()
    {
        $html = '<table>';
        $html .= "<tr><td>"._("por defecto")."</td><td>";
        $html .= "<span class=\"link\" onCLick=\"fnjs_modificar_default()\" >";
        $html .= "$this->default</span></td></tr>";
        $html .= '</table><table>';
        foreach ($this->a_excepciones as $id_tipo_activ => $activacion) {
            $oActividadTipo = new TiposActividades($id_tipo_activ);
            $tipo_txt = $oActividadTipo->getNom();

            $html .= "<tr><td>$tipo_txt</td><td>";
            $html .= "<span class=\"link\" onCLick=\"fnjs_modificar($id_tipo_activ,'$activacion')\" >";
            $html .= "$activacion</span></td></tr>";
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
        $this->default = $default;
        $this->guardar();
    }

    public function getDefault()
    {
        return $this->default;
    }

    private function get()
    {
        $oPasarelaConfig = new PasarelaConfig(self::PARAMETRO);
        $json_activacion = $oPasarelaConfig->getJson_valor();
        if (empty((array)$json_activacion)) {
            $this->default = '3 días';
            $this->a_excepciones = [111000 => 'upload', 111001 => '5 días'];
        } else {
            $activacion = json_decode($json_activacion);
            $aaa = $activacion->excepciones;
            $this->a_excepciones = (array)$aaa;
            $this->default = $activacion->default;
        }

    }

    private function guardar()
    {
        // tipo json: {'default':'4 dias','excepciones': [111000:'upload', 111001: '3 dias' ...]}
        // pasarlo a json:
        $activacion = new stdClass();
        // default
        $activacion->default = $this->default;
        // excepciones
        $activacion->excepciones = $this->a_excepciones;
        // guardarlo
        $oPasarelaConfig = new PasarelaConfig();
        $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        $oPasarelaConfig->setJson_valor($activacion);
        $oPasarelaConfig->DBGuardar();
    }
}