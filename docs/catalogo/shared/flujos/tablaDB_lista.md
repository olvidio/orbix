---
id: "shared.tablaDB_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Gestionar TablaDB Lista"
capacidad: "shared.tablaDB_lista.gestionar"
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver", "shared.pantalla.tablaDB_lista_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/shared/tablaDB_lista_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar TablaDB Lista

Propuesta generada automaticamente desde la capacidad `shared.tablaDB_lista.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TablaDBLista. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `shared.pantalla.tablaDB_formulario_ver`
- `shared.pantalla.tablaDB_lista_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.accion`
- `form.clase_info`
- `form.sel`
- `form.valor_depende`
- `html.<?= $nom_camp ?>`
- `html.btn_new`
- `html.btn_ok`
- `html.k_buscar`
- `html.mod`
- `post.aSerieBuscar`
- `post.clase_info`
- `post.datos_buscar`
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
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar_depende`
- `fnjs_cancelar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_grabar`
- `fnjs_nuevo`

## Endpoints Del Flujo

- `/src/shared/tablaDB_lista_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
