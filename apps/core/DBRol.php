<?php

namespace core;
class DBRol
{
    /**
     * oDbl de Grupo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * Password de DBRol
     *
     * @var string
     */
    protected $sPwd;
    /**
     * Usuario a Crear de DBRol
     *
     * @var string
     */
    protected $sUser;
    /**
     * Opciones para a Crear el Role de DBRol
     *
     * @var string
     */
    protected $sOptions;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     */
    function __construct()
    {
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setDbConexion($oDbl)
    {
        $this->setoDbl($oDbl);
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    protected function setoDbl($oDbl)
    {
        $this->oDbl = $oDbl;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    protected function getoDbl()
    {
        return $this->oDbl;
    }

    public function setUser($user)
    {
        $this->sUser = $user;
    }

    public function setPwd($password)
    {
        //$password_encoded = urlencode ($password);
        $this->sPwd = $password;
    }

    public function setOptions($options)
    {
        $this->sOptions = $options;
    }


    // usuarios:
    public function addGrupo($grupo)
    {
        $oDbl = $this->getoDbl();
        $sql = "GRANT \"$grupo\" TO \"$this->sUser\"";
        //$sql = "GRANT \"$grupo\" TO \"$this->sUser\" ";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.addGrupo.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.addGrupo.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    public function delGrupo($grupo)
    {
        $oDbl = $this->getoDbl();
        $sql = "REVOKE \"$grupo\" FROM \"$this->sUser\"";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.delGrupo.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.delGrupo.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    public function crearSchema()
    {
        $oDbl = $this->getoDbl();
        $sql = "CREATE SCHEMA IF NOT EXISTS \"$this->sUser\" AUTHORIZATION \"$this->sUser\";";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.crearSchema.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.crearSchema.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    public function renombrarSchema($esquema_old)
    {
        $oDbl = $this->getoDbl();
        $sql = "ALTER SCHEMA \"$esquema_old\" RENAME TO \"$this->sUser\";";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.crearSchema.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.crearSchema.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    public function crearUsuario()
    {
        $oDbl = $this->getoDbl();
        // comprobar antes si existe.
        $sql = "SELECT 1 FROM pg_roles WHERE rolname=\"$this->sUser\"";

        if (($oDbl->query($sql)) === false) {
            $this->sOptions = empty($this->sOptions) ? 'NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN' : $this->sOptions;
            $sql = "CREATE ROLE \"$this->sUser\" PASSWORD '$this->sPwd' $this->sOptions;";

            if (($oDblSt = $oDbl->prepare($sql)) === false) {
                $sClauError = 'DBRol.crear.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            try {
                $oDblSt->execute();
            } catch (\PDOException $e) {
                $sClauError = 'DBRol.crear.execute';
                $sClauError .= ' ' . $e->errorInfo[2];
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        } else {
            $this->cambiarPassword();
        }
    }

    public function renombrarUsuario($usuario_old)
    {
        $oDbl = $this->getoDbl();
        $this->sOptions = empty($this->sOptions) ? 'NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN' : $this->sOptions;

        $sql = "ALTER ROLE \"$usuario_old\" RENAME TO \"$this->sUser\" ";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.crear.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            try {
                $oDblSt->execute();
            } catch (\PDOException $e) {
                $sClauError = 'DBRol.crear.execute';
                $sClauError .= ' ' . $e->errorInfo[2];

                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
            /* Because MD5-encrypted passwords use the role name as cryptographic salt, 
             * renaming a role clears its password if the password is MD5-encrypted.
             */
            $this->cambiarPassword();

            $sql = "ALTER ROLE \"$this->sUser\" SET search_path TO '$this->sUser', 'public'; ";

            if (($oDblSt = $oDbl->prepare($sql)) === false) {
                $sClauError = 'DBRol.crear.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute();
                } catch (\PDOException $e) {
                    $sClauError = 'DBRol.crear.execute';
                    $sClauError .= ' ' . $e->errorInfo[2];

                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
    }

    public function eliminarSchema()
    {
        $oDbl = $this->getoDbl();
        $sql = "DROP SCHEMA IF EXISTS \"$this->sUser\" CASCADE";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.eliminarSchema.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.eliminarSchema.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    public function eliminarUsuario()
    {
        $oDbl = $this->getoDbl();

        $sql = "DROP ROLE IF EXISTS \"$this->sUser\";";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.eliminar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.eliminar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    private function cambiarPassword()
    {
        $oDbl = $this->getoDbl();

        $sql = "ALTER USER \"$this->sUser\" WITH PASSWORD '$this->sPwd';";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBRol.pwd.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBRol.pwd.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }
}
