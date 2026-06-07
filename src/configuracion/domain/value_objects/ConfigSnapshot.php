<?php

namespace src\configuracion\domain\value_objects;

use src\shared\config\ConfigGlobal;

/**
 * Snapshot inmutable y serializable de la configuración de una dl.
 *
 * Sustituye la antigua `src\configuracion\domain\entity\Config`, que estaba
 * ubicada en `domain/entity/` pero en realidad era un servicio que tiraba de
 * `$GLOBALS['container']->get(ConfigSchemaRepositoryInterface::class)` en cada
 * getter (violación DDD: Service Locator dentro de dominio).
 *
 * Al ser un VO con propiedades `readonly` escalares/array, es serializable y
 * puede guardarse sin problemas en `$_SESSION['oConfig']` (no arrastra PDO).
 *
 * Se construye una vez por request/login mediante
 * `src\configuracion\application\ObtenerConfigSnapshot`.
 *
 * El mapa histórico de digrafos Latin → entidades HTML para impresión/PDF ya no forma
 * parte de esta clase; está en {@see \frontend\shared\config\OrbixRuntime::latinHtmlEntityReplaceMap()}.
 *
 * @package orbix
 */
final class ConfigSnapshot
{
    private string $msg;

    public function __construct(
        public readonly ?string $gesCalendario,
        public readonly ?string $ceLugar,
        public readonly ?string $regionLatin,
        public readonly ?string $vstgr,
        public readonly ?string $lugarFirma,
        public readonly ?string $dirStgr,
        public readonly ?string $ambito,
        public readonly ?string $notaCorte,
        public readonly ?string $notaMax,
        public readonly ?string $caducaCursada,
        public readonly ?string $idiomaDefault,
        public readonly ?string $iniContadorCertificados,
        public readonly ?string $jefeCalendario,
        /** @var array{ini_dia?: int, ini_mes?: int, fin_dia?: int, fin_mes?: int}|null */
        public readonly ?array $aCursoStgr,
        /** @var array{ini_dia?: int, ini_mes?: int, fin_dia?: int, fin_mes?: int}|null */
        public readonly ?array $aCursoCrt,
    ) {
        $this->msg = _("Debe configurar el esquema en Menu: Sistema > Configuración > config esquema");
    }

    /**
     * Mensaje HTML cuando falta un parámetro de esquema (sin abortar el request).
     */
    public function formatMissingParameterMessage(string $nom_param): string
    {
        return $this->msg . '<br><br>' . sprintf(_("falta el parámetro: %s"), $nom_param);
    }

    /**
     * Mensaje HTML con todos los parámetros faltantes (sin abortar el request).
     *
     * @param array<int|string, string> $checks mapa valor del parámetro => etiqueta traducible
     */
    public function formatMissingParametersMessage(array $checks): string
    {
        $labels = [];
        foreach ($checks as $value => $label) {
            if ((string)$value === '') {
                $labels[] = $label;
            }
        }
        if ($labels === []) {
            return '';
        }
        $msg = $this->msg . '<br><br>';
        foreach ($labels as $label) {
            $msg .= sprintf(_("falta el parámetro: %s"), $label) . '<br>';
        }

        return rtrim($msg, '<br>');
    }

    /**
     * Devuelve el valor del parámetro o muere con un mensaje de configuración faltante.
     */
    private function requireValue(?string $value, string $nom_param): string
    {
        if (empty($value)) {
            exit($this->formatMissingParameterMessage($nom_param));
        }
        return $value;
    }

    /**
     * @param array<string, mixed>|null $aCurso
     */
    private function requireCursoField(?array $aCurso, string $key, string $nom_param): int
    {
        if (empty($aCurso) || empty($aCurso[$key])) {
            $msg = $this->msg;
            $msg .= "<br><br>";
            $msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($msg);
        }
        return (int) (is_numeric($aCurso[$key]) ? $aCurso[$key] : 0);
    }

    /* ------------------- Básicos ------------------- */

    public function getGestionCalendario(): ?string
    {
        return $this->gesCalendario;
    }

    public function getCe_lugar(): string
    {
        if (empty($this->ceLugar)) {
            $msg = $this->msg;
            $msg .= "<br><br>";
            $msg .= sprintf(_("falta el parámetro: %s"), _("lugar ce"));
            $msg .= "<br>" . _("se puede poner una lista separada por comas");
            exit ($msg);
        }
        return $this->ceLugar;
    }

