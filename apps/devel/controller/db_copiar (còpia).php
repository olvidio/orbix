<?php
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************




$region = empty($_POST['region'])? '' : $_POST['region'];
$dl = empty($_POST['dl'])? '' : $_POST['dl'];

$esquema = "$region-$dl";
$esquemav = $esquema.'v';
$esquemaf = $esquema.'f';

echo "Esquema: $esquema<br>";


// COMUN
$str_conexio = "pgsql:host=localhost port=5432  dbname='comun' user='dani' password='system'";
$oDbl = new \PDO($str_conexio);
//-------------- Actividades ----------------------
$sql = "INSERT INTO \"$esquema\".a_actividades_dl SELECT * FROM resto.a_actividades_ex WHERE dl_org = '$dl'";
if (($qRs = $oDbl->prepare($sql)) === false) {
	$sClauError = 'DBCopiar.actividades.prepare';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	return false;
} else {
	if ($qRs->execute() === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	} else {
		$sql = "DELETE FROM resto.a_actividades_ex WHERE dl_org = '$dl'";
		if ($oDbl->query($sql) === false) {
			$sClauError = 'DBCopiar.actividades.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
	}
}
//---------------- CDC --------------------
$sql = "INSERT INTO \"$esquema\".u_cdc_dl SELECT * FROM resto.u_cdc_ex WHERE dl = '$dl' AND region='$region'; ";
if ($oDbl->query($sql) === false) {
	$sClauError = 'DBCopiar.actividades.execute';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	return false;
} else {
	// actualizar el tipo_ubi.
	$sql = "UPDATE \"$esquema\".u_cdc_dl SET tipo_ubi='cdcdl'";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
	$sql = "INSERT INTO \"$esquema\".u_dir_cdc_dl SELECT  DISTINCT rd.* 
		FROM  resto.u_dir_cdc_ex rd JOIN resto.u_cross_cdc_ex_dir rx USING (id_direccion), \"$esquema\".u_cdc_dl u 
		WHERE u.id_ubi = rx.id_ubi";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	$sql = "INSERT INTO \"$esquema\".u_cross_cdc_dl_dir SELECT r.* FROM  resto.u_cross_cdc_ex_dir r JOIN \"$esquema\".u_cdc_dl a USING (id_ubi)";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// delete cdc
	$sql = "DELETE FROM resto.u_cdc_ex WHERE dl = '$dl' AND region='$region'";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// delete dir
	$sql = "DELETE FROM resto.u_dir_cdc_ex
   			WHERE id_direccion IN (SELECT id_direccion FROM \"$esquema\".u_dir_cdc_dl)"; 
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// delete cross (deberia borrarse sólo; por el foreign key).
}
//---------------- Teleco CDC --------------------
$sql = "INSERT INTO \"$esquema\".d_teleco_cdc_dl SELECT r.* FROM  resto.d_teleco_cdc_ex r JOIN \"$esquema\".u_cdc_dl a USING (id_ubi)";
if ($oDbl->query($sql) === false) {
	$sClauError = 'DBCopiar.actividades.execute';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	return false;
} else {
	$sql = "DELETE FROM resto.d_teleco_cdc_ex  WHERE id_ubi IN (SELECT id_ubi FROM \"$esquema\".u_cdc_dl)";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
}

// SV

$aTablas = array("'aux*'","web_preferencias");


$esquemaRef = 'H-dlbv';
$oDBTabla = new core\DBTabla();
$oDBTabla->setDb('sv');
$oDBTabla->setRef($esquemaRef);
$oDBTabla->setNew($esquemav);
$oDBTabla->setTablas($aTablas);
$oDBTabla->copiar();


$str_conexio = "pgsql:host=localhost port=5432  dbname='sv' user='dani' password='system'";
$oDbl = new \PDO($str_conexio);

//---------------- Ctr --------------------
$sql = "INSERT INTO \"$esquemav\".u_centros_dl SELECT * FROM restov.u_centros_ex WHERE dl = '$dl' AND region='$region'; ";
   if ($oDbl->query($sql) === false) {
	$sClauError = 'DBCopiar.actividades.execute';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	return false;
} else {
	// actualizar el tipo_ubi.
	$sql = "UPDATE \"$esquemav\".u_centros_dl SET tipo_ubi='ctrdl'";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
	$sql = "INSERT INTO \"$esquemav\".u_dir_ctr_dl SELECT DISTINCT rd.* 
		FROM  restov.u_dir_ctr_ex rd JOIN restov.u_cross_ctr_ex_dir rx USING (id_direccion), \"$esquemav\".u_centros_dl u 
		WHERE u.id_ubi = rx.id_ubi";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	$sql = "INSERT INTO \"$esquemav\".u_cross_ctr_dl_dir SELECT r.* FROM  restov.u_cross_ctr_ex_dir r JOIN \"$esquemav\".u_centros_dl a USING (id_ubi)";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// delete ctr
	$sql = "DELETE FROM restov.u_centros_ex WHERE dl = '$dl' AND region='$region'";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// delete dir
	$sql = "DELETE FROM restov.u_dir_ctr_ex
   			WHERE id_direccion IN (SELECT id_direccion FROM \"$esquemav\".u_dir_ctr_dl)"; 
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
	// delete cross (deberia borrarse sólo; por el foreign key).
}
//---------------- Teleco Ctr --------------------
$sql = "INSERT INTO \"$esquemav\".d_teleco_ctr_dl SELECT r.* FROM  restov.d_teleco_ctr_ex r JOIN \"$esquemav\".u_centros_dl a USING (id_ubi)";
if ($oDbl->query($sql) === false) {
	$sClauError = 'DBCopiar.actividades.execute';
	$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	return false;
} else {
	$sql = "DELETE FROM restov.d_teleco_ctr_ex  WHERE id_ubi IN (SELECT id_ubi FROM \"$esquemav\".u_centros_dl)";
	if ($oDbl->query($sql) === false) {
		$sClauError = 'DBCopiar.actividades.execute';
		$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
		return false;
	}
}

// SF





?>
