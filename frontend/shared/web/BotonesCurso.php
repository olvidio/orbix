<?php

namespace frontend\shared\web;

use src\actividades\domain\value_objects\StatusId;
use function src\shared\domain\helpers\curso_est;

class BotonesCurso
{
    private array $aWhere = [];
    private array $aOperator = [];
    private $modo_curso;
    private string $chk_1 = '';
    private string $chk_2 = '';
    private string $chk_3 = '';

    public function __construct($modo_curso)
    {
        $this->modo_curso = $modo_curso;
        $this->getDades();
    }

    public function getWhere(): array
    {
        return $this->aWhere;
    }

    public function getOperator(): array
    {
        return $this->aOperator;
    }

    public function getDades(): void
    {
        $mes = date('m');
        $fin_m = $_SESSION['oConfig']->getMesFinStgr();
        $any = ($mes > $fin_m) ? date('Y') + 1 : date('Y');
        $inicurs_ca = curso_est("inicio", $any)->format('Y-m-d');
        $fincurs_ca = curso_est("fin", $any)->format('Y-m-d');

        $this->aWhere = [];
        $this->aOperator = [];
        $this->aWhere['_ordre'] = 'f_ini';

        switch ($this->modo_curso) {
            case 2 :
                $this->chk_2 = "checked";
                $this->aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $this->aOperator['f_ini'] = 'BETWEEN';
                break;
            case 3:
                $this->chk_3 = "checked";
                break;
            case 1:
            default:
                $this->chk_1 = "checked";
                $this->aWhere['status'] = StatusId::ACTUAL;
                $this->aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $this->aOperator['f_ini'] = 'BETWEEN';
                break;
        }
    }

    public function getRadioHtml(): string
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