    /**
     * @return string[]
     */
    public function getCe(): array
    {
        return explode(',', $this->getCe_lugar());
    }

    public function getNomRegionLatin(): string
    {
        return $this->requireValue($this->regionLatin, _("nombre región en latín"));
    }

    public function getVstgr(): string
    {
        return $this->requireValue($this->vstgr, _("vstgr"));
    }

    /**
     * Alias histórico usado por controladores de certificados.
     * Lee el mismo parámetro que `getVstgr()`.
     */
    public function getNomVstgr(): string
    {
        return $this->getVstgr();
    }

    public function getLugarFirma(): string
    {
        return $this->requireValue($this->lugarFirma, _("lugar firma"));
    }

    public function getDirStgr(): string
    {
        return $this->requireValue($this->dirStgr, _("direccion stgr"));
    }

    public function getAmbito(): string
    {
        return $this->requireValue($this->ambito, _("ámbito"));
    }

    public function getNotaCorte(): string
    {
        return $this->requireValue($this->notaCorte, _("nota corte"));
    }

    public function getNotaMax(): string
    {
        return $this->requireValue($this->notaMax, _("nota máxima"));
    }

    public function getCaducaCursada(): string
    {
        return $this->requireValue($this->caducaCursada, _("caduca cursada"));
    }

    public function getIdioma_default(): string
    {
        return $this->requireValue($this->idiomaDefault, _("idioma por defecto"));
    }

    public function getContador_certificados(): string
    {
        return $this->requireValue($this->iniContadorCertificados, _("inicio contador certificados"));
    }

    /* ------------------- Jefe calendario ------------------- */

    /**
     * Devuelve TRUE O FALSE si es o no jefe del calendario.
     * Si no se le pasa ningún valor, compara con el usuario actual.
     */
    public function is_jefeCalendario(string $username = ''): bool
    {
        $valor = $this->requireValue($this->jefeCalendario, _("jefe calendario"));

        $a_jefes_calendario = explode(',', $valor);
        if (empty($username)) {
            $username = ConfigGlobal::mi_usuario();
        }
        return in_array($username, $a_jefes_calendario, true);
    }

    /* ------------------- Curso (stgr / crt) ------------------- */

    /**
     * @return array{ini_dia?: int, ini_mes?: int, fin_dia?: int, fin_mes?: int}
     */
    public function getCursoStgr(): array
    {
        return $this->aCursoStgr ?? [];
    }

    /**
     * @return array{ini_dia?: int, ini_mes?: int, fin_dia?: int, fin_mes?: int}
     */
    public function getCursoCrt(): array
    {
        return $this->aCursoCrt ?? [];
    }

    public function getDiaIniStgr(): int
    {
        return $this->requireCursoField($this->aCursoStgr, 'ini_dia', _("dia de ini stgr"));
    }

    public function getMesIniStgr(): int
    {
        return $this->requireCursoField($this->aCursoStgr, 'ini_mes', _("mes de ini stgr"));
    }

    public function getDiaFinStgr(): int
    {
        return $this->requireCursoField($this->aCursoStgr, 'fin_dia', _("dia de fin stgr"));
    }

    public function getMesFinStgr(): int
    {
        return $this->requireCursoField($this->aCursoStgr, 'fin_mes', _("mes de fin stgr"));
    }

    public function getDiaIniCrt(): int
    {
        return $this->requireCursoField($this->aCursoCrt, 'ini_dia', _("dia de ini crt"));
    }

    public function getMesIniCrt(): int
    {
        return $this->requireCursoField($this->aCursoCrt, 'ini_mes', _("mes de ini crt"));
    }

    public function getDiaFinCrt(): int
    {
        return $this->requireCursoField($this->aCursoCrt, 'fin_dia', _("dia de fin crt"));
    }

    public function getMesFinCrt(): int
    {
        return $this->requireCursoField($this->aCursoCrt, 'fin_mes', _("mes de fin crt"));
    }

    /**
     * Devuelve el año final del curso lectivo, según si estamos ya pasado el mes de fin.
     */
    public function any_final_curs(string $que = 'est'): int
    {
        switch ($que) {
            case 'est':
                $fin_m = $this->getMesFinStgr();
                break;
            case 'crt':
                $fin_m = $this->getMesFinCrt();
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit ($err_switch);
        }
        if ((int) date('m') > $fin_m) {
            return (int) date("Y") + 1;
        }
        return (int) date("Y");
    }
}
