---
id: "shared.pantalla.tablaDB_formulario_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "shared"
nombre: "Mantenimiento genérico de tablas (formulario)"
controller: "frontend/shared/controller/tablaDB_formulario_ver.php"
vistas: ["frontend/shared/view/tablaDB_formulario.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/shared/tablaDB_depende_datos", "/src/shared/tablaDB_formulario_datos", "/src/shared/tablaDB_update"]
capacidades: ["shared.tablaDB.gestionar", "shared.tablaDB_depende.gestionar", "shared.tablaDB_formulario.gestionar"]
campos: ["form.accion", "form.clase_info", "form.valor_depende", "post.aSerieBuscar", "post.clase_info", "post.datos_buscar", "post.id_pau", "post.k_buscar", "post.mod", "post.obj_pau", "post.permiso", "post.sel", "post.s_pkey", "post.go_to"]
acciones: ["fnjs_actualizar_depende", "fnjs_cancelar", "fnjs_comprobar_fecha", "fnjs_grabar"]
estado_revision: "revisado"
---

# Mantenimiento genérico de tablas (formulario)

Formulario de alta/edición/borrado del patrón `tablaDB`. Campos dinámicos según `DatosCampo` del
`Info*`; guardado AJAX a `tablaDB_update`; desplegables dependientes vía `tablaDB_depende_datos`.

## Tipo

- Subtipo: `fragmento_ajax` (llega desde listado o dossier, no desde menú directo)
- Controller: `frontend/shared/controller/tablaDB_formulario_ver.php`

## Acciones

- **Guardar** → `fnjs_grabar` → `/src/shared/tablaDB_update`; éxito → `nav_atras`.
- **Cancelar** → vuelve al listado sin guardar.
- **Dependiente** → `fnjs_actualizar_depende` al cambiar un `<select>` padre.

Hidden relevantes: `clase_info`, `mod`, `s_pkey`, `id_pau`, `go_to` (retorno firmado al listado o
dossier si hay `obj_pau`).

## Endpoints Usados

- `/src/shared/tablaDB_formulario_datos` (carga inicial)
- `/src/shared/tablaDB_depende_datos` (AJAX dependientes)
- `/src/shared/tablaDB_update` (guardar)

## Manual De Usuario

Revisado contra `frontend/shared/view/tablaDB_formulario.phtml`. Tipos de campo: texto, decimal,
fecha (datepicker), opciones, depende, check, hidden, solo lectura (`ver`).

## Ruta de menú

sin entrada de menú en el índice (se abre desde `tablaDB_lista_ver` o dossiers con `obj_pau`).
