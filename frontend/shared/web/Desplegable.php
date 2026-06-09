<?php

namespace frontend\shared\web;

/**
 * Fuente legacy de opciones (consulta ejecutable e iterable).
 *
 * @extends \Traversable<int, array<int|string, mixed>>
 */
interface DesplegableOpcionesIterable extends \Traversable
{
    public function execute(): void;
}

class Desplegable
{
    /** @var array<string, mixed>|null */
    protected ?array $aPrimary_key = null;
    protected ?string $sNombre = null;
    /** @var array<int|string, string>|DesplegableOpcionesIterable|null */
    protected array|DesplegableOpcionesIterable|null $oOpciones = null;
    protected ?string $sOpcion_sel = null;
    /** @var list<int|string>|null */
    protected ?array $aOpcion_no = null;
    protected bool|string|null $Blanco = null;
    protected ?string $valorBlanco = null;
    protected ?string $sAction = null;
    protected ?int $iSize = null;
    protected ?bool $bMultiple = null;
    protected ?int $iTabIndex = null;
    protected ?string $sClase = null;
    /** @var list<int|string>|null */
    protected ?array $aChecked = null;

    /**
     * @param array<string, mixed>|string $sNombre
     * @param array<int|string, string>|DesplegableOpcionesIterable|string $oOpciones
     */
    public function __construct(
        array|string $sNombre = '',
        array|DesplegableOpcionesIterable|string $oOpciones = '',
        string $sOpcion_sel = '',
        bool|string $bBlanco = '',
    ) {
        if (is_array($sNombre)) {
            $this->aPrimary_key = $sNombre;
            foreach ($sNombre as $nom_id => $val_id) {
                if ($val_id !== '' && property_exists($this, $nom_id)) {
                    $this->{$nom_id} = $val_id;
                }
            }
        } else {
            if ($sNombre !== '') {
                $this->sNombre = $sNombre;
            }
            if (is_array($oOpciones) || $oOpciones instanceof DesplegableOpcionesIterable) {
                $this->oOpciones = $oOpciones;
            }
            if ($sOpcion_sel !== '') {
                $this->sOpcion_sel = $sOpcion_sel;
            }
            if ($bBlanco !== '') {
                $this->Blanco = $bBlanco;
            }
        }
    }

