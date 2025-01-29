<?php

namespace permisos\model;

use core\ConfigGlobal;

abstract class XPermisos
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * permisions. array amb els diferents tipos de permisos i el seu numero.
     *
     * @var array
     */
    protected $permissions;
    /**
     * permis amb el que es contruyeix la clase. La resta es compara amb aquest.
     *
     * @var integer
     */
    protected int $iaccion;

    /* METODES ----------------------------------------------------------------- */

    function setAccion(int $iaccion)
    {
        $this->iaccion = $iaccion;
    }

    function getPermissions()
    {
        return $this->permissions;
    }

    function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    function getTodos()
    {
        return $this->todos;
    }

    function perm_invalid($does_have, $must_have)
    {
        include("perm_invalid.php");
    }

    /**
     * diu si té el permís $p (integer).
     *
     *  Ara per els menus va bé.
     * @param string $p nom del permís: ocupado|ver|modificar|crear|borrar
     * @return boolean
     */
    public function have_perm_bit($pagebits)
    {
        //$pageperm = preg_split('/,/', $p);
        //list ($ok0, $pagebits) = $this->permsum($pageperm);
        $ok0 = true;
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
        if (!($has_one && $ok0)) {
            return false;
        }

        return true;
    }

    /**
     * diu si té el permís $p (en texte i ha d'estar en l'array permissions).
     *
     *  Ara per els menus va bé.
     * @param string $p nom del permís: ocupado|ver|modificar|crear|borrar
     * @return boolean
     */
    public function have_perm_activ(string $p)
    {
        $pageperm = preg_split('/,/', $p);
        list ($ok0, $pagebits) = $this->permsum($pageperm);
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
    public function have_perm_oficina(string $p)
    {
        $pageperm = preg_split('/,/', $p);
        list ($ok0, $pagebits) = $this->permsum($pageperm);
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
    public function have_perm_action(string $p)
    {
        $pageperm = preg_split('/,/', $p);
        list ($ok0, $pagebits) = $this->permsum($pageperm);
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
    public function only_perm(string $p)
    {
        $pageperm = preg_split('/,/', $p);

        list ($ok0, $pagebits) = $this->permsum($pageperm);
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
    public function permxor($p)
    {
        if (!is_array($p)) {
            return array(false, 0);
        }
        //$perms = $this->permissions;
        $r = 0;
        reset($p);
        foreach ($p as $key => $val) {
            echo "val: $val :: $key<br>";
            $r ^= $val;
        }

        return array(true, $r);
    }

    /*
   * Genera el número suma de permiso. Se le pasa un array de integer con los permisos
   *
   *
   */
    public function permsum_bit($p)
    {
        if (!is_array($p)) {
            return array(false, 0);
        }
        $r = 0;
        foreach ($p as $key => $val) {
            $r |= $val;
        }

        return array(true, $r);
    }

    /*
   * Genera el número suma de permiso. Se le pasa un array de texto con los permisos
   *
   *
   */
    public function permsum($p)
    {
        if (!is_array($p)) {
            return array(false, 0);
        }
        $perms = $this->permissions;
        $r = 0;
        reset($p);
        foreach ($p as $key => $val) {
            if (!isset($perms[$val])) {
                //return array(false, 0);
                continue; // si hi ha un permis que no és dels predefinits m'ho salto.
            }
            $r |= $perms[$val];
        }

        return array(true, $r);
    }

    ## Look for a match within an list of strints

    function perm_islisted($perms, $look_for)
    {
        $permlist = explode(",", $perms);
        foreach ($permlist as $a => $b) {
            if ($look_for === $b) {
                return true;
            }
        }
        return false;
    }

    ## Return a complete <select> tag for permission
    ## selection.

    function perm_sel($name, $current = "", $class = "")
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
    public function cuadros_radio($nomcamp, $bin)
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
    public function cuadros_check_menu($nomcamp, $a_perm)
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
    public function cuadros_check_read($bin)
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
            $txt .= "   <img src='http:" . ConfigGlobal::getWeb_icons() . "/" . $chk . "' width=10 height=10 border=0>$nom";
        }
        return $txt;
    }

    /**
     * dibuja una lista de checkbox
     *
     */
    public function cuadros_check($nomcamp, $bin)
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

    public function lista_txt2($bin)
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

    public function lista_txt($bin)
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

    public function lista_tiene_txt($bin)
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

    public function lista_array($bin = 0)
    {
        //si $bin es nulo, le pongo todo 0
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = array();
        $i = 0;
        foreach ($this->permissions as $nom => $num) {
            $i++;
            $txt[$num] = "$nom";
        }
        return $txt;
    }
}