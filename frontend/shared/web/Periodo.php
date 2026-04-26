<?php

namespace frontend\shared\web;

// El front no siempre carga `global_object` (donde se incluye `func_tablas.php`);
// `use function` no dispara el autoload — requerir antes del alias.
require_once dirname(__DIR__, 3) . '/src/shared/domain/helpers/func_tablas.php';

use frontend\shared\PostRequest;
use src\shared\domain\value_objects\DateTimeLocal;
use function src\shared\domain\helpers\curso_est;

/**
 * Classe que passa el periode amb texte a data inici i data fi.
 */
class Periodo
{
    private ?int $iany = null;
    private ?string $sf_ini = null;
    private ?string $sf_fin = null;
    private ?string $sempiezaminiso = null;
    private ?string $sempiezamaxiso = null;

    /** @var array<string, int>|null Ver `PeriodoCalendarioEscolarData` (backend). */
    private ?array $calendarioEscolar = null;

    public function __construct()
    {
    }

    public function setCalendarioEscolar(array $calendario): void
    {
        $this->calendarioEscolar = $calendario;
    }

    /**
     * Carga en BD (vía `/src/configuracion/periodo_calendario_escolar_data`) lo que antes
     * solo existía en `$_SESSION['oConfig']` para `curso` / `curso_ca` / `curso_crt`.
     */
    public static function conCalendarioDesdeBackend(): self
    {
        $o = new self();
        $data = PostRequest::getDataFromUrl(
            '/src/configuracion/periodo_calendario_escolar_data',
            [],
        );
        $o->setCalendarioEscolar($data);
        return $o;
    }

    private function mesFinStgr(): int
    {
        if ($this->calendarioEscolar !== null) {
            return (int)($this->calendarioEscolar['mes_fin_stgr'] ?? 0);
        }
        if (isset($_SESSION['oConfig'])) {
            return $_SESSION['oConfig']->getMesFinStgr();
        }
        throw new \RuntimeException(_('Falta calendario escolar: use Periodo::conCalendarioDesdeBackend() o sesión oConfig.'));
    }

    private function mesFinCrt(): int
    {
        if ($this->calendarioEscolar !== null) {
            return (int)($this->calendarioEscolar['mes_fin_crt'] ?? 0);
        }
        if (isset($_SESSION['oConfig'])) {
            return $_SESSION['oConfig']->getMesFinCrt();
        }
        throw new \RuntimeException(_('Falta calendario escolar: use Periodo::conCalendarioDesdeBackend() o sesión oConfig.'));
    }

    /** @return array<string, int>|null */
    private function calendarioParaCursoEst(): ?array
    {
        return $this->calendarioEscolar;
    }

    public function setDefaultAny($any): void
    {
        switch ($any) {
            case 'prev':
            case 'previo':
            case 'previous':
                $any = (int)date('Y') - 1;
                break;
            case 'siguiente':
            case 'next':
                $any = (int)date('Y') + 1;
                break;
            case 'actual':
            default:
                $any = date('Y');
        }
        $this->setAny($any);
    }

    public function setEmpiezaMax($sempiezamax = ''): void
    {
        if (!empty($sempiezamax)) {
            $oEmpiezamax = DateTimeLocal::createFromLocal($sempiezamax);
            $empiezamaxIso = $oEmpiezamax->getIso();
            $this->setEmpiezaMaxIso($empiezamaxIso);
        } else {
            $this->setEmpiezaMaxIso();
        }
    }

    public function setEmpiezaMin($sempiezamin = ''): void
    {
        if (!empty($sempiezamin)) {
            $oEmpiezamin = DateTimeLocal::createFromLocal($sempiezamin);
            $empiezaminIso = $oEmpiezamin->getIso();
            $this->setEmpiezaMinIso($empiezaminIso);
        } else {
            $this->setEmpiezaMinIso();
        }
    }

    public function setEmpiezaMaxIso($sempiezamaxiso = ''): void
    {
        $this->sempiezamaxiso = $sempiezamaxiso;
    }

    public function setEmpiezaMinIso($sempiezaminiso = ''): void
    {
        $this->sempiezaminiso = $sempiezaminiso;
    }

    public function setAny($iany): void
    {
        if (!empty($iany)) {
            $this->iany = (int)$iany;
        }
    }

    public function getF_ini_iso(): ?string
    {
        return $this->sf_ini;
    }

