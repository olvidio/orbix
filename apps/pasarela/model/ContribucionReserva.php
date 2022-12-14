<?php

namespace pasarela\model;

use pasarela\model\entity\GestorPasarelaConfig;
use pasarela\model\entity\PasarelaConfig;
use stdClass;
use web\TiposActividades;

class ContribucionReserva
{
    const PARAMETRO = 'contribucion_reserva';

    private $default;
    private $a_excepciones;

    public function __construct()
    {
        $this->get();
    }

    public function delContribucionReserva($id_tipo_activ)
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addContribucionReserva($id_tipo_activ, $contribucion_reserva)
    {
        $this->a_excepciones[$id_tipo_activ] = $contribucion_reserva;
        $this->guardar();
    }

    public function getLista()
    {
        $html = '<table>';
        $html .= "<tr><td>"._("por defecto")."</td><td>";
        $html .= "<span class=\"link\" onCLick=\"fnjs_modificar_default()\" >";
        $html .= "$this->default</span></td></tr>";
        $html .= '</table><table>';
        foreach ($this->a_excepciones as $id_tipo_activ => $contribucion_reserva) {
            $oActividadTipo = new TiposActividades($id_tipo_activ);
            $tipo_txt = $oActividadTipo->getNom();

            $html .= "<tr><td>$tipo_txt</td><td>";
            $html .= "<span class=\"link\" onCLick=\"fnjs_modificar($id_tipo_activ,'$contribucion_reserva')\" >";
            $html .= "$contribucion_reserva</span></td></tr>";
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
        $json_contribucion_reserva = $oPasarelaConfig->getJson_valor();
        if (empty((array)$json_contribucion_reserva)) {
            $this->default = 0;
            $this->a_excepciones = [111000 => 45, 111001 => 63];
        } else {
            $contribucion_reserva = json_decode($json_contribucion_reserva);
            $aaa = $contribucion_reserva->excepciones;
            $this->a_excepciones = (array)$aaa;
            $this->default = $contribucion_reserva->default;
        }

    }

    private function guardar()
    {
        // tipo json: {'default':0,'excepciones': [111000:45, 111001: 63 ...]}
        // pasarlo a json:
        $contribucion_reserva = new stdClass();
        // default
        $contribucion_reserva->default = $this->default;
        // excepciones
        $contribucion_reserva->excepciones = $this->a_excepciones;
        // guardarlo
        $oPasarelaConfig = new PasarelaConfig();
        $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        $oPasarelaConfig->setJson_valor($contribucion_reserva);
        $oPasarelaConfig->DBGuardar();
    }
}