<?php
namespace core;

use web;

/**
 * Clase para manejar los errores
 *
 * @package delegación
 * @subpackage model
 * @author
 * @version 1.0
 * @created 21/9/2010
 */
class GestorErrores
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aDades de Actividad
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    private $filename;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private string|int|null $stack;

    /**
     * Constructor de la classe.
     *
     */
    function __construct()
    {
        $this->filename = ConfigGlobal::$directorio . '/log/errores.log';
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    private function limitar($n = 10)
    {
        // Cuando hay el doble, borro $n.
        if (isset($_SESSION['errores'])) { // No sé poruqe no deja poner todo junto
            if (is_array($_SESSION['errores']) & (count($_SESSION['errores']) > 2 * $n)) {
                $eee = 'a borrra!!';
                array_splice($_SESSION['errores'], -$n); // negativo empieza por el final.
                // hay que cambiar el indice stack
                end($_SESSION['errores']);
                $stack = key($_SESSION['errores']);
                $this->stack = $stack;
                //con los stack dentro de parammmmm
            }
        }
    }

    public function recordar($error)
    {
        // evitar que sea muy grande
        $this->limitar(10);
        if (isset($_SESSION['errores']) && is_array($_SESSION['errores'])) { //para la primera
            end($_SESSION['errores']);
        }
        $_SESSION['errores'][] = $error;
    }

    function leerErrorAppLastError(&$oDBSt, $sClauError, $line, $file)
    {
        $user = ConfigGlobal::mi_usuario();
        $ahora = date("Y/m/d H:i:s");
        $err = $oDBSt->errorInfo();
        $txt = "\n" . $ahora . " - " . $user . "->>  " . $err[2] . "\n $sClauError en linea $line de: $file\n";

        $trimmed = file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $linea2 = array_pop($trimmed);
        $linea1 = array_pop($trimmed);
        return $linea1 . "\n" . $linea2;
    }

    /**
     * Añade un error al fichero
     *
     * @param string $oDBSt Puede ser objeto PDO o PDOStatement
     * @param string $sClauError Un texto cualquiera para poner en el error
     * @param string $line
     * @param string $file
     */
    function addErrorAppLastError(&$oDBSt, $sClauError, $line, $file)
    {
        // Cuando ejecuto algun controlador desde la linea de comandos, no existe la ip:
        $ip = empty($_SERVER['REMOTE_ADDR']) ? 'localhost' : $_SERVER['REMOTE_ADDR'];
        $user = ConfigGlobal::mi_usuario();
        $esquema = ConfigGlobal::mi_region_dl();
        $ahora = date("Y/m/d H:i:s");
        // En algunos momentos interesa la info del servidor, pero debe ser con
        // la conexión  PDO, no con el Statement:
        //		$server = $oDB->getAttribute(constant("\PDO::ATTR_SERVER_INFO"));
        //		$txt = "\n# ".$ahora." - ".$user."[$esquema]$ip  ($server)";
        $err = $oDBSt->errorInfo();
        $id_user = $user . "[$esquema]$ip ";
        $txt = "\n# " . $ahora . " - " . $id_user;
        $txt .= "\n\t->>  " . $err[2] . "\n $sClauError en linea $line de: $file\n";

        // También lo guardo en una variable de la session para poder acceder a
        // el desde el controlador correspondiente.
        $this->recordar($err[2]);

        $filename = $this->filename;
        if (!$handle = fopen($filename, 'ab')) {
            echo "Cannot open file ($filename)";
            die();
        }
        // Write $somecontent to our opened file.
        if (fwrite($handle, $txt) === FALSE) {
            echo "Cannot write to file ($filename)";
            die();
        }
        fclose($handle);
    }

    /*
    function addErrorSec($err = '', $sClauError, $line, $file)
    {
        $filename = $this->filename;
        $this->filename = ConfigGlobal::$directorio . '/log/security.log';
        $this->addError($err, $sClauError, $line, $file);
        $this->filename = $filename;
    }
*/

    /*
    function addError($err = '', $sClauError, $line, $file)
    {
        // Cuando ejecuto algún controlador desde la linea de comandos, no existe la ip:
        $ip = empty($_SERVER['REMOTE_ADDR']) ? 'localhost' : $_SERVER['REMOTE_ADDR'];
        $user = ConfigGlobal::mi_usuario();
        $esquema = ConfigGlobal::mi_region_dl();
        $ahora = date("Y/m/d H:i:s");
        $id_user = $user . "[$esquema]$ip ";
        $txt = "\n" . $ahora . " - " . $id_user . "->>  " . $err . "\n $sClauError en linea $line de: $file\n";

        $filename = $this->filename;
        if (!$handle = fopen($filename, 'a')) {
            echo "Cannot open file ($filename)";
            die();
        }
        // Write $somecontent to our opened file.
        if (fwrite($handle, $txt) === FALSE) {
            echo "Cannot write to file ($filename)";
            die();
        }
        fclose($handle);
    }
    */
}
