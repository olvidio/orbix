---
id: "personas.pantalla.personas_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Personas Select"
controller: "frontend/personas/controller/personas_select.php"
vistas: ["frontend/personas/view/personas_select.phtml"]
fragmentos_frontend: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadestudios/controller/ca_posibles.php", "frontend/actividadplazas/controller/peticiones_activ.php", "frontend/certificados/controller/certificado_emitido_imprimir.php", "frontend/certificados/controller/certificado_recibido_adjuntar.php", "frontend/dossiers/controller/dossiers_ver.php", "frontend/notas/controller/tessera_copiar_select.php", "frontend/notas/controller/tessera_imprimir.php", "frontend/notas/controller/tessera_ver.php", "frontend/personas/controller/home_persona.php", "frontend/personas/controller/personas_editar.php", "frontend/personas/controller/stgr_cambio.php", "frontend/personas/controller/traslado_form.php", "frontend/profesores/controller/ficha_profesor_stgr.php"]
endpoints: ["/src/personas/personas_select_data"]
capacidades: ["personas.personas_select.gestionar"]
campos: ["form.id_dossier", "form.que", "form.sel", "html.id_dossier", "html.que", "post.apellido1", "post.apellido2", "post.centro", "post.cmb", "post.es_sacd", "post.exacto", "post.id_sel", "post.na", "post.nombre", "post.que", "post.scroll_id", "post.stack", "post.tabla", "post.tipo"]
acciones: ["fnjs_actividades", "fnjs_copiar_tessera", "fnjs_dossiers", "fnjs_enviar_formulario", "fnjs_ficha", "fnjs_ficha_profe", "fnjs_home", "fnjs_imp_certificado", "fnjs_imp_tessera", "fnjs_lista_activ", "fnjs_matriculas", "fnjs_modificar", "fnjs_modificar_ctr", "fnjs_notas", "fnjs_peticion_activ", "fnjs_posibles_ca", "fnjs_solo_uno", "fnjs_tessera", "fnjs_update_div", "fnjs_upload_certificado"]
estado_revision: "generado"
---

# Personas Select

Tabla de personas que cumplen la condicion introducida en `personas_que`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/personas_select.php`

## Vistas Relacionadas

- `frontend/personas/view/personas_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`
- `frontend/actividadestudios/controller/ca_posibles.php`
- `frontend/actividadplazas/controller/peticiones_activ.php`
- `frontend/certificados/controller/certificado_emitido_imprimir.php`
- `frontend/certificados/controller/certificado_recibido_adjuntar.php`
- `frontend/dossiers/controller/dossiers_ver.php`
- `frontend/notas/controller/tessera_copiar_select.php`
- `frontend/notas/controller/tessera_imprimir.php`
- `frontend/notas/controller/tessera_ver.php`
- `frontend/personas/controller/home_persona.php`
- `frontend/personas/controller/personas_editar.php`
- `frontend/personas/controller/stgr_cambio.php`
- `frontend/personas/controller/traslado_form.php`
- `frontend/profesores/controller/ficha_profesor_stgr.php`

## Endpoints Usados

- `/src/personas/personas_select_data`

## Capacidades Relacionadas

- `personas.personas_select.gestionar`

## Campos Detectados

- `form.id_dossier`
- `form.que`
- `form.sel`
- `html.id_dossier`
- `html.que`
- `post.apellido1`
- `post.apellido2`
- `post.centro`
- `post.cmb`
- `post.es_sacd`
- `post.exacto`
- `post.id_sel`
- `post.na`
- `post.nombre`
- `post.que`
- `post.scroll_id`
- `post.stack`
- `post.tabla`
- `post.tipo`

## Acciones Detectadas

- `fnjs_actividades`
- `fnjs_copiar_tessera`
- `fnjs_dossiers`
- `fnjs_enviar_formulario`
- `fnjs_ficha`
- `fnjs_ficha_profe`
- `fnjs_home`
- `fnjs_imp_certificado`
- `fnjs_imp_tessera`
- `fnjs_lista_activ`
- `fnjs_matriculas`
- `fnjs_modificar`
- `fnjs_modificar_ctr`
- `fnjs_notas`
- `fnjs_peticion_activ`
- `fnjs_posibles_ca`
- `fnjs_solo_uno`
- `fnjs_tessera`
- `fnjs_update_div`
- `fnjs_upload_certificado`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
