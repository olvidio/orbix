<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

/**
 * Espejo en frontend de {@see \src\actividades\domain\entity\TiposActividades}:
 * misma API pública (parseo de id_tipo_activ y resolución de "posibles"), pero
 * sin acceso a repositorio.
 *
 * Toda la información que el dominio resuelve por SQL aquí vive en memoria:
 *
 *  - Los 4 mapas texto→código (sfsv, asistentes, actividad 1 dígito,
 *    actividad 2 dígitos) y la lista plana de tipos `{id_tipo_activ, nombre}`
 *    se obtienen via {@see TipoActivMetadataLoader}, que hace UNA sola
 *    request a `/src/actividades/tipo_activ_metadata` por petición de página
 *    y la cachea en memoria.
 *  - Las consultas tipo `getSfsvPosibles`, `getAsistentesPosibles`,
 *    `getActividadesPosibles*` y `getNom_tipoPosibles*` se resuelven contra
 *    esa lista en memoria, sin volver a tocar backend.
 *
 * Los maps y las filas se pueden inyectar explícitamente por constructor (útil
 * para tests). Si no se pasan, la clase se autocarga vía loader.
 */
class TiposDeActividades
{
    private string $sregexp_id_tipo_activ = '';
    private string $ssfsv = '';
    private string $sasistentes = '';
    private string $sactividad = '';
    private string $snom_tipo = '';

    /** @var array<string, int|string> */
    private array $aSfsv;

    /** @var array<string, int|string> */
    private array $aAsistentes;

    /** @var array<string, int|string> */
    private array $aActividad1Digito;

    /** @var array<string, int|string> */
    private array $aActividad2Digitos;

    /** @var array<int|string, string> */
    private array $afSfsv = [];

    /** @var array<int|string, string> */
    private array $afAsistentes = [];

    /** @var array<int|string, string> */
    private array $afActividad1Digito = [];

    /** @var array<int|string, string> */
    private array $afActividad2Digitos = [];

    private bool $extendida = false;

    /** @var array<int|string, string> */
    private array $afNom_tipo = [];

    /** @var array<string, int|string> */
    private array $aNom_tipo = [];

    /**
     * Filas planas de `a_tipos_actividad` (id_tipo_activ + nombre) sobre las
     * que se resuelven los "posibles" en memoria.
     *
     * @var list<array{id_tipo_activ:int, nombre:string}>
     */
    private array $tiposFilas;

    /**
     * @param int|string $id id_tipo_activ a parsear (o '' si se rellenará por setters).
     * @param bool $extendida true para IDs con parte "actividad" de 2 dígitos.
     * @param list<array{id_tipo_activ:int, nombre:string}>|null $tiposFilas
     *        Filas inyectadas (tests / callers con datos propios). Si es `null`
     *        se cargan automáticamente vía {@see TipoActivMetadataLoader}.
     * @param array{
     *     sfsv?: array<string, int|string>,
     *     asistentes?: array<string, int|string>,
     *     actividad1digito?: array<string, int|string>,
     *     actividad2digitos?: array<string, int|string>,
     * }|null $maps Mapas inyectados (tests). Si es `null` se cargan vía loader.
     */
    public function __construct(
        int|string $id = '',
        bool $extendida = false,
        ?array $tiposFilas = null,
        ?array $maps = null
    ) {
        $maps = $maps ?? TipoActivMetadataLoader::maps();
        $this->aSfsv = $maps['sfsv'] ?? [];
        $this->aAsistentes = $maps['asistentes'] ?? [];
        $this->aActividad1Digito = $maps['actividad1digito'] ?? [];
        $this->aActividad2Digitos = $maps['actividad2digitos'] ?? [];

        $this->tiposFilas = $tiposFilas ?? TipoActivMetadataLoader::filas();

        $this->setExtendida($extendida);
        if ($id !== '') {
            $this->separarId((string)$id);
        }
        $this->getFlipAsistentes();
        $this->getFlipActividad1Digito();
        $this->getFlipActividad2Digitos();
        $this->getFlipSfsv();
    }

