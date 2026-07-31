<?php

namespace src\shared\domain;

use src\shared\config\ConfigGlobal;
use src\shared\domain\contracts\DatosFichaInterface;
use src\shared\infrastructure\DependencyResolver;

/**
 * Clase que implementa la entidad d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/04/2018
 */
class DatosFormRepo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ?string $camposForm = null;
    private ?string $camposNo = null;

    private ?object $oFicha = null;
    /** @var array<string, string> */
    private array $aOpciones_txt = [];
    private string $mod = '';
    /** @var array<int|string, mixed> */
    private array $aOpcion_no = [];

    /**
     * @return array{fields: list<array<string, mixed>>, camposForm: string, camposNo: string}
     */
    public function getFormularioData(): array
    {
        $camposForm = '';
        $camposNo = '';
        /** @var list<array<string, mixed>> $formData */
        $formData = [];

        $ficha = $this->getFicha();
        if ($ficha === null) {
            return [
                'fields' => [],
                'camposForm' => '',
                'camposNo' => '',
            ];
        }
        /** @var DatosFichaInterface $typedFicha */
        $typedFicha = $ficha;
        $primaryKeyFields = self::primaryKeyFieldNames($ficha);

        foreach ($typedFicha->getDatosCampos() as $oDatosCampo) {
            $metodo = $oDatosCampo->getMetodoGet() ?? '';
            $nom_camp = $oDatosCampo->getNom_camp();
            if ($nom_camp === null) {
                continue;
            }

            if ($this->mod === 'nuevo') {
                $valor_camp = '';
            } elseif (substr($metodo, -2) === 'Vo') {
                $valor_camp = $typedFicha->$metodo()->value();
            } else {
                $valor_camp = $typedFicha->$metodo();
            }
            $var_1 = $oDatosCampo->getArgument();
            $eti = $oDatosCampo->getEtiqueta();
            $tipo = $oDatosCampo->getTipo();

            $camposForm .= empty($camposForm) ? $nom_camp : '!' . $nom_camp;

            $field = [
                'tipo' => $tipo,
                'nombre' => $nom_camp,
                'etiqueta' => $eti,
                'valor' => $valor_camp,
                'argument' => $var_1,
                'readonly' => false,
            ];

            switch ($tipo) {
                case "ver":
                    $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                    break;
                case "decimal":
                case "texto":
                    $field['size'] = $var_1;
                    break;
                case "fecha":
                    $locale_us = ConfigGlobal::is_locale_us();
                    if (!empty($valor_camp)) {
                        $field['valor_txt'] = $valor_camp->getFromLocal();
                    } else {
                        $field['valor_txt'] = $valor_camp;
                    }
                    $field['locale_us'] = $locale_us;
                    break;
                case "opciones":
                    $acc = $oDatosCampo->getAccion();
                    $var_3 = $oDatosCampo->getArgument3() ?? '';

                    if (is_string($var_1) && $var_1 !== '' && (interface_exists($var_1) || class_exists($var_1))) {
                        $RepoRelacionado = DependencyResolver::get($var_1);
                        $field['opciones'] = $RepoRelacionado->$var_3();
                    } else {
                        $field['opciones'] = [];
                    }

                    $field['accion'] = $acc;
                    $field['aOpcion_no'] = $this->aOpcion_no;
                    // PK (p.ej. id_mod / nombre del módulo): editable al crear, solo lectura al modificar.
                    if ($this->mod !== 'nuevo' && in_array($nom_camp, $primaryKeyFields, true)) {
                        $field['readonly'] = true;
                        $opciones = is_array($field['opciones']) ? $field['opciones'] : [];
                        $field['valor_txt'] = self::mixedToString(
                            $opciones[$valor_camp] ?? ($opciones[(string) $valor_camp] ?? $valor_camp)
                        );
                    }
                    break;
                case "depende":
                    $field['opciones_txt'] = $this->aOpciones_txt[$nom_camp] ?? '';
                    break;
                case "array":
                    $acc = $oDatosCampo->getAccion();
                    $field['accion'] = $acc;
                    $field['opciones'] = self::opcionesDesdeLista($oDatosCampo->getLista());
                    $field['aOpcion_no'] = $this->aOpcion_no;
                    break;
                case "check":
                    $field['checked'] = \src\shared\domain\helpers\FuncTablasSupport::isTrue($valor_camp);
                    $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                    break;
                case "checks":
                    $field['opciones'] = self::opcionesDesdeLista($oDatosCampo->getLista());
                    $field['valores_seleccionados'] = is_array($valor_camp) ? $valor_camp : [];
                    break;
            }

            $formData[] = $field;
        }
        $this->camposForm = $camposForm;
        $this->camposNo = $camposNo;

        return [
            'fields' => $formData,
            'camposForm' => $camposForm,
            'camposNo' => $camposNo,
        ];
    }

    public function getFormulario(): string
    {
        $formData = $this->getFormularioData();
        $formulario = '';

        foreach ($formData['fields'] as $field) {
            $tipo = self::mixedToString($field['tipo'] ?? null);
            $nom_camp = self::mixedToString($field['nombre'] ?? null);
            $eti = self::mixedToString($field['etiqueta'] ?? null);
            $valor_camp = $field['valor'] ?? null;
            $valorCampStr = self::mixedToString($valor_camp);

            switch ($tipo) {
                case "ver":
                    if ($this->mod !== 'nuevo') {
                        $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                        $formulario .= "<td class=contenido>" . htmlspecialchars($valorCampStr) . "</td></tr>";
                        $formulario .= "<input type='hidden' name='$nom_camp' value=\"" . htmlspecialchars($valorCampStr) . "\"></td></tr>";
                    }
                    break;
                case "decimal":
                case "texto":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $size = self::mixedToString($field['size'] ?? null);
                    $formulario .= "<td class=contenido><input type='text' name='$nom_camp' value=\"" . htmlspecialchars($valorCampStr) . "\" size='$size'></td></tr>";
                    break;
                case "fecha":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $locale_us = (bool) ($field['locale_us'] ?? false);
                    $valor_camp_txt = self::mixedToString($field['valor_txt'] ?? null);
                    $formulario .= "<td class=contenido><input class=\"fecha\" type=\"text\" id=\"$nom_camp\" name=\"$nom_camp\" value=\"$valor_camp_txt\" 
									onchange='fnjs_comprobar_fecha(\"#$nom_camp\",$locale_us)'>";
                    break;
                case "opciones":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    if (!empty($field['readonly'])) {
                        $valorTxt = self::mixedToString($field['valor_txt'] ?? $valorCampStr);
                        $formulario .= "<td class=contenido>" . htmlspecialchars($valorTxt) . "</td></tr>";
                        $formulario .= "<input type='hidden' name='$nom_camp' value=\"" . htmlspecialchars($valorCampStr) . "\">";
                        break;
                    }
                    $acc = self::mixedToString($field['accion'] ?? null);
                    $a_opciones = is_array($field['opciones'] ?? null) ? $field['opciones'] : [];
                    $aOpcion_no = is_array($field['aOpcion_no'] ?? null) ? $field['aOpcion_no'] : [];

                    $accion = empty($acc) ? '' : "onchange=\"fnjs_actualizar_depende('$nom_camp','$acc');\" ";
                    $formulario .= "<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\" $accion>";
                    $formulario .= "<option></option>";
                    foreach ($a_opciones as $key => $val) {
                        $keyStr = self::mixedToString($key);
                        $valStr = self::mixedToString($val);
                        if ($keyStr === $valorCampStr) {
                            $sel = 'selected';
                        } else {
                            $sel = '';
                        }
                        if (!empty($aOpcion_no) && in_array($key, $aOpcion_no, true)) {
                            continue;
                        }
                        $formulario .= "<option value=\"$keyStr\" $sel>$valStr</option>";
                    }
                    $formulario .= "</select></td></tr>";
                    break;
                case "depende":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $formulario .= "<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\">";
                    $formulario .= self::mixedToString($field['opciones_txt'] ?? null);
                    $formulario .= "</select></td></tr>";
                    break;
                case "array":
                    $acc = self::mixedToString($field['accion'] ?? null);
                    $a_opciones = is_array($field['opciones'] ?? null) ? $field['opciones'] : [];
                    $aOpcion_no = is_array($field['aOpcion_no'] ?? null) ? $field['aOpcion_no'] : [];
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $accion = empty($acc) ? '' : "onchange=\"fnjs_actualizar_depende('$nom_camp','$acc');\" ";
                    $formulario .= "<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\" $accion>";
                    $formulario .= "<option></option>";
                    foreach ($a_opciones as $key => $val) {
                        $keyStr = self::mixedToString($key);
                        $valStr = self::mixedToString($val);
                        if ($keyStr === $valorCampStr) {
                            $sel = 'selected';
                        } else {
                            $sel = '';
                        }
                        if (!empty($aOpcion_no) && in_array($key, $aOpcion_no, true)) {
                            continue;
                        }
                        $formulario .= "<option value=\"$keyStr\" $sel>$valStr</option>";
                    }
                    $formulario .= "</select></td></tr>";
                    break;
                case "check":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $chk = !empty($field['checked']) ? "checked" : "";
                    $formulario .= "<td class=contenido><input type='checkbox' name='$nom_camp' $chk>";
                    break;
                case "checks":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $a_opciones = is_array($field['opciones'] ?? null) ? $field['opciones'] : [];
                    $valores_seleccionados = is_array($field['valores_seleccionados'] ?? null) ? $field['valores_seleccionados'] : [];
                    $formulario .= "<td class=contenido>";
                    foreach ($a_opciones as $key => $val) {
                        $keyStr = self::mixedToString($key);
                        $valStr = self::mixedToString($val);
                        $chk = in_array($key, $valores_seleccionados) ? 'checked' : '';
                        $formulario .= "<input type='checkbox' name='{$nom_camp}[]' value=\"$keyStr\" $chk>$valStr ";
                    }
                    $formulario .= "</td></tr>";
                    break;
                case "hidden":
                    $formulario .= "<input type='hidden' name='$nom_camp' value ='$valorCampStr'>";
                    break;
            }
        }

        return $formulario;
    }

    public function getCamposForm(): string
    {
        if (!isset($this->camposForm)) {
            $camposForm = '';
            $ficha = $this->getFicha();
            if ($ficha !== null) {
                /** @var DatosFichaInterface $typedFicha */
                $typedFicha = $ficha;
                foreach ($typedFicha->getDatosCampos() as $oDatosCampo) {
                    $nom_camp = $oDatosCampo->getNom_camp();
                    if ($nom_camp === null) {
                        continue;
                    }
                    $camposForm .= empty($camposForm) ? $nom_camp : '!' . $nom_camp;
                }
            }
            $this->camposForm = $camposForm;
        }

        return $this->camposForm ?? '';
    }

    public function getCamposNo(): string
    {
        if (!isset($this->camposNo)) {
            $camposNo = '';
            $ficha = $this->getFicha();
            if ($ficha !== null) {
                /** @var DatosFichaInterface $typedFicha */
                $typedFicha = $ficha;
                foreach ($typedFicha->getDatosCampos() as $oDatosCampo) {
                    $nom_camp = $oDatosCampo->getNom_camp();
                    if ($nom_camp === null) {
                        continue;
                    }
                    switch ($oDatosCampo->getTipo()) {
                        case "ver":
                            $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                            break;
                        case "check":
                            $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                            break;
                    }
                }
            }
            $this->camposNo = $camposNo;
        }

        return $this->camposNo ?? '';
    }

    public function setFicha(?object $oFicha): void
    {
        $this->oFicha = $oFicha;
    }

    public function getFicha(): ?object
    {
        return $this->oFicha;
    }

    /**
     * @param array<string, string> $aOpciones_txt
     */
    public function setArrayOpcionesTxt(array $aOpciones_txt): void
    {
        $this->aOpciones_txt = $aOpciones_txt;
    }

    public function getMod(): string
    {
        return $this->mod;
    }

    public function setMod(string $mod): void
    {
        $this->mod = $mod;
    }

    /**
     * Convierte el valor de {@see DatosCampo::getLista()} en mapa value => etiqueta
     * (misma semántica que {@see \frontend\shared\web\Desplegable::getArrayOpciones}).
     *
     * @return array<int|string, mixed>
     */
    private static function opcionesDesdeLista(mixed $lista): array
    {
        if ($lista === null || $lista === '') {
            return [];
        }
        if (is_array($lista)) {
            return $lista;
        }
        if (is_object($lista) && method_exists($lista, 'execute')) {
            $lista->execute();
        }
        $a_options = [];
        if (is_object($lista) && is_iterable($lista)) {
            foreach ($lista as $row) {
                $a = isset($row[1]) ? 1 : 0;
                $a_options[$row[0]] = $row[$a];
            }
        }

        return $a_options;
    }

    private static function mixedToString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Nombres de campos de la clave primaria de la ficha.
     * Duck-typing: muchas entidades cumplen el contrato sin declarar DatosFichaInterface.
     *
     * @return list<string>
     */
    private static function primaryKeyFieldNames(object $ficha): array
    {
        if (!method_exists($ficha, 'getPrimary_key')) {
            return [];
        }
        $primaryKey = $ficha->getPrimary_key();
        if (is_string($primaryKey) && $primaryKey !== '') {
            return [$primaryKey];
        }
        if (!is_array($primaryKey)) {
            return [];
        }

        $fields = [];
        foreach ($primaryKey as $key => $value) {
            if (is_int($key)) {
                $fields[] = is_scalar($value) ? (string) $value : '';
            } else {
                $fields[] = $key;
            }
        }

        return $fields;
    }
}
