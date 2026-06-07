<?php

namespace src\permisos\domain;

use src\shared\config\ConfigGlobal;

/**
 * Lógica histórica de bits + helpers de HTML. Los mapas canónicos viven en clases `*Bits`
 * por ámbito ({@see MenuDlPermissionBits}, {@see \src\menus\domain\PermisoMenuBits},
 * {@see \src\ubis\domain\CuadrosLaborBits}, etc.); la pintura de formularios en `frontend/`
 * usa {@see \frontend\shared\permisos\MenuPermisoMenuHtml} y afines cuando el flujo es por API.
 */
abstract class XPermisos
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /** @var array<string, int> */
    protected array $permissions = [];
    /**
     * permis amb el que es contruyeix la clase. La resta es compara amb aquest.
     *
     * @var integer
     */
    protected int $iaccion;

    /* METODES ----------------------------------------------------------------- */

    public function setAccion(int $iaccion): void
    {
        $this->iaccion = $iaccion;
    }

    /**
     * @return array<string, int>
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param array<string, int> $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * diu si té el permís $p (integer).
     *
     *  Ara per els menus va bé.
     * @return boolean
     */
    public function have_perm_bit(int $pagebits): bool
    {
        $userbits = $this->iaccion;
        /*
                echo "user: $userbits<br>";
                echo "menu: $pagebits<br>";
        */
        /* per els menus va bé. Per les activitats estava posat:
            $has_all = (($userbits & $pagebits) === $pagebits);
         ??
        */
        //$has_all = (($userbits & $pagebits) === $userbits);
        $has_one = (($userbits & $pagebits) != 0);
        /*
        var_dump($has_one); echo"<br>KKK<br>";
        echo "AND =page : ".var_dump((($userbits & $pagebits) === $pagebits)) ."<br>";
        echo "AND : ".var_dump((($userbits & $pagebits) === $userbits)) ."<br>";
        echo "OR = page : ".var_dump((($userbits | $pagebits) === $pagebits)) ."<br>";
        echo "OR : ".var_dump((($userbits | $pagebits) === $userbits)) ."<br>";
        */
        return $has_one;
    }

    /**
     * diu si té el permís $p (en texte i ha d'estar en l'array permissions).
     *
     *  Ara per els menus va bé.
     * @param string $p nom del permís: ocupado|ver|modificar|crear|borrar
     * @return boolean
     */
    public function have_perm_activ(string $p): bool
    {
        $pageperm = preg_split('/,/', $p) ?: [];
        [$ok0, $pagebits] = $this->permsum($pageperm);
        $userbits = $this->iaccion;
        $has_all = (($userbits & $pagebits) === $pagebits);
        if (!($has_all && $ok0)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * diu si té el permís $p (en texte i ha d'estar en l'array permissions).
     *
     * @param string $p el nom de la oficina
     * @return boolean
     */
    public function have_perm_oficina(string $p): bool
    {
        $pageperm = preg_split('/,/', $p) ?: [];
        [$ok0, $pagebits] = $this->permsum($pageperm);
        $userbits = $this->iaccion;

        $has_one = (($userbits & $pagebits) != 0);

        if (!($has_one && $ok0)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * diu si té el permís $p (en texte i ha d'estar en l'array permissions).
     *
     * @param string $p nom del permís: ocupado|ver|modificar|crear|borrar
     * @return boolean
     */
    public function have_perm_action(string $p): bool
    {
        $pageperm = preg_split('/,/', $p) ?: [];
        [$ok0, $pagebits] = $this->permsum($pageperm);
        $userbits = $this->iaccion;

        $has_all = (($userbits & $pagebits) === $pagebits);

        if (!($has_all && $ok0)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * diu si té el permís $p (en texte i ha d'estar en l'array permissions).
     *
     * @param string $p nom del permís: ocupado|ver|modificar|crear|borrar
     * @return boolean
     */
    public function only_perm(string $p): bool
    {
        $pageperm = preg_split('/,/', $p) ?: [];

        [$ok0, $pagebits] = $this->permsum($pageperm);
        $userbits = $this->iaccion;

        if ($userbits === $pagebits) {
            return true;
        } else {
            return false;
        }
    }

    /* MÉTODOS PRIVADOS --------------------------------------------------------- */
    /*
   * Genera el número suma de permiso. Se le pasa un array de texto con los permisos
   *
   *
   */
    /**
     * @param list<int> $p
     * @return array{0: bool, 1: int}
     */
    public function permxor(array $p): array
    {
        $r = 0;
        foreach ($p as $key => $val) {
            echo "val: $val :: $key<br>";
            $r ^= $val;
        }

        return [true, $r];
    }

    /*
   * Genera el número suma de permiso. Se le pasa un array de integer con los permisos
   *
   *
   */
    /**
     * @param list<int> $p
     * @return array{0: bool, 1: int}
     */
    public function permsum_bit(array $p): array
    {
        $r = 0;
        foreach ($p as $val) {
            $r |= $val;
        }

        return [true, $r];
    }

    /*
   * Genera el número suma de permiso. Se le pasa un array de texto con los permisos
   *
   *
   */
    /**
     * @param list<string> $p
     * @return array{0: bool, 1: int}
     */
    public function permsum(array $p): array
    {
        $perms = $this->permissions;
        $r = 0;
        foreach ($p as $val) {
            if (!isset($perms[$val])) {
                continue;
            }
            $r |= $perms[$val];
        }

        return [true, $r];
    }

    ## Look for a match within an list of strints

    public function perm_islisted(string $perms, string $look_for): bool
    {
        $permlist = explode(',', $perms);
        foreach ($permlist as $b) {
            if ($look_for === $b) {
                return true;
            }
        }

        return false;
    }

    ## Return a complete <select> tag for permission
    ## selection.

    public function perm_sel(string $name, string $current = '', string $class = ''): string
    {
        reset($this->permissions);
        $ret = sprintf("<select multiple name=\"%s[]\"%s>\n",
            $name,
            ($class != "") ? " class=$class" : "");
        foreach ($this->permissions as $k => $v) {
            $ret .= sprintf(" <option%s%s>%s\n",
                $this->perm_islisted($current, $k) ? " selected" : "",
                ($class != "") ? " class=$class" : "",
                $k);
        }
        $ret .= "</select>";

        return $ret;
    }

    /**
     * dibuja una lista de radios
     *
     */
    public function cuadros_radio(string $nomcamp, int $bin): string
    {
        $camp = $nomcamp . "[]";
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = "";
        foreach ($this->permissions as $nom => $num) {
            if ($bin == $num) {
                $chk = "checked";
            } else {
                $chk = "";
            }
            $txt .= "   <input type=\"radio\" id=\"$camp\" name=\"$camp\" value=\"$num\" $chk>$nom";
        }
        return $txt;
    }

    /**
     * dibuja una lista de checkbox
     * Con el nombre exacto. No si contiene.
     *
     */
    /**
     * @param list<int> $a_perm
     */
    public function cuadros_check_menu(string $nomcamp, array $a_perm): string
    {
        $camp = $nomcamp . "[]";
        $txt = "";
        foreach ($this->permissions as $nom => $num) {
            if (in_array($num, $a_perm)) {
                $chk = "checked";
            } else {
                $chk = "";
            }
            echo "$nom<br>";
            $txt .= "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"$nom\" $chk>$nom";
        }
        return $txt;
    }

    /**
     * dibuja una lista de check solo lectura
     *
     */
    public function cuadros_check_read(int $bin): string
    {
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = "";
        foreach ($this->permissions as $nom => $num) {
            if ($bin & $num) {
                $chk = "checkbox-checked.png";
            } else {
                $chk = "check-box-outline-blank.png";
            }
            $txt .= "   <img src='" . ConfigGlobal::getWeb_icons() . "/" . $chk . "' width=10 height=10 border=0>$nom";
        }
        return $txt;
    }

    /**
     * dibuja una lista de checkbox
     *
     */
    public function cuadros_check(string $nomcamp, int $bin): string
    {
        $camp = $nomcamp . "[]";
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        //$bin &= (-1);
        $txt = "";
        // un bucle para comprobar que no exista uno idéntico
        foreach ($this->permissions as $nom => $num) {
            if ($bin === $num) {
                $admin = $num;
            }
        }
        foreach ($this->permissions as $nom => $num) {
            if (!empty($admin)) {
                if ($admin == $num) {
                    $chk = "checked";
                } else {
                    $chk = "";
                }
            } else {
                if ($bin & $num) {
                    $chk = "checked";
                } else {
                    $chk = "";
                }
            }
            $txt .= "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"$num\" $chk>$nom";
        }
        return $txt;
    }

    public function lista_txt2(int $bin): string
    {
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = "";
        $i = 0;
        foreach ($this->permissions as $nom => $num) {
            //if ($bin & $num) {
            if (($bin & $num) === $num) {
                $i++;
                if ($i > 1) $txt .= ', ';
                $txt .= "$nom";
            }
        }
        return $txt;
    }

    public function lista_txt(int $bin): string
    {
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = "";
        $i = 0;
        foreach ($this->permissions as $nom => $num) {
            //if ($bin & $num) {
            if ($bin === $num) {
                $i++;
                if ($i > 1) $txt .= ', ';
                $txt .= "$nom";
            }
        }
        return $txt;
    }

    public function lista_tiene_txt(int $bin): string
    {
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = "";
        $i = 0;
        foreach ($this->permissions as $nom => $num) {
            if ($bin & $num) {
                $i++;
                if ($i > 1) $txt .= ', ';
                $txt .= "$nom";
            }
        }
        return $txt;
    }

    /**
     * @return array<int, string>
     */
    public function lista_array(int $bin = 0): array
    {
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = [];
        $i = 0;
        foreach ($this->permissions as $nom => $num) {
            $i++;
            $txt[$num] = "$nom";
        }
        return $txt;
    }
}