    public function getF_fin_iso(): ?string
    {
        return $this->sf_fin;
    }

    public function getF_ini(): DateTimeLocal
    {
        return new DateTimeLocal($this->sf_ini);
    }

    public function getF_fin(): DateTimeLocal
    {
        return new DateTimeLocal($this->sf_fin);
    }

    public function getTxt_cusro(): string
    {
        $oInicio = $this->getF_ini();
        $oFin = $this->getF_fin();

        $ini_local = $oInicio->getFromLocal();
        $fin_local = $oFin->getFromLocal();

        return "$ini_local - $fin_local";
    }

    /**
     * Establece una fecha inicio y una fecha fin de un periodo. Debe ser el último de todos los set.
     *
     * @param string $sPeriodo Alias del periodo ('actual', 'curso', 'trimestre', 'verano', 'navidad', 'mes', 'otro', 'tot_any', 'any_prox', 'trimestre_N', 'curso_crt', 'curso_ca', 'desdeHoy').
     */
    public function setPeriodo(string $sPeriodo): void
    {
        $any = empty($this->iany) ? date('Y') : $this->iany;
        $mes = date('m');
        switch ($sPeriodo) {
            case "otro":
                $inicio = $this->sempiezaminiso;
                $fin = $this->sempiezamaxiso;
                break;
            case 'actual':
                $inicio = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 40, date('Y')));
                $fin = date('Y-m-d', mktime(0, 0, 0, date('m') + 9, 0, date('Y')));
                break;
            case "desdeHoy":
                $inicio = date('Y/m/d');
                $fin = date('Y/m/d', mktime(0, 0, 0, $mes + 6, 0, $any));
                break;
            case "curso":
                $fin_m = $this->mesFinStgr();
                if ($mes > $fin_m) {
                    $any2 = $any + 1;
                    $inicio = $any . "/10/1";
                    $fin = $any2 . "/5/31";
                } else {
                    $any2 = $any - 1;
                    $inicio = $any2 . "/10/1";
                    $fin = $any . "/5/31";
                }
                break;
            case "curso_crt":
                $fin_m = $this->mesFinCrt();
                $any2 = ($mes > $fin_m) ? $any + 1 : $any;
                $c = $this->calendarioParaCursoEst();
                $oInicio = curso_est('inicio', $any2, 'crt', $c);
                $oFin = curso_est('fin', $any2, 'crt', $c);
                $inicio = $oInicio->getIso();
                $fin = $oFin->getIso();
                break;
            case "curso_ca":
                $fin_m = $this->mesFinStgr();
                $any2 = ($mes > $fin_m) ? $any + 1 : $any;
                $c = $this->calendarioParaCursoEst();
                $oInicio = curso_est('inicio', $any2, 'est', $c);
                $oFin = curso_est('fin', $any2, 'est', $c);
                $inicio = $oInicio->getIso();
                $fin = $oFin->getIso();
                break;
            case "navidad":
                $inicio = $any . "/12/1";
                $fin = date('Y/m/d', mktime(0, 0, 0, $mes + 1, 0, $any));
                break;
            case "trimestre":
                $inicio = $any . "/$mes/1";
                $fin = date('Y/m/d', mktime(0, 0, 0, $mes + 3, 0, $any));
                break;
            case "mes":
                $inicio = $any . "/$mes/1";
                $fin = date('Y/m/d', mktime(0, 0, 0, $mes + 1, 0, $any));
                break;
            case "verano":
                $inicio = $any . "/6/1";
                $fin = $any . "/9/30";
                break;
            case "trimestre_1":
                $inicio = $any . "/1/1";
                $fin = $any . "/3/31";
                break;
            case "trimestre_2":
                $inicio = $any . "/4/1";
                $fin = $any . "/6/30";
                break;
            case "trimestre_3":
                $inicio = $any . "/7/1";
                $fin = $any . "/9/30";
                break;
            case "trimestre_4":
                $inicio = $any . "/10/1";
                $fin = $any . "/12/31";
                break;
            case "tot_any":
                $inicio = $any . "/1/1";
                $fin = $any . "/12/31";
                break;
            case "any_prox":
                $inicio = ($any + 1) . "/1/1";
                $fin = ($any + 1) . "/12/31";
                break;
            default:
                $inicio = $any . "/1/1";
                $fin = $any . "/12/31";
        }
        $this->sf_ini = $inicio;
        $this->sf_fin = $fin;
    }

}
