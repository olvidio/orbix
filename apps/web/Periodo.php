<?php

namespace web;

use function core\curso_est;

/**
 * Classe que passa el periode amb texte a data inici i data fi.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class Periodo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * iAny de Periodo
     *
     * @var integer
     */
    private $iany;
    /**
     * df_ini de Periodo
     *
     * @var string
     */
    private $sf_ini;
    /**
     * df_fin de Periodo
     *
     * @var string
     */
    private $sf_fin;

    /**
     *
     * @var string
     */
    private $sempiezaminiso;
    /**
     *
     * @var string
     */
    private $sempiezamaxiso;


    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    // valores por defeccto
    function setDefaultAny($any)
    {
        switch ($any) {
            case 'prev':
            case 'previo':
            case 'previous':
                $any = date('Y') - 1;
                break;
            case 'siguiente':
            case 'next':
                $any = date('Y') + 1;
                break;
            case 'actual':
            default:
                $any = date('Y');
        }
        $this->setAny($any);
    }

    function setEmpiezaMax($sempiezamax = '')
    {
        if (!empty($sempiezamax)) {
            $oEmpiezamax = DateTimeLocal::createFromLocal($sempiezamax);
            $empiezamaxIso = $oEmpiezamax->getIso();
            $this->setEmpiezaMaxIso($empiezamaxIso);
        } else {
            $this->setEmpiezaMaxIso();
        }
    }

    function setEmpiezaMin($sempiezamin = '')
    {
        if (!empty($sempiezamin)) {
            $oEmpiezamin = DateTimeLocal::createFromLocal($sempiezamin);
            $empiezaminIso = $oEmpiezamin->getIso();
            $this->setEmpiezaMinIso($empiezaminIso);
        } else {
            $this->setEmpiezaMinIso();
        }
    }

    function setEmpiezaMaxIso($sempiezamaxiso = '')
    {
        $this->sempiezamaxiso = $sempiezamaxiso;
    }

    function setEmpiezaMinIso($sempiezaminiso = '')
    {
        $this->sempiezaminiso = $sempiezaminiso;
    }

    function setAny($iany)
    {
        if (!empty($iany)) {
            $this->iany = $iany;
        }
    }

    function getF_ini_iso()
    {
        return $this->sf_ini;
    }

    function getF_fin_iso()
    {
        return $this->sf_fin;
    }

    function getF_ini()
    {
        return new DateTimeLocal($this->sf_ini);
    }

    function getF_fin()
    {
        return new DateTimeLocal($this->sf_fin);
    }

    function getTxt_cusro()
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
     * @param string $sPeriodo .
     *          El año es el actual, o el que se haya establecido por defecto.
     *                 'otro' -> devuelve los valores de empiexamx y empiezamin que sh haya establecido.
     *                 'actual' -> desde 40 dias antes de hoy, hasta 9 meses después.$this
     *                 'desdeHoy' -> desde hoy hasta 6 meses después.
     *                 'curso' -> desde el 1-octubre al 31-mayo
     *                 'curso_crt' -> toma los dias de los parámetros de configuración.
     *                 'curso_ca' -> toma los dias de los parámetros de configuración.
     *                 'trimestre' -> mes actual + 3.
     *                 'mes' -> del 1 al 31 del mes actual.
     *                 'verano' -> 1-junio al 30-setiembre.
     *                 'navidad' -> 1--diciembre al 31-enero del año proximo.
     *                 'trimestre_1' -> 1-enero al 31-marzo.
     *                 'trimestre_2' -> 1-abril al 30-junio.
     *                 'trimestre_3' -> 1-julio al 30-septiembre.
     *                 'trimestre_4' -> 1-octubre al 31-diciembre.
     *                 'tot_any' -> 1-enero al 31-diciembre.
     *                 'any_prox' -> 1--enero al 31-diciembre del año proximo.
     *
     *                 'default' -> 1-enero al 31-diciembre.
     *
     */
    function setPeriodo(string $sPeriodo)
    {
        $any = empty($this->iany) ? date('Y') : $this->iany;
        $mes = date('m');
        switch ($sPeriodo) {
            case "otro":
                $inicio = $this->sempiezaminiso;
                $fin = $this->sempiezamaxiso;
                break;
            case 'actual':
                // desde 40 dias antes de hoy:
                $inicio = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 40, date('Y')));
                // hasta dentro de 9 meses desde hoy.
                $fin = date('Y-m-d', mktime(0, 0, 0, date('m') + 9, 0, date('Y')));
                break;
            case "desdeHoy":
                $inicio = date('Y/m/d');
                $fin = date('Y/m/d', mktime(0, 0, 0, $mes + 6, 0, $any));
                break;
            case "curso":
                $fin_m = $_SESSION['oConfig']->getMesFinStgr();
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
                $fin_m = $_SESSION['oConfig']->getMesFinCrt();
                if ($mes > $fin_m) {
                    $any2 = $any + 1;
                } else {
                    $any2 = $any;
                }
                $oInicio = curso_est("inicio", $any2, 'crt');
                $oFin = curso_est("fin", $any2, 'crt');

                $inicio = $oInicio->getIso();
                $fin = $oFin->getIso();
                break;
            case "curso_ca":
                $fin_m = $_SESSION['oConfig']->getMesFinStgr();
                if ($mes > $fin_m) {
                    $any2 = $any + 1;
                } else {
                    $any2 = $any;
                }
                $oInicio = curso_est("inicio", $any2, 'est');
                $oFin = curso_est("fin", $any2, 'est');

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
                if (empty($inicio)) {
                    $inicio = $any . "/1/1";
                }
                if (empty($fin)) {
                    $fin = $any . "/12/31";
                }
        }
        $this->sf_ini = $inicio;
        $this->sf_fin = $fin;
    }

}
