jsForm = new Object();
jsForm.form = "";
jsForm.enviar = function(){
	if (this.SoloUno) {
		rta=fnjs_solo_uno(this.form);
	} else {
		rta=1;
	}
	if (rta==1) {
		if (this.action) {
			$(this.form).attr('action',this.action);
		}
  		fnjs_enviar_formulario(this.form);
  	}
}

jsForm.mandar=function(formulario,que){
	this.form = formulario;
	this.SoloUno = true;
	switch(que) {
		case "estado":
			this.action = "apps/actividades/controller/actividad_estado.php";
			break;
		case "datos":
			this.action = "apps/actividades/controller/actividad_ver.php";
			break;
		case "importar":
			$('#mod').val(que);
			this.action = "apps/actividades/controller/actividad_update.php";
			break;
		case "lista_clase":
			this.action = "apps/actividades/controller/lista_clases_ca.php";
			break;
		case "plan_estudios":
			this.action = "apps/actividades/controller/plan_estudios_ca.php";
			break;
		case "asig":
			$('#queSel').val(que);
			this.action = "apps/dossiers/controller/dossiers_ver.php";
			break;
		case "carg":
			$('#queSel').val(que);
			$('#id_dossier').val(3102);
			this.action = "apps/dossiers/controller/dossiers_ver.php";
			break;
		case "asis":
			$('#queSel').val(que);
			$('#id_dossier').val(3101);
			this.action = "apps/dossiers/controller/dossiers_ver.php";
			break;
		case "list":
			$('#queSel').val(que);
			this.action = "apps/actividades/controller/lista_asistentes.php";
			break;
		case "listcl":
			$('#queSel').val(que);
			this.action = "apps/actividades/controller/lista_asistentes.php";
			break;
		case "duplicar":
			$('#mod').val(que);
			this.action = "apps/actividades/controller/actividad_update.php";
			break;
		case "historicos":
			$('#queSel').val(que);
			$('#id_dossier').val(1301);
			this.action = "apps/actividades/controller/dossiers/historics.php";
			break;
	}
	this.enviar();
}

