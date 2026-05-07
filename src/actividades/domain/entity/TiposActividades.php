<?php

namespace src\actividades\domain\entity;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;

/**
 * Entidad que implementa la lógica de los tipos de actividades.
 *
 * Formato id_tipo_activ = 6 dígitos: sfsv(1) + asistentes(1) + actividad(1 ó 2 -extendida-) + nom_tipo(3 ó 2).
 *
 * Ubicación: esta clase pertenece al dominio de `actividades`. Antes vivía
 * en `frontend/shared/web/`; se movió aquí porque combina parseo de ID
 * (dominio puro) con lookup de "posibles" contra el repositorio, que son
 * conceptos de dominio/backend — no UI.
 */
class TiposActividades
{
    /**
     * Mapa texto → código del primer dígito del id_tipo_activ (sf/sv/reservada).
     * Fuente única de verdad: este `const` lo expone también el endpoint
     * `/src/actividades/tipo_activ_metadata` (vía
     * {@see \src\actividades\application\TipoActivMetadata}) para que
     * {@see \frontend\actividades\helpers\TiposDeActividades} no lo duplique.
     */
    public const A_SFSV = [
        "sv" => 1,
        "sf" => 2,
        "reservada" => 3,
        "all" => '.',
    ];

    /**
     * Mapa texto → código del segundo dígito del id_tipo_activ (asistentes).
     * @see self::A_SFSV
     */
    public const A_ASISTENTES = [
        "n" => 1,
        "nax" => 2,
        "agd" => 3,
        "s" => 4,
        "sg" => 5,
        "sss+" => 6,
        "sr" => 7,
        "sr-nax" => 8,
        "sr-agd" => 9,
        "all" => '.',
    ];

    /**
     * Mapa texto → código del tercer dígito (actividad) en modo no extendida.
     * @see self::A_SFSV
     */
    public const A_ACTIVIDAD_1_DIGITO = [
        "crt" => '1',
        "ca" => '2',
        "cv" => '3',
        "cve" => '4',
        "cv-crt" => '5',
        "all" => '.',
    ];

    /**
     * Mapa texto → código de los dígitos 3-4 (actividad) en modo extendida.
     * @see self::A_SFSV
     */
    public const A_ACTIVIDAD_2_DIGITOS = [
        "crt" => 10,
        "crt-recientes" => 11,
        "crt-bach" => 15,
        "crt-univ" => 16,
        "ca" => 20,
        "ca-recientes" => 21,
        "ca-est" => 22,
        "semestre-inv" => 23,
        "ca-repaso" => 24,
        "ca-sacd" => 25,
        "cv" => 30,
        "cv-recientes" => 31,
        "cv-est" => 32,
        "cv-repaso" => 34,
        "cv-bach" => 35,
        "cv-univ" => 36,
        "cve" => 40,
        "cve-sacd" => 41,
        "cv-crt" => 50,
        "all" => '..',
    ];

    private string $sregexp_id_tipo_activ = '';
    private string $ssfsv = '';
    private string $sasistentes = '';
    private string $sactividad = '';
    private string $snom_tipo = '';

    private array $aSfsv = self::A_SFSV;
    private array $aAsistentes = self::A_ASISTENTES;
    private array $aActividad1Digito = self::A_ACTIVIDAD_1_DIGITO;
    private array $aActividad2Digitos = self::A_ACTIVIDAD_2_DIGITOS;

    private array $afSfsv = [];
    private array $afAsistentes = [];
    private array $afActividad1Digito = [];
    private array $afActividad2Digitos = [];

    private bool $extendida = false;
    private array $afNom_tipo;
    private array $aNom_tipo;
    private ?TipoDeActividadRepositoryInterface $TipoDeActividadRepository = null;

    /**
     * @param int|string $id id_tipo_activ (parseable). Puede omitirse y fijarse luego con los setters.
     * @param bool $extendida true para IDs con parte "actividad" de 2 dígitos (total 7 dígitos).
     * @param TipoDeActividadRepositoryInterface|null $repository Repositorio para los lookups de "posibles".
     *        Se puede omitir: en ese caso se resuelve perezosamente desde el contenedor la primera vez
     *        que se necesita (ver {@see resolveRepository()}). Preferir inyección explícita.
     */
    public function __construct(
        int|string $id = '',
        bool $extendida = false,
        ?TipoDeActividadRepositoryInterface $repository = null
    ) {
        $this->setExtendida($extendida);
        if (isset($id) && $id !== '') {
            $this->separarId((string)$id);
        }
        $this->TipoDeActividadRepository = $repository;
        $this->getFlipAsistentes();
        $this->getFlipActividad1Digito();
        $this->getFlipActividad2Digitos();
        $this->getFlipSfsv();
    }

