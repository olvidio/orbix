<?php
/**
* Esta página genera un fichero con todos los textos de los menús que hay en la base de datos, para poder traducirlos por gettex
*
*
*@package	delegacion
*@subpackage	fichas
*@author	Dani Serrabou
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oGesMenus = new menus\model\entity\GestorMenuDb();
$cMenus = $oGesMenus->getMenuDbs(['ok' => 't', '_ordre' => 'id_grupmenu,orden']);


$texto = "<?php\n //Menus interiores\n";
foreach ($cMenus as $oMenuDb) {
	$menu = $oMenuDb->getMenu();
	if (!empty($menu))	{
		$texto.=" _(\"$menu\"); \n";
	}
}

//echo $texto;
//$dir_base = "/var/www/orbix";
$dir_base = core\ConfigGlobal::DIR;
$filename = "$dir_base/apps/menus/view/traducir_menu.phtml";
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
    
    //print "Success, wrote ($somecontent) to file ($filename)";
    
    fclose($handle);
					
} else {
    print "The file $filename is not writable";
}