    private static function scalarString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }

    /**
     * Construye un desplegable a partir de un mapa value => label (p. ej. opciones devueltas por *Dropdown en src).
     *
     * @param array<int|string, string> $opciones
     */
    final public static function desdeOpciones(array $opciones, string $nombre, bool $conBlanco = true): self
    {
        $d = new self();
        $d->setNombre($nombre);
        $d->setOpciones($opciones);
        if ($conBlanco) {
            $d->setBlanco(true);
        }

        return $d;
    }

    /**
     * @return array{
     *     multiple: bool,
     *     size: int,
     *     clase: string,
     *     action: string,
     *     nombre: string,
     *     blanco: bool|string|null,
     *     valorBlanco: string,
     *     opcion_sel: string,
     *     options: array<int|string, string>
     * }
     */
    public function export(): array
    {
        return [
            'multiple' => $this->bMultiple ?? false,
            'size' => $this->iSize ?? 0,
            'clase' => $this->sClase ?? '',
            'action' => $this->sAction ?? '',
            'nombre' => $this->sNombre ?? '',
            'blanco' => $this->Blanco,
            'valorBlanco' => $this->valorBlanco ?? '',
            'opcion_sel' => $this->sOpcion_sel ?? '',
            'options' => $this->getArrayOpciones(),
        ];
    }

    /** @param array<string, mixed> $data */
    public function import(array $data): void
    {
        $this->bMultiple = (bool) ($data['multiple'] ?? false);
        $size = $data['size'] ?? null;
        $this->iSize = is_int($size) ? $size : (is_string($size) && is_numeric($size) ? (int) $size : null);
        $this->sClase = self::scalarString($data['clase'] ?? '');
        $this->sAction = self::scalarString($data['action'] ?? '');
        $this->sNombre = self::scalarString($data['nombre'] ?? '');
        $blanco = $data['blanco'] ?? null;
        $this->Blanco = is_bool($blanco) || is_string($blanco) ? $blanco : null;
        $this->valorBlanco = self::scalarString($data['valorBlanco'] ?? '');
        $this->sOpcion_sel = self::scalarString($data['opcion_sel'] ?? '');
        $options = $data['options'] ?? [];
        if (is_array($options)) {
            $normalized = [];
            foreach ($options as $k => $v) {
                $normalized[self::scalarString($k)] = self::scalarString($v);
            }
            $this->oOpciones = $normalized;
        } else {
            $this->oOpciones = null;
        }
    }

    public function desplegable(): string
    {
        $nombre = $this->sNombre ?? '';
        $multiple = empty($this->bMultiple) ? '' : 'multiple';
        $tab_index = empty($this->iTabIndex) ? '' : 'tabindex="' . $this->iTabIndex . '"';
        $size = empty($this->iSize) ? '' : 'size="' . $this->iSize . '"';
        $clase = empty($this->sClase) ? '' : 'class="' . $this->sClase . '"';
        if (empty($this->sAction)) {
            $sHtml = '<select ' . $multiple . ' ' . $tab_index . ' id="' . $nombre . '" name="' . $nombre . '" ' . $clase . ' ' . $size . '>';
        } else {
            $sHtml = '<select ' . $multiple . ' ' . $tab_index . ' id="' . $nombre . '" name="' . $nombre . '" ' . $clase . ' ' . $size . ' onChange="' . $this->sAction . '" >';
        }
        $sHtml .= $this->options();
        $sHtml .= '</select>';

        return $sHtml;
    }

    public function cuadros_check(): string
    {
        $txt = '';
        $camp = ($this->sNombre ?? '') . '[]';
        $aChecked = $this->aChecked ?? [];
        $aOpcionNo = $this->aOpcion_no ?? [];

        if ($this->oOpciones instanceof DesplegableOpcionesIterable) {
            $this->oOpciones->execute();
            foreach ($this->oOpciones as $row) {
                $a = isset($row[1]) ? 1 : 0;
                $value = self::scalarString($row[0] ?? '');
                $label = self::scalarString($row[$a] ?? '');
                $chk = in_array($value, $aChecked, true) ? 'checked' : '';

                if (in_array($value, $aOpcionNo, true)) {
                    continue;
                }
                $txt .= '   <input type="Checkbox" id="' . $camp . '" name="' . $camp . '" value="' . $value . '" ' . $chk . '>' . $label;
            }
        } elseif (is_array($this->oOpciones)) {
            foreach ($this->oOpciones as $key => $val) {
                $chk = in_array($key, $aChecked, true) ? 'checked' : '';
                if (in_array($key, $aOpcionNo, true)) {
                    continue;
                }
                $txt .= '   <input type="Checkbox" id="' . $camp . '" name="' . $camp . '" value="' . self::scalarString($key) . '" ' . $chk . '>' . self::scalarString($val);
            }
        } else {
            $msg_err = _("tiene que ser un array") . ': ' . __FILE__ . ': line ' . __LINE__;
            exit($msg_err);
        }

        return $txt;
    }

    /** @return array<int|string, string> */
    public function getArrayOpciones(): array
    {
        $a_options = [];
        if ($this->oOpciones instanceof DesplegableOpcionesIterable) {
            $this->oOpciones->execute();
            foreach ($this->oOpciones as $row) {
                $a = isset($row[1]) ? 1 : 0;
                $a_options[self::scalarString($row[0] ?? '')] = self::scalarString($row[$a] ?? '');
            }
        } elseif (is_array($this->oOpciones)) {
            $a_options = $this->oOpciones;
        }

        return $a_options;
    }

    public function options(): string
    {
        $txt = '';
        if (!empty($this->Blanco)) {
            if (!empty($this->valorBlanco)) {
                $txt .= '<option value="' . $this->valorBlanco . '"></option>';
            } else {
                $txt .= '<option></option>';
            }
        }
        $a_opciones = $this->getArrayOpciones();
        $aOpcionNo = $this->aOpcion_no ?? [];
        $opcionSel = $this->sOpcion_sel ?? '';
        foreach ($a_opciones as $key => $val) {
            $sel = self::scalarString($key) === self::scalarString($opcionSel) ? 'selected' : '';
            if (in_array($key, $aOpcionNo, true)) {
                continue;
            }
            $txt .= '<option value="' . self::scalarString($key) . '" ' . $sel . '>' . self::scalarString($val) . '</option>';
        }

        return $txt;
    }

    public function setNombre(string $sNombre): void
    {
        $this->sNombre = $sNombre;
    }

    /** @param array<int|string, string>|DesplegableOpcionesIterable $aOpciones */
    public function setOpciones(array|DesplegableOpcionesIterable $aOpciones): void
    {
        $this->oOpciones = $aOpciones;
    }

    /** @return array<int|string, string>|DesplegableOpcionesIterable|null */
    public function getOpciones(): array|DesplegableOpcionesIterable|null
    {
        return $this->oOpciones;
    }

    public function setOpcion_sel(string $sOpcion_sel): void
    {
        $this->sOpcion_sel = $sOpcion_sel;
    }

    /** @param list<int|string> $aChecked */
    public function setChecked(array $aChecked): void
    {
        $this->aChecked = $aChecked;
    }

    /** @param list<int|string> $aOpcion_no */
    public function setOpcion_no(array $aOpcion_no): void
    {
        $this->aOpcion_no = $aOpcion_no;
    }

    public function setBlanco(bool|string $bBlanco): void
    {
        $this->Blanco = $bBlanco;
    }

    public function setValBlanco(string $valorBlanco): void
    {
        $this->valorBlanco = $valorBlanco;
    }

    public function setAction(string $sAction): void
    {
        $this->sAction = $sAction;
    }

    public function setSize(int $iSize): void
    {
        $this->iSize = $iSize;
    }

    public function setMultiple(bool $bMultiple): void
    {
        $this->bMultiple = $bMultiple;
    }

    public function setClase(string $sClase): void
    {
        $this->sClase = $sClase;
    }
}
