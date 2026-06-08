<?php

namespace src\shared\domain;

use PDO;
use PDOStatement;
use src\shared\infrastructure\logging\GestorErrores;

/**
 * DatosCampo
 *
 * Classe per a gestionar les dades referents a un camp de la Base de Dades
 *
 * @subpackage model
 * @author
 * @version 1.0
 * @created 22/9/2010
 *
 * @package delegación
 */
class DatosCampo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    private ?string $snom_tabla = null;
    private ?string $snom_camp = null;
    private ?string $metodoGet = null;
    private ?string $metodoSet = null;
    private ?string $setiqueta = null;
    private bool $baviso = true;
    private ?string $stipo = null;
    private ?string $sargument = null;
    private ?string $sargument2 = null;
    private ?string $sargument3 = null;
    private ?string $saccion = null;
    private ?string $sdepende = null;
    /** @var array<int|string, mixed>|null */
    private ?array $alista = null;
    private ?string $sRegExp = null;
    private ?string $sRegExpText = null;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    /** @var array<string, mixed> */
    private array $aPrimary_key = [];

    /**
     * @param array<string, mixed>|string $a_id
     */
    public function __construct(array|string $a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 's' . $nom_id;
                if ($val_id !== '') {
                    $this->$nom_id = $val_id;
                }
            }
        }
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Esta función devuelve datos sobre el campo de una tabla
     *
     * $oDB es la conexión al Postgresql
     * $tabla es el nombre de la tabla
     * $camp es el nombre del campo
     * $que es el dato que queremos saber:
     *        "longitud"    longitud del campo
     *        "nulo"        si es permite nulo o no
     *        "tipo"        int, varchar, bool...
     *        "valor"        valor por defecto
     *
     * @return string|int|false|null
     */
    public function datos_campo(PDO $oDB, string $que): string|int|false|null
    {
        $tabla = $this->getNom_tabla();
        $camp = $this->getNom_camp();
        if ($tabla && $camp) {
            $sql_get_fields = "
				SELECT 
					a.attrelid,
					a.attnum,
					a.attname AS field, 
					t.typname AS type, 
					a.attlen AS length,
					a.atttypmod AS lengthvar,
					a.attnotnull AS notnull,
					a.atthasdef
				FROM 
					pg_attribute a, 
					pg_type t
				WHERE 
					a.attnum > 0
					and a.attrelid = ('\"'|| current_schema() || '\"' || '.$tabla')::regclass
					and a.atttypid = t.oid
					and a.attname = '$camp'
				ORDER BY a.attnum
			";
            $oDBSt_res_fields = $oDB->query($sql_get_fields);
            if ($oDBSt_res_fields === false) {
                $sClauError = 'DatosCampo.datos_campo';
                if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, (string) __LINE__, __FILE__);
                }
                return false;
            }
            $row = $oDBSt_res_fields->fetch(PDO::FETCH_ASSOC);
            if (!is_array($row)) {
                $err_txt = sprintf(_("No está definido el campo %s en DatosCampo para la tabla %s"), $camp, $tabla);
                exit($err_txt);
            }
            $length = isset($row['length']) && is_numeric($row['length']) ? (int) $row['length'] : 0;
            $lengthvar = isset($row['lengthvar']) && is_numeric($row['lengthvar']) ? (int) $row['lengthvar'] : 0;
            if ($length > 0) {
                $llarg = $length;
            } elseif ($lengthvar > 0) {
                $llarg = $lengthvar - 4;
            } else {
                $llarg = 'var';
            }
            $type = isset($row['type']) && is_scalar($row['type']) ? (string) $row['type'] : '';
            if ($type === 'date') {
                $llarg = 8;
            }
            $null = $row['notnull'] ?? null;

            switch ($que) {
                case 'longitud':
                    return is_int($llarg) ? $llarg : $llarg;
                case 'nulo':
                    return is_scalar($null) ? (string) $null : null;
                case 'tipo':
                    return $type;
                case 'valor':
                    $rowdefault = '';
                    if (($row['atthasdef'] ?? false) == true) {
                        $attnum = isset($row['attnum']) && is_numeric($row['attnum']) ? (int) $row['attnum'] : 0;
                        $attrelid = isset($row['attrelid']) && is_numeric($row['attrelid']) ? (int) $row['attrelid'] : 0;
                        $sql_get_default = "
							SELECT pg_get_expr(adbin, adrelid) as defvalor
							FROM pg_attrdef d
						   	WHERE d.adnum =" . $attnum . " AND 
							d.adrelid =" . $attrelid;

                        $oDBSt_def_res = $oDB->query($sql_get_default);
                        if (!$oDBSt_def_res instanceof PDOStatement || !$oDBSt_def_res->rowCount()) {
                            $rowdefault = "";
                        } else {
                            $rowdefault = $oDBSt_def_res->fetchColumn();
                            $rta = preg_match_all("/^'([\w]+)'::(.*)/", (string) $rowdefault, $matches, PREG_SET_ORDER);
                            if (!empty($rta)) {
                                $rowdefault = $matches[0][1];
                            } elseif (strstr((string) $rowdefault, '(')) {
                                $rowdefault = "function";
                            }
                        }
                        return $rowdefault;
                    }

                    return '';
            }
        }

        return null;
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function getNom_tabla(): ?string
    {
        return $this->snom_tabla;
    }

    public function setNom_tabla(?string $snom_tabla): void
    {
        $this->snom_tabla = $snom_tabla;
    }

    public function getNom_camp(): ?string
    {
        return $this->snom_camp;
    }

    public function setNom_camp(?string $snom_camp): void
    {
        $this->snom_camp = $snom_camp;
    }

    public function getEtiqueta(): ?string
    {
        return $this->setiqueta;
    }

    public function setEtiqueta(?string $setiqueta): void
    {
        $this->setiqueta = $setiqueta;
    }

    public function getAviso(): bool
    {
        return $this->baviso;
    }

    public function setAviso(bool $baviso): void
    {
        $this->baviso = $baviso;
    }

    public function getTipo(): ?string
    {
        return $this->stipo;
    }

    public function setTipo(?string $stipo): void
    {
        $this->stipo = $stipo;
    }

    public function getArgument(): ?string
    {
        return $this->sargument;
    }

    public function setArgument(?string $sargument): void
    {
        $this->sargument = $sargument;
    }

    public function getArgument2(): ?string
    {
        return $this->sargument2;
    }

    public function setArgument2(?string $sargument2): void
    {
        $this->sargument2 = $sargument2;
    }

    public function getArgument3(): ?string
    {
        return $this->sargument3;
    }

    public function setArgument3(?string $sargument3): void
    {
        $this->sargument3 = $sargument3;
    }

    public function getAccion(): ?string
    {
        return $this->saccion;
    }

    public function setAccion(?string $saccion): void
    {
        $this->saccion = $saccion;
    }

    public function getDepende(): ?string
    {
        return $this->sdepende;
    }

    public function setDepende(?string $sdepende): void
    {
        $this->sdepende = $sdepende;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getLista(): ?array
    {
        return $this->alista;
    }

    /**
     * @param array<int|string, mixed>|null $alista
     */
    public function setLista(?array $alista): void
    {
        $this->alista = $alista;
    }

    public function getRegExp(): ?string
    {
        return $this->sRegExp;
    }

    public function setRegExp(?string $sRegExp): void
    {
        $this->sRegExp = $sRegExp;
    }

    public function getRegExpText(): ?string
    {
        return $this->sRegExpText;
    }

    public function setRegExpText(?string $sRegExpText): void
    {
        $this->sRegExpText = $sRegExpText;
    }

    public function getMetodoGet(): ?string
    {
        return $this->metodoGet;
    }

    public function setMetodoGet(?string $metodoGet): void
    {
        $this->metodoGet = $metodoGet;
    }

    public function getMetodoSet(): ?string
    {
        return $this->metodoSet;
    }

    public function setMetodoSet(?string $metodoSet): void
    {
        $this->metodoSet = $metodoSet;
    }
}