    /**
     * @return array<int|string, string>
     */
    private function getFlipSfsv(): array
    {
        if (empty($this->afSfsv)) {
            $this->afSfsv = array_flip($this->aSfsv);
        }

        return $this->afSfsv;
    }

    /**
     * @return array<int|string, string>
     */
    private function getFlipAsistentes(): array
    {
        if (empty($this->afAsistentes)) {
            $this->afAsistentes = array_flip($this->aAsistentes);
        }

        return $this->afAsistentes;
    }

    /**
     * @return array<int|string, string>
     */
    private function getFlipActividad1Digito(): array
    {
        if (empty($this->afActividad1Digito)) {
            $this->afActividad1Digito = array_flip($this->aActividad1Digito);
        }

        return $this->afActividad1Digito;
    }

    /**
     * @return array<int|string, string>
     */
    private function getFlipActividad2Digitos(): array
    {
        if (empty($this->afActividad2Digitos)) {
            $this->afActividad2Digitos = array_flip($this->aActividad2Digitos);
        }

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
            unset($this->aSfsv['all']);
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
            $txt_asistentes = $txt_svsf === 'sv' ? _('numerarios') : _('numerarias');
        }
        if ($this->getAsistentesText() === 'nax') {
            $txt_asistentes = _('numerarias auxiliares');
        }
        if ($this->getAsistentesText() === 'agd') {
            $txt_asistentes = $txt_svsf === 'sv' ? _('agregados') : _('agregadas');
        }
        if ($this->getAsistentesText() === 'sg') {
            $txt_asistentes = $txt_svsf === 'sv' ? _('coperadores') : _('coperadoras');
        }

        $txt_actividad = 'Actividad';
        if ($this->getActividadText() === 'crt') {
            $txt_actividad = _('curso de retiro');
        }
        if ($this->getActividadText() === 'ca') {
            $txt_actividad = _('curso anual');
        }
        if ($this->getActividadText() === 'cv' || $this->getActividadText() === 'cve') {
            $txt_actividad = _('convivencia');
        }

