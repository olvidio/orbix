<?php
use core\ConfigGlobal;
use menus\model\entity as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/*
 * Para que no de errror al ejecutar psql. usuario root no coincide con dani
 * En el fichero /etc/postgresql/9.5/main/pg_hba.conf, hacia la line 90:
 * Hay que poner 'trust'
 * 
 * # "local" is for Unix domain socket connections only
*     local   all             all                                     trust
 * 
 */

// Copiar de dlb a public roles-grupmenu, grupmenu, menus

$oConfig = new core\Config('importar'); //de la database comun 
$config = $oConfig->getEsquema('public'); //de la database comun 

$oConexion = new core\dbConnection($config);
$oDevelPC = $oConexion->getPDO();

$Qaccion = (string) \filter_input(INPUT_POST, 'accion');

//$dir_base = "/var/www/orbix";
$dir_base = core\ConfigGlobal::DIR;
$filename_base = "$dir_base/log/menus/tot_menus_base.sql";
$filename = "$dir_base/log/menus/tot_menus.sql";
$filelog = "$dir_base/log/menus/menus.log";

if ($Qaccion == 'importar') {
	/* IMPORTANTE
	   En el fichero /etc/sudoers  (editar con visudo) debe estar la linea:

		# Para importar los menus a la base de datos
		www-data ALL=NOPASSWD: /usr/bin/psql

		para permitir a ww-data ejecutar psql
	*/

	// Cambiar el directorio local al de la instalacion
	$txt_base = file_get_contents($filename_base);
	$txt_comun = str_replace ( "DIRBASE", $dir_base, $txt_base);
	file_put_contents($filename, $txt_comun);
	
    $host = $config['host'];
    //$sslmode = $config['sslmode'];
    $port = $config['port'];
    $dbname = $config['dbname'];
    $user = $config['user'];
    $password = $config['password'];
    
    $password_encoded = rawurlencode ($password);
    $dsn = "postgresql://$user:$password_encoded@$host:$port/".$dbname;
    
    $command = "/usr/bin/psql -q ";
    $command .= "--pset pager=off ";
    $command .= "--file=".$filename." ";
    $command .= "\"".$dsn."\"";
    $command .= " > ".$filelog." 2>&1";
    passthru($command); // no output to capture so no need to store it
    // read the file, if empty all's well
    $error = file_get_contents($filelog);
    if(trim($error) != '') {
        if (ConfigGlobal::is_debug_mode()) {
            echo sprintf("PSQL ERROR IN COMMAND: %s<br> mirar en: %s<br>",$command,$filelog);
        }
    }
}


if ($Qaccion == 'exportar') {
	// PASSAR A FICHEROS

	/* IMPORTANTE
	   Para tener permisos de escritura el directorio menus debe tener a+w
	 * 
	 */
	
	//************ METAMENUS **************
	$file_metamenus = "$dir_base/log/menus/comun.sql";
	$name_metamenus = "DIRBASE/log/menus/comun.sql";
	$txt_comun = '';

	$txt_comun .= 'TRUNCATE TABLE "public".aux_metamenus RESTART IDENTITY;'."\n";
	$oDevelPC->exec('COPY "public".aux_metamenus TO \''.$file_metamenus.'\' ');
	$txt_comun .= 'COPY "public".aux_metamenus FROM \''.$name_metamenus.'\''.";\n";

	//************ GRUPMENU **************
	$file_refgrupmenu = "$dir_base/log/menus/refgrupmenu.sql";
	$name_refgrupmenu = "DIRBASE/log/menus/refgrupmenu.sql";
	$txt_comun .= 'TRUNCATE TABLE "public".ref_grupmenu RESTART IDENTITY;'."\n";
	$oDevelPC->exec('COPY "public".ref_grupmenu TO \''.$file_refgrupmenu.'\' ');
	$txt_comun .= 'COPY "public".ref_grupmenu FROM \''.$name_refgrupmenu.'\''.";\n";

	//************ GRUPMENU_ROL **************
	$file_refgrupmenu_rol = "$dir_base/log/menus/refgrupmenu_rol.sql";
	$name_refgrupmenu_rol = "DIRBASE/log/menus/refgrupmenu_rol.sql";
	$txt_comun .= 'TRUNCATE TABLE "public".ref_grupmenu_rol RESTART IDENTITY;'."\n";
	$oDevelPC->exec('COPY "public".ref_grupmenu_rol TO \''.$file_refgrupmenu_rol.'\' ');
	$txt_comun .= 'COPY "public".ref_grupmenu_rol FROM \''.$name_refgrupmenu_rol.'\''.";\n";

	//************ MENUS **************
	$file_refmenus = "$dir_base/log/menus/refmenus.sql";
	$name_refmenus = "DIRBASE/log/menus/refmenus.sql";
	$txt_comun .= 'TRUNCATE TABLE "public".ref_menus RESTART IDENTITY;'."\n";
	$oDevelPC->exec('COPY "public".ref_menus TO \''.$file_refmenus.'\' ');
	$txt_comun .= 'COPY "public".ref_menus FROM \''.$name_refmenus.'\''.";\n";

	file_put_contents($filename_base, $txt_comun);
} 