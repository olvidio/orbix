<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace web;

use core;

/**
 * Description of botonescurso
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class BotonesCurso
{

    private $aWhere;
    private $aOperator;
    private $modo_curso;
    private $chk_1;
    private $chk_2;
    private $chk_3;


    public function __construct($modo_curso)
    {
        $this->modo_curso = $modo_curso;
        $this->getDades();
    }

    public function getWhere()
    {
        return $this->aWhere;
    }

    public function getOperator()
    {
        return $this->aOperator;
    }

    public function getDades()
    {
        /* Pongo en la variable $curso el periodo del curso */
        $mes = date('m');
        $fin_m = $_SESSION['oConfig']->getMesFinStgr();
        if ($mes > $fin_m) {
            $any = date('Y') + 1;
        } else {
            $any = date('Y');
        }
        $inicurs_ca = core\curso_est("inicio", $any)->format('Y-m-d');
        $fincurs_ca = core\curso_est("fin", $any)->format('Y-m-d');

        $this->aWhere = array();
        $this->aOperator = array();
        $this->aWhere['_ordre'] = 'f_ini';

        switch ($this->modo_curso) {
            case 2 :
                $this->chk_1 = "";
                $this->chk_2 = "checked";
                $this->chk_3 = "";
                $this->aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $this->aOperator['f_ini'] = 'BETWEEN';
                break;
            case 3:
                $this->chk_1 = "";
                $this->chk_2 = "";
                $this->chk_3 = "checked";
                break;
            case 1:
            default:
                $this->chk_1 = "checked";
                $this->chk_2 = "";
                $this->chk_3 = "";
                $this->aWhere['status'] = \actividades\model\entity\ActividadAll::STATUS_ACTUAL;
                $this->aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $this->aOperator['f_ini'] = 'BETWEEN';
                break;
        }
    }

    public function getRadioHtml()
    {
        $html = "<input type='Radio' id='modo_curso_1' name='modo_curso' value=1 $this->chk_1 onclick=fnjs_actualizar(this.form)>";
        $html .= ucfirst(_("actuales"));
        $html .= "<input type='Radio' id='modo_curso_2' name='modo_curso' value=2 $this->chk_2 onclick=fnjs_actualizar(this.form)>";
        $html .= ucfirst(_("todas las de este curso"));
        $html .= "<input type='Radio' id='modo_curso_3' name='modo_curso' value=3 $this->chk_3 onclick=fnjs_actualizar(this.form)>";
        $html .= ucfirst(_("todos los cursos"));

        return $html;
    }
}
