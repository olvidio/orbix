jsForm = new Object();
jsForm.form = "";
jsForm.enviar = function () {
    if (this.SoloUno) {
        rta = fnjs_solo_uno(this.form);
    } else {
        rta = 1;
    }
    if (rta == 1) {
        if (this.action) {
            $(this.form).attr('action', this.action);
        }
        if (this.form === '#seleccionados' || this.form === 'seleccionados') {
            if (typeof fnjs_nav_state_patch_form_selection === 'function') {
                fnjs_nav_state_patch_form_selection('#seleccionados', '#scroll_id_actividad_select', 'grid_actividad_select');
            }
            if (typeof fnjs_nav_state_flush === 'function') {
                fnjs_nav_state_flush();
            }
        }
        fnjs_enviar_formulario(this.form);
    }
}

jsForm.refresh = function () {
    if (this.Aviso) {
        if (confirm(this.Aviso)) {
            ok = 1;
        } else {
            ok = 0;
        }
    } else {
        ok = 1;
    }
    if (this.SoloUno) {
        rta = fnjs_solo_uno(this.form);
    } else {
        rta = 1;
    }
    if (ok == 1 && rta == 1) {
        var param = $(this.form).serialize();
        var url = this.action;
        $(this.form).one("submit", function () {
            $.ajax({
                url: url,
                type: 'post',
                data: param,
                dataType: 'json'
            })
                .done(function (rta) {
                    if (rta && typeof rta === 'object') {
                        if (rta.success === false) {
                            alert(rta.mensaje || '');
                            return;
                        }
                        jsForm.actualizar();
                    } else if (rta && rta !== '\n') {
                        alert(rta);
                    } else {
                        jsForm.actualizar();
                    }
                })
                .fail(function (jqXHR) {
                    var msg = '';
                    try {
                        var json = JSON.parse(jqXHR.responseText || '{}');
                        msg = json.mensaje || jqXHR.responseText || '';
                    } catch (e) {
                        msg = jqXHR.responseText || '';
                    }
                    if (msg) { alert(msg); }
                });
            return false;
        });
        $(this.form).trigger("submit");
        $(this.form).off();
    }
}
jsForm.actualizar = function () {
    var continuar = '<input type="hidden" name="continuar" value="si">';
    $(this.form).attr('action', "frontend/actividades/controller/actividad_select.php");
    $(this.form).append(continuar);
    fnjs_enviar_formulario(this.form, '#main');
}

jsForm.update = function (formulario, que) {
    this.form = formulario;
    this.SoloUno = true;
    switch (que) {
        case "publicar":
            this.SoloUno = false;
            $('#mod').val(que);
            this.action = "src/actividades/actividad_publicar";
            break;
        case "importar":
            this.SoloUno = false;
            $('#mod').val(que);
            this.action = "src/actividades/actividad_importar";
            break;
        case "duplicar":
            this.Aviso = "Seguro que desa duplicar esta actividad";
            $('#mod').val(que);
            this.action = "src/actividades/actividad_duplicar";
            break;
    }
    this.refresh();
}

jsForm.mandar = function (formulario, que) {
    this.form = formulario;
    this.SoloUno = true;
    switch (que) {
        case "proceso":
            this.action = "frontend/procesos/controller/actividad_proceso.php";
            break;
        case "datos":
            this.action = "frontend/actividades/controller/actividad_ver.php";
            break;
        case "cambiar_tipo":
            $('#mod').val(que);
            this.action = "frontend/actividades/controller/actividad_ver.php";
            break;
        case "lista_clase":
            this.action = "frontend/actividadestudios/controller/lista_clases_ca.php";
            break;
        case "posibles_asignaturas":
            this.action = "frontend/actividadestudios/controller/posibles_asignaturas_ca.php";
            break;
        case "plan_estudios":
            this.action = "frontend/actividadestudios/controller/plan_estudios_ca.php";
            break;
        case "dossiers":
            //$('#queSel').val(que);
            this.action = "frontend/dossiers/controller/dossiers_ver.php";
            break;
        case "asig":
            $('#queSel').val(que);
            $('#id_dossier').val(3005);
            this.action = "frontend/dossiers/controller/dossiers_ver.php";
            break;
        case "carg":
            $('#queSel').val(que);
            $('#id_dossier').val(3102);
            this.action = "frontend/dossiers/controller/dossiers_ver.php";
            break;
        case "asis":
            $('#queSel').val(que);
            $('#id_dossier').val(3101);
            this.action = "frontend/dossiers/controller/dossiers_ver.php";
            break;
        case "asis_peticiones":
            $('#queSel').val(que);
            this.action = "frontend/asistentes/controller/tabla_peticiones.php";
            break;
        case "camas":
            $('#queSel').val(que);
            this.action = "frontend/ubiscamas/controller/lista_habitaciones.php";
            break;
        case "plazas":
            $('#queSel').val(que);
            this.action = "frontend/actividadplazas/controller/resumen_plazas.php";
            break;
        case "list":
            $('#queSel').val(que);
            this.action = "frontend/asistentes/controller/lista_asistentes.php";
            break;
        case "listcl":
            $('#queSel').val(que);
            this.action = "frontend/asistentes/controller/lista_asistentes.php";
            break;
        case "historicos":
            $('#queSel').val(que);
            $('#id_dossier').val(1301);
            this.action = "frontend/actividades/controller/dossiers/historics.php";
            break;
    }
    this.enviar();
}