    /**
     * Inyecta el repositorio después de la construcción (útil cuando la clase se
     * crea con `new TiposActividades($id)` desde código legacy y se quiere
     * forzar la dependencia desde un test o servicio).
     */
    public function setTipoDeActividadRepository(TipoDeActividadRepositoryInterface $repository): void
    {
        $this->TipoDeActividadRepository = $repository;
    }

    /**
     * Devuelve el repositorio inyectado, o lo obtiene del contenedor global
     * como fallback perezoso.
     *
     * @deprecated El fallback al contenedor global (service locator) se
     *             mantiene solo para no romper los ~65 sitios legacy que
     *             instancian esta clase con `new TiposActividades($id)`. Los
     *             sitios nuevos deben inyectar el repositorio explícitamente
     *             por constructor o vía {@see setTipoDeActividadRepository()}.
     */
    private function resolveRepository(): TipoDeActividadRepositoryInterface
    {
        if ($this->TipoDeActividadRepository === null) {
            $this->TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        }
        return $this->TipoDeActividadRepository;
    }

    private function getFlipSfsv(): array
    {
        if (empty($this->afSfsv)) $this->afSfsv = array_flip($this->aSfsv);
        return $this->afSfsv;
    }

    private function getFlipAsistentes(): array
    {
        if (empty($this->afAsistentes)) $this->afAsistentes = array_flip($this->aAsistentes);
        return $this->afAsistentes;
    }

    private function getFlipActividad1Digito(): array
    {
        if (empty($this->afActividad1Digito)) $this->afActividad1Digito = array_flip($this->aActividad1Digito);
        return $this->afActividad1Digito;
    }

    private function getFlipActividad2Digitos(): array
    {
        if (empty($this->afActividad2Digitos)) $this->afActividad2Digitos = array_flip($this->aActividad2Digitos);
        return $this->afActividad2Digitos;
    }

    private function separarId(string $sregexp_id_tipo_activ): void
    {
        if (!empty($sregexp_id_tipo_activ)) {
            $inc = 0;
            if (($ini = strpos($sregexp_id_tipo_activ, '[')) !== false) {
                $fin = strpos($sregexp_id_tipo_activ, ']');
                $inc = $fin - $ini;
            }
            $long = empty($inc) ? 6 : 6 + $inc;
            for ($i = strlen($sregexp_id_tipo_activ); $i < $long; $i++) {
                $sregexp_id_tipo_activ .= '.';
            }
            $matches = [];
            if ($this->extendida) {
                preg_match('/(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\[\d+\]|\d{2}|\d\.|\.\.)(\d{2}|\.*)/', $sregexp_id_tipo_activ, $matches);
            } else {
                preg_match('/(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\d{3}|\.*)/', $sregexp_id_tipo_activ, $matches);
            }
            if (!empty($matches)) {
                $this->sregexp_id_tipo_activ = $matches[0];
                $this->ssfsv = $matches[1];
                $this->sasistentes = $matches[2];
                $this->sactividad = $matches[3];
                $this->snom_tipo = $matches[4];
            }
        }
    }

    public function setPosiblesAll($bAll): void
    {
        if ($bAll === false) {
            unset ($this->aSfsv['all']);
        }
    }

    /**
     * Separa un id_tipo_activ con posibles asistentes en
     * un array de id_tipo_activ separados.
     */
    public function getArrayAsistentesIndividual(): array
    {
        $a_tipos = [];
        $aAsistentes = $this->getAsistentesPosibles();
        foreach ($aAsistentes as $iasistentes => $sasistentes) {
            $txt_id = $this->getSfsvText() . ' ' . $sasistentes;
            $a_tipos[$txt_id] = $this->getSfsvId() . $iasistentes;
        }
        return $a_tipos;
    }

    public function getId_tipo_activ(): string
    {
        $txt = $this->ssfsv;
        $txt .= $this->sasistentes;
        $txt .= $this->sactividad;
        $txt .= $this->snom_tipo;
        return $txt;
    }

