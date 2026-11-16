<?php

declare(strict_types=1);

namespace src\shared\domain;

use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Pasa un periodo con nombre («curso», «trimestre_2», «otro»…) a fecha de inicio y fecha de fin.
 *
 * Es el cálculo de `frontend\shared\web\Periodo`, para las clases de `src/`: sin sesión ni llamadas al backend. Los
 * periodos del curso (`curso`, `curso_crt`, `curso_ca`) necesitan el calendario escolar, que se pasa al construir
 * (ver {@see \src\shared\domain\contracts\CalendarioEscolarInterface}).
 */
final class Periodo
{
    private ?int $iany = null;
    private ?string $sf_ini = null;
    private ?string $sf_fin = null;
    private ?string $sempiezaminiso = null;
    private ?string $sempiezamaxiso = null;

    /**
     * @param array<string, int>|null $calendarioEscolar claves de `PeriodoCalendarioEscolarData`.
     */
    public function __construct(private ?array $calendarioEscolar = null)
    {
    }

    public function setDefaultAny(string $any): void
    {
        $any = match ($any) {
            'prev', 'previo', 'previous' => (string) ((int) date('Y') - 1),
            'siguiente', 'next' => (string) ((int) date('Y') + 1),
            default => date('Y'),
        };
        $this->setAny($any);
    }

    public function setEmpiezaMax(string $sempiezamax = ''): void
    {
        $this->setEmpiezaMaxIso(self::isoDesdeLocal($sempiezamax));
    }

    public function setEmpiezaMin(string $sempiezamin = ''): void
    {
        $this->setEmpiezaMinIso(self::isoDesdeLocal($sempiezamin));
    }

    public function setEmpiezaMaxIso(string $sempiezamaxiso = ''): void
    {
        $this->sempiezamaxiso = $sempiezamaxiso;
    }

    public function setEmpiezaMinIso(string $sempiezaminiso = ''): void
    {
        $this->sempiezaminiso = $sempiezaminiso;
    }

    public function setAny(int|string $iany): void
    {
        if ($iany !== '' && $iany !== 0 && $iany !== '0') {
            $this->iany = (int) $iany;
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
        if ($this->sf_ini === null || $this->sf_ini === '') {
            throw new \RuntimeException(_("Fecha de inicio no establecida: llame a setPeriodo() antes."));
        }

        return new DateTimeLocal($this->sf_ini);
    }

    public function getF_fin(): DateTimeLocal
    {
        if ($this->sf_fin === null || $this->sf_fin === '') {
            throw new \RuntimeException(_("Fecha de fin no establecida: llame a setPeriodo() antes."));
        }

        return new DateTimeLocal($this->sf_fin);
    }

    public function getTxt_cusro(): string
    {
        return $this->getF_ini()->getFromLocal() . ' - ' . $this->getF_fin()->getFromLocal();
    }

    /**
     * Calcula la fecha de inicio y de fin. Debe ser el último de todos los set.
     *
     * @param string $sPeriodo 'actual', 'curso', 'trimestre', 'verano', 'navidad', 'mes', 'otro', 'tot_any', 'any_prox',
     *                         'trimestre_N', 'curso_crt', 'curso_ca', 'desdeHoy'; cualquier otro, el año entero.
     */
    public function setPeriodo(string $sPeriodo): void
    {
        $any = $this->iany ?? (int) date('Y');
        $mes = (int) date('m');
        $dia = (int) date('d');
        switch ($sPeriodo) {
            case 'otro':
                $inicio = $this->sempiezaminiso ?? '';
                $fin = $this->sempiezamaxiso ?? '';
                break;
            case 'actual':
                $inicio = self::fecha($mes, $dia - 40, $any);
                $fin = self::fecha($mes + 9, 0, $any);
                break;
            case 'desdeHoy':
                $inicio = date('Y/m/d');
                $fin = self::fecha($mes + 6, 0, $any, 'Y/m/d');
                break;
            case 'curso':
                if ($mes > $this->calendario('mes_fin_stgr')) {
                    $inicio = $any . '/10/1';
                    $fin = ($any + 1) . '/5/31';
                } else {
                    $inicio = ($any - 1) . '/10/1';
                    $fin = $any . '/5/31';
                }
                break;
            case 'curso_crt':
            case 'curso_ca':
                [$tipo, $claveMesFin] = $sPeriodo === 'curso_crt' ? ['crt', 'mes_fin_crt'] : ['est', 'mes_fin_stgr'];
                $any2 = ($mes > $this->calendario($claveMesFin)) ? $any + 1 : $any;
                $inicio = FuncTablasSupport::cursoEst('inicio', $any2, $tipo, $this->calendarioEscolar)->getIso();
                $fin = FuncTablasSupport::cursoEst('fin', $any2, $tipo, $this->calendarioEscolar)->getIso();
                break;
            case 'navidad':
                $inicio = $any . '/12/1';
                $fin = self::fecha($mes + 1, 0, $any, 'Y/m/d');
                break;
            case 'trimestre':
                $inicio = $any . '/' . $mes . '/1';
                $fin = self::fecha($mes + 3, 0, $any, 'Y/m/d');
                break;
            case 'mes':
                $inicio = $any . '/' . $mes . '/1';
                $fin = self::fecha($mes + 1, 0, $any, 'Y/m/d');
                break;
            case 'verano':
                $inicio = $any . '/6/1';
                $fin = $any . '/9/30';
                break;
            case 'trimestre_1':
                $inicio = $any . '/1/1';
                $fin = $any . '/3/31';
                break;
            case 'trimestre_2':
                $inicio = $any . '/4/1';
                $fin = $any . '/6/30';
                break;
            case 'trimestre_3':
                $inicio = $any . '/7/1';
                $fin = $any . '/9/30';
                break;
            case 'trimestre_4':
                $inicio = $any . '/10/1';
                $fin = $any . '/12/31';
                break;
            case 'any_prox':
                $inicio = ($any + 1) . '/1/1';
                $fin = ($any + 1) . '/12/31';
                break;
            case 'tot_any':
            default:
                $inicio = $any . '/1/1';
                $fin = $any . '/12/31';
        }
        $this->sf_ini = $inicio;
        $this->sf_fin = $fin;
    }

    private function calendario(string $clave): int
    {
        if ($this->calendarioEscolar === null) {
            throw new \RuntimeException(_("Falta calendario escolar: use Periodo::conCalendarioDesdeBackend() o sesión oConfig."));
        }

        return (int) ($this->calendarioEscolar[$clave] ?? 0);
    }

    private static function isoDesdeLocal(string $local): string
    {
        $fecha = $local === '' ? null : DateTimeLocal::createFromLocal($local);

        return $fecha instanceof DateTimeLocal ? $fecha->getIso() : '';
    }

    private static function fecha(int $month, int $day, int $year, string $format = 'Y-m-d'): string
    {
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        if ($timestamp === false) {
            throw new \RuntimeException(sprintf(_("Fecha inválida: %d-%02d-%02d"), $year, $month, $day));
        }

        return date($format, $timestamp);
    }
}
