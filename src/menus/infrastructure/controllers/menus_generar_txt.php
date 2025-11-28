<?php
/**
 * Esta página genera un fichero con todos los textos de los menús que hay en la base de datos,
 * para poder traducirlos por gettex
 *
 *
 * @package    delegacion
 * @subpackage    fichas
 * @author    Dani Serrabou
 * @since        15/5/02.
 *
 */

use core\ServerConf;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;

$MenusRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);
$cMenus = $MenusRepository->getMenuDbs(['ok' => 't', '_ordre' => 'id_grupmenu,orden']);

$texto = "<?php\n //Menus interiores\n";

foreach ($cMenus as $oMenuDb) {
    $menu = $oMenuDb->getMenu();
    if (!empty($menu)) {
        $texto .= " _(\"$menu\"); \n";
    }
}

// Añadir los tipos de repetición
$RepeticionRepository = $GLOBALS['container']->get(RepeticionRepositoryInterface::class);
$cRepeticion = $RepeticionRepository->getRepeticiones();

$texto .= "//tipos de repetición actividades\n";
foreach ($cRepeticion as $oRepeticion) {
    $repe = $oRepeticion->getRepeticion();
    if (!empty($repe)) {
        $texto .= " _(\"$repe\"); \n";
    }
}

//echo $texto;
//$dir_base = "/var/www/orbix";
$dir_base = ServerConf::DIR;
$filename = "$dir_base/frontend/menus/view/traducir_menu.phtml";
$somecontent = $texto;

// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {

    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence 
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'w+')) {
        print "No puedo abrir el fichero ($filename)";
        exit;
    }

    // Write $somecontent to our opened file.
    if (!fwrite($handle, $somecontent)) {
        print "Cannot write to file ($filename)";
        exit;
    }
    fclose($handle);

} else {
    print "The file $filename is not writable";
}