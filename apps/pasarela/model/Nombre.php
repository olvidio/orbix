<?php

namespace pasarela\model;

use pasarela\model\entity\GestorPasarelaConfig;
use pasarela\model\entity\PasarelaConfig;
use stdClass;
use web\TiposActividades;

class Nombre
{
    const PARAMETRO = 'nombre';

    private $a_excepciones;

    public function __construct()
    {
        $this->get();
    }

    public function delNombre($id_tipo_activ)
    {
        unset($this->a_excepciones[$id_tipo_activ]);
        $this->guardar();
    }

    public function addNombre($id_tipo_activ, $nombre_actividad)
    {
        $this->a_excepciones[$id_tipo_activ] = $nombre_actividad;
        $this->guardar();
    }

    public function getLista()
    {
        $html = '<table>';
        foreach ($this->a_excepciones as $id_tipo_activ => $nom_tipo) {
            $oActividadTipo = new TiposActividades($id_tipo_activ);
            $tipo_txt = $oActividadTipo->getNom();

            $html .= "<tr><td>$tipo_txt</td><td>";
            $html .= "<span class=\"link\" onCLick=\"fnjs_modificar($id_tipo_activ,'$nom_tipo')\" >";
            $html .= "$nom_tipo</span></td></tr>";
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

    private function get()
    {
        $oPasarelaConfig = new PasarelaConfig(self::PARAMETRO);
        $json_nombres = $oPasarelaConfig->getJson_valor();
        if (empty((array)$json_nombres)) {
            $this->a_excepciones = [111000 => 'prova1', 111001 => 'prova2'];
        } else {
            $nombres = json_decode($json_nombres);
            $aaa = $nombres->excepciones;
            $this->a_excepciones = (array)$aaa;
        }

    }

    private function guardar()
    {
        // tipo json: {'excepciones': [111000:'upload', 111001: '3 dias' ...]}
        // pasarlo a json:
        $nombres = new stdClass();
        // excepciones
        $nombres->excepciones = $this->a_excepciones;
        // guardarlo
        $oPasarelaConfig = new PasarelaConfig();
        $oPasarelaConfig->setNom_parametro(self::PARAMETRO);
        $oPasarelaConfig->setJson_valor($nombres);
        $oPasarelaConfig->DBGuardar();
    }
}