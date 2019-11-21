<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
	
$QEsquemaRef = (string) \filter_input(INPUT_POST, 'esquema');
$Qregion = (string) \filter_input(INPUT_POST, 'region');
$Qdl = (string) \filter_input(INPUT_POST, 'dl');
$Qcomun = (integer) \filter_input(INPUT_POST, 'comun');
$Qsv = (integer) \filter_input(INPUT_POST, 'sv');
$Qsf = (integer) \filter_input(INPUT_POST, 'sf');

$esquema = "$Qregion-$Qdl";
$esquemav = $esquema.'v';
$esquemaf = $esquema.'f';

// Copiar esquema de...
$a_reg = explode('-',$QEsquemaRef);
$RegionRef = $a_reg[0];
$DlRef = substr($a_reg[1],0,-1); // quito la v o la f.

$esquemaRef = "$RegionRef-$DlRef";
$esquemaRefv = $esquemaRef.'v';
$esquemaReff = $esquemaRef.'f';

// COMUN
if (!empty($Qcomun)) {
    /**
     * lista de tablas de las que hay que copiar los valores.
     * Posteriormente hay que cambiar el id_schema (si tiene)
     * y actualizar la sequencia (se hace al final, en DBTrasvase)
     * 
     * @var array $aTablas
     */
    $aTablas = [
                "a_tipos_actividad" => ['id_schema' => 'yes'],
                "xa_tipo_tarifa" => ['id_schema' => 'yes'],
                "x_config_schema" => ['id_schema' => 'yes'],
                ];
    $oDBTabla = new core\DBTabla();
    $oDBTabla->setDb('comun');
    $oDBTabla->setRef($esquemaRef);
    $oDBTabla->setNew($esquema);
    $oDBTabla->setTablas($aTablas);
    $oDBTabla->copiar_remote();

    $oTrasvase = new core\DBTrasvase();
    $oTrasvase->setDbName('comun');
    $oTrasvase->setDbConexion();
    $oTrasvase->setRegion($Qregion);
    $oTrasvase->setDl($Qdl);

    $oTrasvase->actividades('resto2dl');
    $oTrasvase->cdc('resto2dl');
    $oTrasvase->teleco_cdc('resto2dl');
    // fijar secuencias
    $oTrasvase->fix_seq();
}
// SV
if (!empty($Qsv)) {
    $aTablas = ["aux_cross_usuarios_grupos" => ['id_schema' => 'yes'],
                "aux_grupmenu" => ['id_schema' => 'yes'],
                "aux_grupmenu_rol" => ['id_schema' => 'yes'],
                "aux_grupo_permmenu" => ['id_schema' => 'yes'],
                "aux_grupos_y_usuarios" => ['id_schema' => 'yes'],
                "aux_menus" => ['id_schema' => 'yes'],
                "aux_usuarios" => ['id_schema' => 'yes'],
                "web_preferencias" => ['id_schema' => 'yes'],
                "m0_mods_installed_dl" => ['id_schema' => 'yes'],
                ];
	$oDBTabla = new core\DBTabla();
	$oDBTabla->setDb('sv');
	$oDBTabla->setRef($esquemaRefv);
	$oDBTabla->setNew($esquemav);
	$oDBTabla->setTablas($aTablas);
	$oDBTabla->copiar();

    $oTrasvase = new core\DBTrasvase();
	$oTrasvase->setDbName('sv');
	$oTrasvase->setDbConexion();
	$oTrasvase->setRegion($Qregion);
	$oTrasvase->setDl($Qdl);

	$oTrasvase->ctr('resto2dl');
	$oTrasvase->teleco_ctr('resto2dl');
	// fijar secuencias
	$oTrasvase->fix_seq();
	
}
// SF
if (!empty($Qsf)) {
    $aTablas = ["aux_cross_usuarios_grupos" => ['id_schema' => 'yes'],
                "aux_grupmenu" => ['id_schema' => 'yes'],
                "aux_grupmenu_rol" => ['id_schema' => 'yes'],
                "aux_grupo_permmenu" => ['id_schema' => 'yes'],
                "aux_grupos_y_usuarios" => ['id_schema' => 'yes'],
                "aux_menus" => ['id_schema' => 'yes'],
                "aux_usuarios" => ['id_schema' => 'yes'],
                "web_preferencias" => ['id_schema' => 'yes'],
                "m0_mods_installed_dl" => ['id_schema' => 'yes'],
                ];
	$oDBTabla = new core\DBTabla();
	$oDBTabla->setDb('sf');
	$oDBTabla->setRef($esquemaReff);
	$oDBTabla->setNew($esquemaf);
	$oDBTabla->setTablas($aTablas);
	$oDBTabla->copiar();

    $oTrasvase = new core\DBTrasvase();
	$oTrasvase->setDbName('sf');
	$oTrasvase->setDbConexion();
	$oTrasvase->setRegion($Qregion);
	$oTrasvase->setDl($Qdl);

	$oTrasvase->ctr('resto2dl');
	$oTrasvase->teleco_ctr('resto2dl');
	// fijar secuencias
	$oTrasvase->fix_seq();
	
}

echo "<br>";
echo sprintf(_("esquema: %s. Se han pasado todos los datos que se tenian."),$esquema);