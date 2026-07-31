<?php

namespace frontend\shared\web;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\shared\PostRequest;
use frontend\shared\domain\value_objects\DateTimeLocal;
use frontend\shared\session\SessionConfig;

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

    /**
     * @param array<string, int> $calendario
     */
    public function setCalendarioEscolar(array $calendario): void
    {
        $this->calendarioEscolar = $calendario;
    }

    /**
     * Carga en BD (vía `/src/configuracion/periodo_calendario_escolar_data`) lo que antes
     * solo existía en `$_SESSION['oConfig']` para `curso` / `curso_ca` / `curso_crt`.
     *
     * @param bool $throwOnError Si es true, no hace `exit` en error de PostRequest (p. ej. respuestas AJAX JSON).
     */
    public static function conCalendarioDesdeBackend(bool $throwOnError = false): self
    {
        if (SessionConfig::isPresent()) {
            return new self();
        }

        /** @var array<string, int>|null */
        static $calendarioCache = null;
        if ($calendarioCache !== null) {
            $o = new self();
            $o->setCalendarioEscolar($calendarioCache);

            return $o;
        }

        $o = new self();
        $data = PostRequest::getDataFromUrl(
            '/src/configuracion/periodo_calendario_escolar_data',
            [],
            !$throwOnError,
        );
        if ($throwOnError && isset($data['error']) && is_string($data['error']) && $data['error'] !== '') {
            throw new \RuntimeException(
                PostRequest::stripInternalCallProvenance($data['error'])
            );
        }
        $calendarioCache = self::calendarioFromPostRequestData($data);
        $o->setCalendarioEscolar($calendarioCache);

        return $o;
    }

    /**
     * @param array<int|string, mixed> $data
     * @return array<string, int>
     */
    private static function calendarioFromPostRequestData(array $data): array
    {
        $keys = [
            'mes_fin_stgr',
            'mes_fin_crt',
            'dia_ini_stgr',
            'mes_ini_stgr',
            'dia_fin_stgr',
            'dia_ini_crt',
            'mes_ini_crt',
            'dia_fin_crt',
            'any_final_est',
            'any_final_crt',
        ];
        $calendario = [];
        foreach ($keys as $key) {
            $value = $data[$key] ?? 0;
            if (is_int($value)) {
                $calendario[$key] = $value;
            } elseif (is_string($value) && is_numeric($value)) {
                $calendario[$key] = (int) $value;
            } else {
                $calendario[$key] = 0;
            }
        }

        return $calendario;
    }

    private function mesFinStgr(): int
    {
        if ($this->calendarioEscolar !== null) {
            return (int)($this->calendarioEscolar['mes_fin_stgr'] ?? 0);
        }
        if (SessionConfig::isPresent()) {
            return SessionConfig::getMesFinStgr();
        }
        throw new \RuntimeException(_('Falta calendario escolar: use Periodo::conCalendarioDesdeBackend() o sesión oConfig.'));
    }

    private function mesFinCrt(): int
    {
        if ($this->calendarioEscolar !== null) {
            return (int)($this->calendarioEscolar['mes_fin_crt'] ?? 0);
        }
        if (SessionConfig::isPresent()) {
            return SessionConfig::getMesFinCrt();
        }
        throw new \RuntimeException(_('Falta calendario escolar: use Periodo::conCalendarioDesdeBackend() o sesión oConfig.'));
    }

    /** @return array<string, int>|null */
    private function calendarioParaCursoEst(): ?array
    {
        return $this->calendarioEscolar;
    }

    public function setDefaultAny(string $any): void
    {
        switch ($any) {
            case 'prev':
            case 'previo':
            case 'previous':
                $any = (string) ((int) date('Y') - 1);
                break;
            case 'siguiente':
            case 'next':
                $any = (string) ((int) date('Y') + 1);
                break;
            case 'actual':
            default:
                $any = date('Y');
        }
        $this->setAny($any);
    }

    public function setEmpiezaMax(string $sempiezamax = ''): void
    {
        if (!empty($sempiezamax)) {
            $oEmpiezamax = DateTimeLocal::createFromLocal($sempiezamax);
            if ($oEmpiezamax instanceof DateTimeLocal) {
                $this->setEmpiezaMaxIso($oEmpiezamax->getIso());
            } else {
                $this->setEmpiezaMaxIso();
            }
        } else {
            $this->setEmpiezaMaxIso();
        }
    }

    public function setEmpiezaMin(string $sempiezamin = ''): void
    {
        if (!empty($sempiezamin)) {
            $oEmpiezamin = DateTimeLocal::createFromLocal($sempiezamin);
            if ($oEmpiezamin instanceof DateTimeLocal) {
                $this->setEmpiezaMinIso($oEmpiezamin->getIso());
            } else {
                $this->setEmpiezaMinIso();
            }
        } else {
            $this->setEmpiezaMinIso();
        }
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
            throw new \RuntimeException(_('Fecha de inicio no establecida: llame a setPeriodo() antes.'));
        }

        return new DateTimeLocal($this->sf_ini);
    }

    public function getF_fin(): DateTimeLocal
    {
        if ($this->sf_fin === null || $this->sf_fin === '') {
            throw new \RuntimeException(_('Fecha de fin no establecida: llame a setPeriodo() antes.'));
        }

        return new DateTimeLocal($this->sf_fin);
    }

    private static function formatDateFromMktime(int $month, int $day, int $year, string $format = 'Y-m-d'): string
    {
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        if ($timestamp === false) {
            throw new \RuntimeException(sprintf(_('Fecha inválida: %d-%02d-%02d'), $year, $month, $day));
        }

        return date($format, $timestamp);
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
        $any = $this->iany ?? (int) date('Y');
        $mes = (int) date('m');
        $dia = (int) date('d');
        switch ($sPeriodo) {
            case "otro":
                $inicio = $this->sempiezaminiso ?? '';
                $fin = $this->sempiezamaxiso ?? '';
                break;
            case 'actual':
                $inicio = self::formatDateFromMktime($mes, $dia - 40, $any);
                $fin = self::formatDateFromMktime($mes + 9, 0, $any);
                break;
            case "desdeHoy":
                $inicio = date('Y/m/d');
                $fin = self::formatDateFromMktime($mes + 6, 0, $any, 'Y/m/d');
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
                $oInicio = \frontend\shared\helpers\FuncTablasSupport::cursoEst('inicio', $any2, 'crt', $c);
                $oFin = \frontend\shared\helpers\FuncTablasSupport::cursoEst('fin', $any2, 'crt', $c);
                $inicio = $oInicio->getIso();
                $fin = $oFin->getIso();
                break;
            case "curso_ca":
                $fin_m = $this->mesFinStgr();
                $any2 = ($mes > $fin_m) ? $any + 1 : $any;
                $c = $this->calendarioParaCursoEst();
                $oInicio = \frontend\shared\helpers\FuncTablasSupport::cursoEst('inicio', $any2, 'est', $c);
                $oFin = \frontend\shared\helpers\FuncTablasSupport::cursoEst('fin', $any2, 'est', $c);
                $inicio = $oInicio->getIso();
                $fin = $oFin->getIso();
                break;
            case "navidad":
                $inicio = $any . "/12/1";
                $fin = self::formatDateFromMktime($mes + 1, 0, $any, 'Y/m/d');
                break;
            case "trimestre":
                $inicio = $any . '/' . $mes . '/1';
                $fin = self::formatDateFromMktime($mes + 3, 0, $any, 'Y/m/d');
                break;
            case "mes":
                $inicio = $any . '/' . $mes . '/1';
                $fin = self::formatDateFromMktime($mes + 1, 0, $any, 'Y/m/d');
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
