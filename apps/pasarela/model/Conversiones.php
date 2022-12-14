<?php

namespace pasarela\model;


use actividades\model\entity\GestorTipoDeActividad;
use web\TiposActividades;

/**
 * Classe
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 7/5/2019
 */
class Conversiones
{

    /**
     * colección de objetos TipoActividad
     *
     * @var array
     */
    private $c_tipos_activ = [];

    /**
     * array con los nombres (cuadrienio, bienio...) para cada id_tipo actividad
     *
     * @var array
     */
    private $a_tipos_nom = [];

    /**
     * array con los nombres tipo actividad (crt, ca, cv...) para cada id_tipo actividad
     * (1 digito)
     *
     * @var array
     */
    private $a_tipos_activ1 = [];

    private $a_tipos_asistentes = [];
    private $a_tipos_activacion = [];
    private $a_tipos_contribucion_no_duerme = [];
    private $a_tipos_contribucion_reserva = [];

    public function getArrayContribucionReserva(): array
    {
        $oContribucionReserva = new ContribucionReserva();
        $default = $oContribucionReserva->getDefault();
        $a_excepciones = $oContribucionReserva->getExcepciones();
        $a_tipos = $this->getArrayTipos_contribucion_reserva($default);

        return array_replace($a_tipos, $a_excepciones);
    }

    public function getArrayContribucionNoDuerme(): array
    {
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $default = $oContribucionNoDuerme->getDefault();
        $a_excepciones = $oContribucionNoDuerme->getExcepciones();
        $a_tipos = $this->getArrayTipos_contribucion_no_duerme($default);

        return array_replace($a_tipos, $a_excepciones);
    }

    public function getArrayActivacion(): array
    {
        $oActivacion = new Activacion();
        $default = $oActivacion->getDefault();
        $a_excepciones = $oActivacion->getExcepciones();
        $a_tipos = $this->getArrayTipos_activacion($default);

        return array_replace($a_tipos, $a_excepciones);
    }

    public function getArrayPerfil(): array
    {
        // no hay excepciones
        return $this->getArrayTipos_asistentes();
    }

    public function getArrayNombre(): array
    {
        $a_tipos = $this->getArrayTipos_nombre();
        $oNombre = new Nombre();
        $a_excepciones = $oNombre->getExcepciones();

        return array_replace($a_tipos, $a_excepciones);
    }

    public function getArrayTipo(): array
    {
        // no hay excepciones
        return $this->getArrayTipos_actividad();
    }

    private function getArrayTipos_contribucion_reserva($default): array
    {
        $this->getcTiposDeActividades();
        if (empty($this->a_tipos_contribucion_reserva)) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $a_tipos[$id_tipo_activ] = $default;
            }
            $this->a_tipos_contribucion_reserva = $a_tipos;
        }
        return $this->a_tipos_contribucion_reserva;
    }

    private function getArrayTipos_contribucion_no_duerme($default): array
    {
        $this->getcTiposDeActividades();
        if (empty($this->a_tipos_contribucion_no_duerme)) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $a_tipos[$id_tipo_activ] = $default;
            }
            $this->a_tipos_contribucion_no_duerme = $a_tipos;
        }
        return $this->a_tipos_contribucion_no_duerme;
    }

    private function getArrayTipos_activacion($default): array
    {
        $this->getcTiposDeActividades();
        if (empty($this->a_tipos_activacion)) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $a_tipos[$id_tipo_activ] = $default;
            }
            $this->a_tipos_activacion = $a_tipos;
        }
        return $this->a_tipos_activacion;
    }


    private function getArrayTipos_asistentes(): array
    {
        $this->getcTiposDeActividades();
        if (empty($this->a_tipos_asistentes)) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $oTiposActividades = new TiposActividades($id_tipo_activ, TRUE);
                switch ($oTiposActividades->getAsistentesText()) {
                    case 'sg';
                        $a_tipos[$id_tipo_activ] = _("CP/AMIG");
                        break;
                    case 'sss+';
                        $a_tipos[$id_tipo_activ] = _("SACD");
                        break;
                    case 'sr';
                    case 'sr-nax';
                    case 'sr-agd';
                        $a_tipos[$id_tipo_activ] = _("SR");
                        $nom_asistentes = $oTiposActividades->getActividad2DigitosText();
                        if (strpos($nom_asistentes, 'univ') !== FALSE) {
                            $a_tipos[$id_tipo_activ] = _("SR-UNIV");
                        }
                        if (strpos($nom_asistentes, 'bach') !== FALSE) {
                            $a_tipos[$id_tipo_activ] = _("SR-BACH");
                        }
                        break;
                    default:
                        $a_tipos[$id_tipo_activ] = strtoupper($oTiposActividades->getAsistentesText());
                }
            }
            $this->a_tipos_asistentes = $a_tipos;
        }
        return $this->a_tipos_asistentes;
    }

    private function getArrayTipos_actividad(): array
    {
        $this->getcTiposDeActividades();
        if (empty($this->a_tipos_activ1)) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $oTiposActividades = new TiposActividades($id_tipo_activ);
                if ($oTiposActividades->getActividadText() === 'crt') {
                    $a_tipos[$id_tipo_activ] = _("curso retiro");
                } else {
                    $a_tipos[$id_tipo_activ] = _("convivencia");
                }
            }
            $this->a_tipos_activ1 = $a_tipos;
        }
        return $this->a_tipos_activ1;
    }

    private function getArrayTipos_nombre(): array
    {
        $this->getcTiposDeActividades();
        if (empty($this->a_tipos_nom)) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $oTiposActividades = new TiposActividades($id_tipo_activ);

                $a_tipos[$id_tipo_activ] = $oTiposActividades->getNomPasarela();
            }
            $this->a_tipos_nom = $a_tipos;
        }
        return $this->a_tipos_nom;
    }

    private function getcTiposDeActividades(): array
    {
        if (empty($this->c_tipos_activ)) {
            $aWhere = ['_ordre' => 'id_tipo_activ'];
            $oGesTiposDeActividades = new GestorTipoDeActividad();
            $cTiposDeActividades = $oGesTiposDeActividades->getTiposDeActividades($aWhere);
            $this->c_tipos_activ = $cTiposDeActividades;
        }
        return $this->c_tipos_activ;
    }
}