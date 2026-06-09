<?php

namespace frontend\shared\web;

class DesplegableArray extends Desplegable
{
    /** @var string|list<string>|null */
    private string|array|null $sSeleccionados = null;
    private ?string $sNomConjunto = null;
    private ?string $sAccionConjunto = null;
    private ?int $iTabIndexIni = null;

    /**
     * @param string|list<string> $id
     * @param array<int|string, string>|DesplegableOpcionesIterable|string $Opciones
     */
    public function __construct(
        string|array $id = '',
        array|DesplegableOpcionesIterable|string $Opciones = '',
        string $Nom = '',
    ) {
        if ($id !== '' && $id !== []) {
            $this->sSeleccionados = $id;
        }
        if (is_array($Opciones) || $Opciones instanceof DesplegableOpcionesIterable) {
            $this->oOpciones = $Opciones;
        }
        if ($Nom !== '') {
            $this->sNomConjunto = $Nom;
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
     * @return array{
     *     multiple: bool,
     *     size: int,
     *     clase: string,
     *     action: string,
     *     nombre: string,
     *     blanco: bool|string|null,
     *     valorBlanco: string,
     *     opcion_sel: string,
     *     options: array<int|string, string>,
     *     Seleccionados: string|list<string>|null,
     *     NomConjunto: string|null,
     *     AccionConjunto: string|null,
     *     TabIndexIni: int|null
     * }
     */
    public function export(): array
    {
        $a_properties = parent::export();

        $a_properties['Seleccionados'] = $this->sSeleccionados;
        $a_properties['NomConjunto'] = $this->sNomConjunto;
        $a_properties['AccionConjunto'] = $this->sAccionConjunto;
        $a_properties['TabIndexIni'] = $this->iTabIndexIni;

        return $a_properties;
    }

    /** @param array<string, mixed> $data */
    public function import(array $data): void
    {
        $sel = $data['Seleccionados'] ?? '';
        if (is_array($sel)) {
            $normalized = [];
            foreach ($sel as $item) {
                $normalized[] = self::scalarString($item);
            }
            $this->sSeleccionados = $normalized;
        } else {
            $this->sSeleccionados = self::scalarString($sel);
        }
        $this->sNomConjunto = self::scalarString($data['NomConjunto'] ?? '') ?: null;
        $this->sAccionConjunto = self::scalarString($data['AccionConjunto'] ?? '') ?: null;
        $tab = $data['TabIndexIni'] ?? 0;
        $this->iTabIndexIni = is_int($tab) ? $tab : (is_string($tab) && is_numeric($tab) ? (int) $tab : null);

        parent::import($data);
    }

    /**
     * @return string html `<select>...</select>`
     */
    public function ListaSelects(): string
    {
        $nomConjunto = $this->sNomConjunto ?? '';
        $aSeleccionados = [];
        if (is_string($this->sSeleccionados)) {
            $aSeleccionados = explode(',', $this->sSeleccionados);
        } elseif (is_array($this->sSeleccionados)) {
            $aSeleccionados = $this->sSeleccionados;
        }

        $span = $nomConjunto . '_span';
        $n = 0;
        $sLista = '<span id="' . $span . '" >';
        if ($aSeleccionados !== []) {
            foreach ($aSeleccionados as $id) {
                $this->sNombre = $nomConjunto . '[' . $n . ']';
                if (isset($this->iTabIndexIni)) {
                    $this->iTabIndex = $this->iTabIndexIni + $n;
                }
                $this->sOpcion_sel = self::scalarString($id);
                $this->sAction = "fnjs_comprobar_select('" . $nomConjunto . "',$n);";

                $sLista .= $this->desplegable();
                $n++;
            }
        }
        $sLista .= '</span>';
        $this->sNombre = $nomConjunto . '_mas';
        $this->sAction = $this->sAccionConjunto;
        $this->sOpcion_sel = '';
        $sLista .= $this->desplegable();
        $sLista .= '<input type=hidden name=\'' . $nomConjunto . '_num\' id=\'' . $nomConjunto . '_num\' value=' . $n . '>';

        return $sLista;
    }

    public function ListaSelectsJs(): string
    {
        $nom = $this->sNomConjunto ?? '';
        $mas = $nom . '_mas';
        $num = $nom . '_num';
        $span = $nom . '_span';
        $tab = $this->iTabIndexIni ?? 10;

        $txt_js = "\n\t\t\tvar num=$('#$num');";
        $txt_js .= "\n\t\t\tvar id_mas=$('#$mas').val();";
        $txt_js .= "\n\t\t\tvar n=Number(num.val());";
        $txt_js .= "\n\t\t\tvar txt;";
        $txt_js .= "\n\t\t\tvar tab=$tab+n;";

        $txt_js .= "\n\t\t\ttxt='<select tabindex=";
        $txt_js .= "'+tab+' id=" . $nom . "['+n+'] name=" . $nom . "['+n+'] class=contenido onChange=fnjs_comprobar_select(\'" . $nom . "\','+n+');>';";
        $txt_js .= "\n\t\t\ttxt += '" . addslashes($this->options()) . "';";
        $txt_js .= "\n\t\t\ttxt += '</select>';";
        $txt_js .= "\n\t\t\t// antes del desplegable de añadir";
        $txt_js .= "\n\t\t\t$('#$span').append(txt);";
        $txt_js .= "\n\t\t\t// selecciono el valor del desplegable";
        $txt_js .= "\n\t\t\tvar nom='#" . $nom . "\\\\['+n+'\\\\]';";
        $txt_js .= "\n\t\t\t$(nom).val(id_mas);";
        $txt_js .= "\n\t\t\tn1=n+1;";
        $txt_js .= "\n\t\t\tnum.val(n1);";
        $txt_js .= "\n\t\t\t$('#$mas').val('');";
        $txt_js .= "\n";

        return $txt_js;
    }

    public function ComprobarSelectJs(): string
    {
        $txt_js = "\nfnjs_comprobar_select = function (nom,n) {";
        $txt_js .= "\n\t" . 'var id="#"+nom+"\\\\["+n+"\\\\]";';
        $txt_js .= "\n\t" . 'var valor=$(id).val();';
        $txt_js .= "\n\tif (!valor) {";
        $txt_js .= "\n\t\t" . '$(id).hide();';
        $txt_js .= "\n\t}";
        $txt_js .= "\n}";
        $txt_js .= "\n";

        return $txt_js;
    }

    public function setNomConjunto(string $sNomConjunto): void
    {
        $this->sNomConjunto = $sNomConjunto;
    }

    public function setAccionConjunto(string $sAccionConjunto): void
    {
        $this->sAccionConjunto = $sAccionConjunto;
    }

    /** @param string|list<string> $sSeleccionados */
    public function setSeleccionados(string|array $sSeleccionados): void
    {
        $this->sSeleccionados = $sSeleccionados;
    }
}
