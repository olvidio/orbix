---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "shared"
titulo: "Mantenimiento genérico de tablas (listado)"
pantalla: "shared.pantalla.tablaDB_lista_ver"
preguntas: ["Que se puede hacer en Mantenimiento genérico de tablas (listado)?", "Que campos tiene Mantenimiento genérico de tablas (listado)?", "Que acciones hay en Mantenimiento genérico de tablas (listado)?"]
capacidades: ["shared.tablaDB_buscar.gestionar", "shared.tablaDB_lista.gestionar"]
endpoints: ["/src/shared/tablaDB_buscar_datos", "/src/shared/tablaDB_lista_datos"]
source: "docs/catalogo/shared/pantallas/tablaDB_lista_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Mantenimiento genérico de tablas (listado)

## Resumen

Shell transversal de listado CRUD: formulario de búsqueda (si aplica) + tabla editable con selección, botón «nuevo» (solo `permiso === 3`) y acciones de fila definidas por cada `Info*`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `html.btn_new`
- `html.k_buscar`
- `html.mod`
- `post.aSerieBuscar`
- `post.clase_info`
- `post.id_pau`
- `post.id_sel`
- `post.k_buscar`
- `post.mod`
- `post.obj_pau`
- `post.pau`
- `post.permiso`
- `post.refresh`
- `post.scroll_id`
- `post.sel`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_nuevo`

## Capacidades Relacionadas

- `shared.tablaDB_buscar.gestionar`
- `shared.tablaDB_lista.gestionar`

## Endpoints Relacionados

- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