        return $txt_actividad . ' ' . $txt_asistentes;
    }

    /**
     * Recupera el atributo nom en formato de texto, sin el "(sin especificar)".
     */
    public function getNomGral(): string
    {
        $txt = $this->getSfsvText();
        if ($this->getAsistentesText() !== 'all') {
            $txt .= ' ' . $this->getAsistentesText();
        }
        if ($this->getActividadText() !== 'all') {
            $txt .= ' ' . $this->getActividadText();
        }
        if ($this->getNom_tipoId() !== 0 && $this->getNom_tipoText() !== 'all') {
            $txt .= ' ' . $this->getNom_tipoText();
        }

        return $txt;
    }

    public function getNom(): string
    {
        $txt = $this->getSfsvText();
        if ($this->getAsistentesText() !== 'all') {
            $txt .= ' ' . $this->getAsistentesText();
        }
        if ($this->getActividadText() !== 'all') {
            $txt .= ' ' . $this->getActividadText();
        }
        if ($this->getNom_tipoText() !== 'all') {
            $txt .= ' ' . $this->getNom_tipoText();
        }

        return $txt;
    }

    public function getSfsvText(): string
    {
        $aText = $this->getFlipSfsv();
        if (is_numeric($this->ssfsv)) {
            return $aText[$this->ssfsv] ?? 'all';
        }

        return 'all';
    }

    public function setSfsvText(?string $sSfsv): void
    {
        if ($sSfsv === null) {
            $sSfsv = 'all';
        }
        $this->ssfsv = (string)($this->aSfsv[$sSfsv] ?? '');
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

        return $this->lookupSfsvPosibles($aText);
    }

    public function getAsistentesText(): string
    {
        $aText = $this->getFlipAsistentes();
        if (is_numeric($this->sasistentes)) {
            return $aText[$this->sasistentes] ?? 'all';
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
            $asistentes_txt = '[';
            foreach ($a_asistentes_multiple as $asis) {
                $asistentes_txt .= $this->aAsistentes[$asis] ?? '';
            }
            $asistentes_txt .= ']';
        } else {
            $asis = $a_asistentes_multiple[0];
            $asistentes_txt = (string)($this->aAsistentes[$asis] ?? '');
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

        return $this->lookupAsistentesPosibles($aText, $regexp);
    }

    public function getActividadText(): string
    {
        if (is_numeric($this->sactividad)) {
            $aText = $this->extendida ? $this->getFlipActividad2Digitos() : $this->getFlipActividad1Digito();

            return $aText[$this->sactividad] ?? 'all';
        }

        return 'all';
    }

    public function setActividadText($sActividad): bool
    {
        if (is_string($sActividad)) {
            if (empty($sActividad) || $sActividad === '.') {
                $sActividad = 'all';
            }
            $this->sactividad = (string)($this->aActividad1Digito[$sActividad] ?? '');
            if ($this->extendida) {
                $this->sactividad = (string)($this->aActividad2Digitos[$sActividad] ?? '');
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
            return $aText[$this->sactividad] ?? 'all';
        }

        return 'all';
    }

    public function setActividad2DigitosText($sActividad): bool
    {
        if (is_string($sActividad)) {
            if (empty($sActividad)) {
                $sActividad = 'all';
            }
            $this->sactividad = (string)($this->aActividad2Digitos[$sActividad] ?? '');
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
        $this->sactividad = (string)$id;
    }

    public function getActividadRegexp(): string
    {
        return $this->getAsistentesRegexp() . $this->sactividad;
    }

    public function getActividadesPosibles1Digito(): array
    {
        $aText = $this->getFlipActividad1Digito();

        return $this->lookupActividadesPosibles(1, $aText, $this->getAsistentesRegexp());
    }

    public function getActividadesPosibles2Digitos(): array
    {
        $aText = $this->getFlipActividad2Digitos();

        return $this->lookupActividadesPosibles(2, $aText, $this->getAsistentesRegexp());
    }

    public function getNom_tipoText(): string
    {
        if (is_numeric($this->snom_tipo)) {
            if (!empty($this->afNom_tipo)) {
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
            $this->snom_tipo = (string)($this->aNom_tipo[$sNom_tipo] ?? '');
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
        $rta = $this->lookupNomTipoPosibles(3, $this->getActividadRegexp());
        $this->afNom_tipo = $rta['tipo_nom'];
        $this->aNom_tipo = $rta['nom_tipo'];

        return $rta['tipo_nom'];
    }

    public function getNom_tipoPosibles2Digitos(): array
    {
        $rta = $this->lookupNomTipoPosibles(2, $this->getActividadRegexp());
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
        return $this->lookupIdTipoPosibles($regexp, $this->getActividadRegexp());
    }

    public function getExtendida(): bool
    {
        return $this->extendida;
    }

    public function setExtendida(bool $extendida): void
    {
        $this->extendida = $extendida;
    }

    /**
     * @return list<array{id_tipo_activ:int, nombre:string}>
     */
    public function getTiposFilasCargadas(): array
    {
        return $this->tiposFilas;
    }

    // --- consultas en memoria (equivalente a PgTipoDeActividadRepository en lo usado por TiposActividades) ---

    /**
     * @return list<array{id_tipo_activ:int, nombre:string}>
     */
    private function tiposOrdenadosPorId(): array
    {
        $copy = $this->tiposFilas;
        usort(
            $copy,
            static fn (array $a, array $b): int => $a['id_tipo_activ'] <=> $b['id_tipo_activ']
        );

        return $copy;
    }

    private function matchesPgRegex(string $text, string $pattern): bool
    {
        if ($pattern === '') {
            return true;
        }
        $wrapped = $this->wrapRegex($pattern);
        $r = @preg_match($wrapped, $text);

        return $r === 1;
    }

    private function pgSubstringFrom(string $text, string $pattern): ?string
    {
        $wrapped = $this->wrapRegex($pattern);
        if (@preg_match($wrapped, $text, $m)) {
            return count($m) > 1 ? (string)$m[1] : ($m[0] ?? '');
        }

        return null;
    }

    private function wrapRegex(string $pattern): string
    {
        return '#' . str_replace('#', '\#', $pattern) . '#';
    }

    /**
     * @param array<int|string, string> $aText
     * @return array<string, string>
     */
    private function lookupSfsvPosibles(array $aText): array
    {
        $sfsv = [];
        $seen = [];
        foreach ($this->tiposOrdenadosPorId() as $row) {
            $idTxt = (string)$row['id_tipo_activ'];
            $ta1 = substr($idTxt, 0, 1);
            $seen[$ta1] = true;
        }
        ksort($seen, SORT_STRING);
        foreach (array_keys($seen) as $ta1) {
            if (isset($aText[$ta1])) {
                $sfsv[$ta1] = $aText[$ta1];
            }
        }

        return $sfsv;
    }

    /**
     * @param array<int|string, string> $aText
     * @return array<string, string>
     */
    private function lookupAsistentesPosibles(array $aText, string $filtro_regex_txt): array
    {
        $asistentes = [];
        $seen = [];
        foreach ($this->tiposOrdenadosPorId() as $row) {
            $idTxt = (string)$row['id_tipo_activ'];
            if (!$this->matchesPgRegex($idTxt, $filtro_regex_txt)) {
                continue;
            }
            $ta2 = substr($idTxt, 1, 1);
            $seen[$ta2] = true;
        }
        ksort($seen, SORT_STRING);
        foreach (array_keys($seen) as $ta2) {
            if (isset($aText[$ta2])) {
                $asistentes[$ta2] = $aText[$ta2];
            }
        }

        return $asistentes;
    }

    /**
     * @param array<int|string, string> $aText
     * @return array<string, string>
     */
    private function lookupActividadesPosibles(int $num_digitos, array $aText, string $expr_txt): array
    {
        $actividades = [];
        $seen = [];
        foreach ($this->tiposOrdenadosPorId() as $row) {
            $idTxt = (string)$row['id_tipo_activ'];
            if (!$this->matchesPgRegex($idTxt, $expr_txt)) {
                continue;
            }
            $ta3 = substr($idTxt, 2, $num_digitos);
            $seen[$ta3] = true;
        }
        ksort($seen, SORT_STRING);
        foreach (array_keys($seen) as $key) {
            if (isset($aText[$key])) {
                $actividades[$key] = $aText[$key];
            }
        }

        return $actividades;
    }

    /**
     * @return array{tipo_nom: array<string, string>, nom_tipo: array<int, string>}
     */
    private function lookupNomTipoPosibles(int $num_digitos, string $filtro_regexp_txt): array
    {
        $tipo_nom = [];
        $nom_tipo = [];
        $i = 0;
        $char_ini = 6 - $num_digitos;
        foreach ($this->tiposOrdenadosPorId() as $row) {
            $idTxt = (string)$row['id_tipo_activ'];
            if (!$this->matchesPgRegex($idTxt, $filtro_regexp_txt)) {
                continue;
            }
            $i++;
            $nom_tipo[$i] = $row['nombre'] . '#' . $idTxt;
            $num = substr($idTxt, $char_ini - 1, $num_digitos);
            $tipo_nom[$num] = $row['nombre'];
        }

        return [
            'tipo_nom' => $tipo_nom,
            'nom_tipo' => $nom_tipo,
        ];
    }

    /**
     * @return array<string, true>
     */
    private function lookupIdTipoPosibles(string $regexp, string $filtro_regexp_txt): array
    {
        $a_id_tipos = [];
        foreach ($this->tiposFilas as $row) {
            $idTxt = (string)$row['id_tipo_activ'];
            if (!$this->matchesPgRegex($idTxt, $filtro_regexp_txt)) {
                continue;
            }
            $sub = $this->pgSubstringFrom($idTxt, $regexp);
            if ($sub !== null && $sub !== '') {
                $a_id_tipos[$sub] = true;
            }
        }

        return $a_id_tipos;
    }
}
