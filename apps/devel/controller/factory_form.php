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


?>
<form name="frm" action="apps/devel/controller/factory.php">
Base de dades: <input type="text" name="db" value="test-dl_interior"><br>
Nom de la taula: <input type="text" name="tabla" value='H-dlb.'><br>
Nom de la Clase: <input type="text" name="clase" value=""><br>
Plural de Nom de la Clase: <input type="text" name="clase_plural" value=""><br>
app: <input type="text" name="grupo" value="personas"> (actividades,personas,ubis)<br>
Nom de la Aplicacion: <input type="text" name="aplicacion" value="delegación"> (registro,...)<br>
<br>
<input TYPE="button" VALUE="<?php echo ucfirst(_("guardar cambios")); ?>"  onclick="fnjs_enviar_formulario(this.form)">

</form>
