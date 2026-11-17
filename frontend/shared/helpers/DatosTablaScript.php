<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * JavaScript de las pantallas genéricas de tabla (`tablaDB_lista_ver`, `tablaDB_busqueda`, ficha de dossier
 * con tabla): `fnjs_nuevo`, `fnjs_modificar`, `fnjs_eliminar` y `fnjs_actualizar`.
 *
 * Antes lo generaba `src\shared\domain\DatosTablaRepo::getScript()`; ahora `src/` solo devuelve
 * `eliminar_txt` y el frontend monta el script.
 */
final class DatosTablaScript
{
    public const ACTION_FORM = 'frontend/shared/controller/tablaDB_formulario_ver.php';
    public const ACTION_UPDATE = 'src/shared/tablaDB_update';
    public const ACTION_TABLA = 'frontend/shared/controller/tablaDB_lista_ver.php';

    public static function script(
        string $eliminar_txt,
        string $bloque = '#main',
        string $action_form = self::ACTION_FORM,
        string $action_update = self::ACTION_UPDATE,
        string $action_tabla = self::ACTION_TABLA,
    ): string {
        $respuesta = _("respuesta");
        $html_script = "
		fnjs_nuevo=function(formulario){
			$('#mod').val(\"nuevo\");
			$(formulario).attr('action',\"$action_form\");
			fnjs_enviar_formulario(formulario,'$bloque');
		}
		fnjs_modificar=function(formulario){
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				$('#mod').val(\"editar\");
				$(formulario).attr('action',\"$action_form\");
				fnjs_enviar_formulario(formulario,'$bloque');
			}
		}
		fnjs_eliminar=function(formulario){
			var err;
			var eliminar;
			eliminar=\"$eliminar_txt\";

			if (!eliminar) eliminar=\"" . _("¿Está seguro que desea eliminar este registro?") . "\";
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				if (confirm(eliminar) ) {
					var ctx_eliminar = fnjs_ctx_fila_seleccionada(formulario, 'ctx_eliminar');
					if (!ctx_eliminar) {
						alert(\"$respuesta\" + ': ' + " . json_encode(_("Operación no autorizada")) . ");
						return;
					}
					$('#mod').val(\"eliminar\");
					$(formulario).attr('action',\"$action_update\");
					$(formulario).one(\"submit\", function() {
						$.ajax({
							url: $(this).attr('action'),
							type: 'post',
							data: $(this).serialize() + '&ctx_eliminar=' + encodeURIComponent(ctx_eliminar),
							dataType: 'json'
							})
                        .done(function (json) {
                            if (json.success !== true) {
                                alert(\"$respuesta\" + ': ' + json.mensaje);
                            } else {
                                fnjs_actualizar(formulario);
                            }
                        });
						return false;
					});
					$(formulario).trigger(\"submit\");
					$(formulario).off();
				}
			}
		}
		fnjs_actualizar=function(formulario){
			var campo = '<input name=\"refresh\" type=\"hidden\" value=1>';
			$(formulario).attr('action',\"$action_tabla\");
			$(formulario).append(campo);
			fnjs_enviar_formulario(formulario,'#main');
		}
		";
        return $html_script;
    }
}