    public function getNomPasarela(): string
    {
        $txt_svsf = $this->getSfsvText();
        $txt_asistentes = '';
        if ($this->getAsistentesText() === 'n') {
            $txt_asistentes = $txt_svsf === 'sv' ? _("numerarios") : _("numerarias");
        }
        if ($this->getAsistentesText() === 'nax') {
            $txt_asistentes = _("numerarias auxiliares");
        }
        if ($this->getAsistentesText() === 'agd') {
            $txt_asistentes = $txt_svsf === 'sv' ? _("agregados") : _("agregadas");
        }
        if ($this->getAsistentesText() === 'sg') {
            $txt_asistentes = $txt_svsf === 'sv' ? _("coperadores") : _("coperadoras");
        }

        $txt_actividad = 'Actividad';
        if ($this->getActividadText() === 'crt') {
            $txt_actividad = _("curso de retiro");
        }
        if ($this->getActividadText() === 'ca') {
            $txt_actividad = _("curso anual");
        }
        if ($this->getActividadText() === 'cv' || $this->getActividadText() === 'cve') {
            $txt_actividad = _("convivencia");
        }
        return $txt_actividad . ' ' . $txt_asistentes;
    }

    /**
     * Recupera el atributo nom en formato de texto, sin el "(sin especificar)".
     */
    public function getNomGral(): string
    {
        $txt = $this->getSfsvText();
        if ($this->getAsistentesText() !== 'all') $txt .= ' ' . $this->getAsistentesText();
        if ($this->getActividadText() !== 'all') $txt .= ' ' . $this->getActividadText();
        if ($this->getNom_tipoId() !== 0 && $this->getNom_tipoText() !== 'all') $txt .= ' ' . $this->getNom_tipoText();
        return $txt;
    }

    public function getNom(): string
    {
        $txt = $this->getSfsvText();
        if ($this->getAsistentesText() !== 'all') $txt .= ' ' . $this->getAsistentesText();
        if ($this->getActividadText() !== 'all') $txt .= ' ' . $this->getActividadText();
        if ($this->getNom_tipoText() !== 'all') $txt .= ' ' . $this->getNom_tipoText();
        return $txt;
    }

    public function getSfsvText(): string
    {
        $aText = $this->getFlipSfsv();
        if (is_numeric($this->ssfsv)) {
            return $aText[$this->ssfsv];
        }
        return 'all';
    }

    public function setSfsvText(?string $sSfsv): void
    {
        if ($sSfsv === null) {
            $sSfsv = 'all';
        }
        $this->ssfsv = $this->aSfsv[$sSfsv];
    }

    public function getSfsvId(): int
    {
        return (int)$this->ssfsv;
    }

    public function setSfsvId($isfsv): void
    {
        $this->ssfsv = (string)$isfsv;
    }

    public function getSfsvRegexp(): string
    {
        return '^' . $this->ssfsv;
    }

    public function getSfsvPosibles(): array
    {
        $aText = $this->getFlipSfsv();
        return $this->resolveRepository()->getSfsvPosibles($aText);
    }

    public function getAsistentesText(): string
    {
        $aText = $this->getFlipAsistentes();
        if (is_numeric($this->sasistentes)) {
            return $aText[$this->sasistentes];
        }
        return 'all';
    }

    public function setAsistentesText(string $sAsistentes): void
    {
        if (empty($sAsistentes) || $sAsistentes === '.') {
            $sAsistentes = 'all';
        }
        // puede ser un string separado por comas (s,sg)
        $a_asistentes_multiple = explode(',', $sAsistentes);
        if (count($a_asistentes_multiple) > 1) {
            $asistentes_txt = "[";
            foreach ($a_asistentes_multiple as $asis) {
                $asistentes_txt .= $this->aAsistentes[$asis];
            }
            $asistentes_txt .= "]";
        } else {
            $asis = $a_asistentes_multiple[0];
            $asistentes_txt = $this->aAsistentes[$asis];
        }
        $this->sasistentes = $asistentes_txt;
    }

    public function getAsistentesId(): int
    {
        return (int)$this->sasistentes;
    }

    public function setAsistentesId($id): void
    {
        $this->sasistentes = (string)$id;
    }

    public function getAsistentesRegexp(): string
    {
        return $this->getSfsvRegexp() . $this->sasistentes;
    }

