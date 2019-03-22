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

jsForm.refresh=function(){
	if (this.SoloUno) {
		rta=fnjs_solo_uno(this.form);
	} else {
		rta=1;
	}
	if (rta==1) {
		var param=$(this.form).serialize();
		var url=this.action;
		$(this.form).submit(function() {
			$.ajax({
				url: url,
				type: 'post',
				data: param
			})
			.done(function (rta_txt) {
				if (rta_txt != '' && rta_txt != '\n') {
					alert (rta_txt);
				} else {
					jsForm.actualizar();
				}
			});
			return false;
		});
		$(this.form).submit();
		$(this.form).off();
	}
}
jsForm.actualizar=function(){
	var continuar = '<input type="hidden" name="continuar" value="si">';
	$(this.form).attr('action',"apps/actividades/controller/actividad_select.php");
	$(this.form).append(continuar);
	fnjs_enviar_formulario(this.form,'#main');
}

jsForm.update=function(formulario,que){
	this.form = formulario;
	this.SoloUno = true;
	switch(que) {
		case "publicar":
			this.SoloUno = false;
			$('#mod').val(que);
			this.action = "apps/actividades/controller/actividad_update.php";
			break;
		case "importar":
			this.SoloUno = false;
			$('#mod').val(que);
			this.action = "apps/actividades/controller/actividad_update.php";
			break;
		case "duplicar":
			$('#mod').val(que);
			this.action = "apps/actividades/controller/actividad_update.php";
			break;
	}
	this.refresh();
}

jsForm.mandar=function(formulario,que){
	this.form = formulario;
	this.SoloUno = true;
	switch(que) {
		case "proceso":
			this.action = "apps/actividades/controller/actividad_proceso.php";
			break;
		case "datos":
			this.action = "apps/actividades/controller/actividad_ver.php";
			break;
		case "lista_clase":
			this.action = "apps/actividadestudios/controller/lista_clases_ca.php";
			break;
		case "plan_estudios":
			this.action = "apps/actividadestudios/controller/plan_estudios_ca.php";
			break;
		case "dossiers":
			//$('#queSel').val(que);
			this.action = "apps/dossiers/controller/dossiers_ver.php";
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
		case "plazas":
			$('#queSel').val(que);
			this.action = "apps/actividadplazas/controller/resumen_plazas.php";
			break;
		case "list":
			$('#queSel').val(que);
			this.action = "apps/asistentes/controller/lista_asistentes.php";
			break;
		case "listcl":
			$('#queSel').val(que);
			this.action = "apps/asistentes/controller/lista_asistentes.php";
			break;
		case "historicos":
			$('#queSel').val(que);
			$('#id_dossier').val(1301);
			this.action = "apps/actividades/controller/dossiers/historics.php";
			break;
	}
	this.enviar();
}

