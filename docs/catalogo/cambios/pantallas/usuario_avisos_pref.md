---
id: "cambios.pantalla.usuario_avisos_pref"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Configurar aviso"
controller: "frontend/cambios/controller/usuario_avisos_pref.php"
vistas: ["frontend/cambios/view/usuario_avisos_pref.phtml"]
fragmentos_frontend: ["frontend/cambios/controller/usuario_avisos_pref_fases.php", "frontend/cambios/controller/usuario_avisos_pref_propiedades.php", "frontend/cambios/controller/usuario_avisos_pref_condicion.php"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data", "/src/cambios/cambio_usuario_objeto_pref_guardar", "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas", "/src/cambios/cambio_usuario_propiedad_pref_preview"]
capacidades: ["cambios.usuario_avisos_pref.gestionar"]
campos: ["html.dl_propia", "html.id_tipo_activ", "html.salida", "post.id_item_usuario_objeto", "post.id_usuario", "post.quien", "post.salida", "post.sel"]
acciones: ["fnjs_actualizar_fases", "fnjs_actualizar_propiedades", "fnjs_cerrar", "fnjs_grabar_todo", "fnjs_guardar_cond", "fnjs_mas_casas", "fnjs_modificar", "fnjs_update_div"]
estado_revision: "revisado"
---

# Configurar aviso

Formulario completo para definir una preferencia de aviso: objeto vigilado, tipo de actividad, fase de
referencia, tipo de aviso, casas y propiedades con condiciones.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/usuario_avisos_pref.php`

## Endpoints Usados

- `/src/cambios/usuario_avisos_pref_form_data` (bootstrap)
- `/src/cambios/cambio_usuario_objeto_pref_guardar` + `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas` (`fnjs_grabar_todo`)
- `/src/cambios/cambio_usuario_propiedad_pref_preview` (`fnjs_guardar_cond`)

## Fragmentos AJAX

- Fases, propiedades y modal de condición (controladores en `frontend/cambios/controller/`)

## Manual De Usuario

1. Elegir objeto y tipo de actividad; ajustar fase y flags de aviso.
2. Marcar propiedades a vigilar; opcionalmente configurar condición por propiedad.
3. Grabar: primero persiste el objeto-pref, luego sincroniza propiedades.

## Ruta de menú

sin entrada de menú en el índice (se abre desde `usuario_form_avisos` o equivalente en grupos)