    public function getAsistentesPosibles(): array
    {
        $aText = $this->getFlipAsistentes();
        $regexp = !empty($this->sasistentes) ? $this->getAsistentesRegexp() : $this->getSfsvRegexp();
        return $this->resolveRepository()->getAsistentesPosibles($aText, $regexp);
    }

    public function getActividadText(): string
    {
        if (is_numeric($this->sactividad)) {
            $aText = $this->extendida ? $this->getFlipActividad2Digitos() : $this->getFlipActividad1Digito();
            return $aText[$this->sactividad];
        }
        return 'all';
    }

    public function setActividadText($sActividad): bool
    {
        if (is_string($sActividad)) {
            if (empty($sActividad) || $sActividad === '.') {
                $sActividad = 'all';
            }
            $this->sactividad = $this->aActividad1Digito[$sActividad];
            if ($this->extendida) {
                $this->sactividad = $this->aActividad2Digitos[$sActividad];
            }
        } else {
            return false;
        }
        return true;
    }

    public function getActividad2DigitosText(): string
    {
        $aText = $this->getFlipActividad2Digitos();
        if (is_numeric($this->sactividad)) {
            return $aText[$this->sactividad];
        }
        return 'all';
    }

    public function setActividad2DigitosText($sActividad): bool
    {
        if (is_string($sActividad)) {
            if (empty($sActividad)) {
                $sActividad = 'all';
            }
            $this->sactividad = $this->aActividad2Digitos[$sActividad];
        } else {
            return false;
        }
        return true;
    }

    public function getActividadId(): string
    {
        return $this->sactividad;
    }

    public function setActividadId($id): void
    {
        $this->sactividad = $id;
    }

    public function getActividadRegexp(): string
    {
        return $this->getAsistentesRegexp() . $this->sactividad;
    }

    public function getActividadesPosibles1Digito(): array
    {
        $aText = $this->getFlipActividad1Digito();
        return $this->resolveRepository()->getActividadesPosibles(1, $aText, $this->getAsistentesRegexp());
    }

    public function getActividadesPosibles2Digitos(): array
    {
        $aText = $this->getFlipActividad2Digitos();
        return $this->resolveRepository()->getActividadesPosibles(2, $aText, $this->getAsistentesRegexp());
    }

    public function getNom_tipoText(): string
    {
        if (is_numeric($this->snom_tipo)) {
            if (isset($this->afNom_tipo)) {
                return $this->afNom_tipo[$this->snom_tipo] ?? '?';
            }
            $this->getNom_tipoPosibles3Digitos();
            return $this->afNom_tipo[$this->snom_tipo] ?? '?';
        }
        return 'all';
    }

    public function setNom_tipoText($sNom_tipo): bool
    {
        if (is_string($sNom_tipo)) {
            $this->snom_tipo = $this->aNom_tipo[$sNom_tipo];
        } else {
            return false;
        }
        return true;
    }

    public function getNom_tipoId(): int
    {
        return (int)$this->snom_tipo;
    }

    public function getNom_tipoRegexp(): string
    {
        return $this->getActividadRegexp() . $this->snom_tipo;
    }

    public function getNom_tipoPosibles3Digitos(): array
    {
        $rta = $this->resolveRepository()->getNom_tipoPosibles(3, $this->getActividadRegexp());
        $this->afNom_tipo = $rta['tipo_nom'];
        $this->aNom_tipo = $rta['nom_tipo'];
        return $rta['tipo_nom'];
    }

    public function getNom_tipoPosibles2Digitos(): array
    {
        $rta = $this->resolveRepository()->getNom_tipoPosibles(2, $this->getActividadRegexp());
        $this->afNom_tipo = $rta['tipo_nom'];
        $this->aNom_tipo = $rta['nom_tipo'];
        return $rta['tipo_nom'];
    }

    /**
     * Retorna els posibles id_tipo en format de array.
     *
     * @param string $regexp expresió regular per tornar el id (substring('bla' from regexp) del postgresql).
     */
    public function getId_tipoPosibles(string $regexp = '.*'): array
    {
        return $this->resolveRepository()->getId_tipoPosibles($regexp, $this->getActividadRegexp());
    }

    public function getExtendida(): bool
    {
        return $this->extendida;
    }

    public function setExtendida(bool $extendida): void
    {
        $this->extendida = $extendida;
    }
}
