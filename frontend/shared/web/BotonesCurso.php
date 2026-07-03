<?php

namespace frontend\shared\web;

use frontend\actividades\helpers\ActividadStatusId;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use frontend\shared\helpers\FuncTablasSupport;
class BotonesCurso
{
    /** @var array<string, mixed> */
    private array $aWhere = [];

    /** @var array<string, string> */
    private array $aOperator = [];

    private int $modo_curso;

    private string $chk_1 = '';

    private string $chk_2 = '';

    private string $chk_3 = '';

    public function __construct(int|string $modo_curso)
    {
        $this->modo_curso = (int) $modo_curso;
        $this->getDades();
    }

    /**
     * @return array<string, mixed>
     */
    public function getWhere(): array
    {
        return $this->aWhere;
    }

    /**
     * @return array<string, string>
     */
    public function getOperator(): array
    {
        return $this->aOperator;
    }

    public function getDades(): void
    {
        $mes = (int) date('m');
        $finM = 6;
        $oConfig = $_SESSION['oConfig'] ?? null;
        if ($oConfig instanceof ConfigSnapshot) {
            $finM = $oConfig->getMesFinStgr();
        }
        $any = ($mes > $finM) ? (int) date('Y') + 1 : (int) date('Y');
        $inicurs_ca = FuncTablasSupport::cursoEst('inicio', $any)->format('Y-m-d');
        $fincurs_ca = FuncTablasSupport::cursoEst('fin', $any)->format('Y-m-d');

        $this->aWhere = [];
        $this->aOperator = [];
        $this->aWhere['_ordre'] = 'f_ini';

        switch ($this->modo_curso) {
            case 2:
                $this->chk_2 = 'checked';
                $this->aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $this->aOperator['f_ini'] = 'BETWEEN';
                break;
            case 3:
                $this->chk_3 = 'checked';
                break;
            case 1:
            default:
                $this->chk_1 = 'checked';
                $this->aWhere['status'] = ActividadStatusId::ACTUAL;
                $this->aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $this->aOperator['f_ini'] = 'BETWEEN';
                break;
        }
    }

    public function getRadioHtml(): string
    {
        $html = "<input type='Radio' id='modo_curso_1' name='modo_curso' value=1 $this->chk_1 onclick=fnjs_actualizar(this.form)>";
        $html .= ucfirst(_('actuales'));
        $html .= "<input type='Radio' id='modo_curso_2' name='modo_curso' value=2 $this->chk_2 onclick=fnjs_actualizar(this.form)>";
        $html .= ucfirst(_('todas las de este curso'));
        $html .= "<input type='Radio' id='modo_curso_3' name='modo_curso' value=3 $this->chk_3 onclick=fnjs_actualizar(this.form)>";
        $html .= ucfirst(_('todos los cursos'));

        return $html;
    }
}
