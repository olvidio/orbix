
		fnjs_nuevo=function(formulario){
			$('#mod').val("nuevo");
			$(formulario).attr('action',"frontend/shared/controller/tablaDB_formulario_ver.php");
			fnjs_enviar_formulario(formulario,'#main');
		}
		fnjs_modificar=function(formulario){
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				$('#mod').val("editar");
				$(formulario).attr('action',"frontend/shared/controller/tablaDB_formulario_ver.php");
				fnjs_enviar_formulario(formulario,'#main');
			}
		}
		fnjs_eliminar=function(formulario){
			var err;
			var eliminar;
			eliminar="¿Quitar este cargo?";

			if (!eliminar) eliminar="¿Está seguro que desea eliminar este registro?";
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				if (confirm(eliminar) ) {
					var ctx_eliminar = fnjs_ctx_fila_seleccionada(formulario, 'ctx_eliminar');
					if (!ctx_eliminar) {
						alert("respuesta" + ': ' + "Operaci\u00f3n no autorizada");
						return;
					}
					$('#mod').val("eliminar");
					$(formulario).attr('action',"src/shared/tablaDB_update");
					$(formulario).one("submit", function() {
						$.ajax({
							url: $(this).attr('action'),
							type: 'post',
							data: $(this).serialize() + '&ctx_eliminar=' + encodeURIComponent(ctx_eliminar),
							dataType: 'json'
							})
                        .done(function (json) {
                            if (json.success !== true) {
                                alert("respuesta" + ': ' + json.mensaje);
                            } else {
                                fnjs_actualizar(formulario);
                            }
                        });
						return false;
					});
					$(formulario).trigger("submit");
					$(formulario).off();
				}
			}
		}
		fnjs_actualizar=function(formulario){
			var campo = '<input name="refresh" type="hidden" value=1>';
			$(formulario).attr('action',"frontend/shared/controller/tablaDB_lista_ver.php");
			$(formulario).append(campo);
			fnjs_enviar_formulario(formulario,'#main');
		}
		