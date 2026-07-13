---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "shared"
titulo: "Mantenimiento genérico de tablas (formulario)"
pantalla: "shared.pantalla.tablaDB_formulario_ver"
preguntas: ["Que se puede hacer en Mantenimiento genérico de tablas (formulario)?", "Que campos tiene Mantenimiento genérico de tablas (formulario)?", "Que acciones hay en Mantenimiento genérico de tablas (formulario)?"]
capacidades: ["shared.tablaDB.gestionar", "shared.tablaDB_depende.gestionar", "shared.tablaDB_formulario.gestionar"]
endpoints: ["/src/shared/tablaDB_depende_datos", "/src/shared/tablaDB_formulario_datos", "/src/shared/tablaDB_update"]
source: "docs/catalogo/shared/pantallas/tablaDB_formulario_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Mantenimiento genérico de tablas (formulario)

## Resumen

Formulario de alta/edición/borrado del patrón `tablaDB`. Campos dinámicos según `DatosCampo` del `Info*`; guardado AJAX a `tablaDB_update`; desplegables dependientes vía `tablaDB_depende_datos`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.accion`
- `form.clase_info`
- `form.valor_depende`
- `post.aSerieBuscar`
- `post.clase_info`
- `post.datos_buscar`
- `post.id_pau`
- `post.k_buscar`
- `post.mod`
- `post.obj_pau`
- `post.permiso`
- `post.sel`
- `post.s_pkey`
- `post.go_to`

## Acciones Detectadas

- `fnjs_actualizar_depende`
- `fnjs_cancelar`
- `fnjs_comprobar_fecha`
- `fnjs_grabar`

## Capacidades Relacionadas

- `shared.tablaDB.gestionar`
- `shared.tablaDB_depende.gestionar`
- `shared.tablaDB_formulario.gestionar`

## Endpoints Relacionados

- `/src/shared/tablaDB_depende_datos`
- `/src/shared/tablaDB_formulario_datos`
- `/src/shared/tablaDB_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
