---
id: "shared.tablaDB_formulario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Gestionar TablaDB Formulario"
capacidad: "shared.tablaDB_formulario.gestionar"
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/shared/tablaDB_formulario_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar TablaDB Formulario

Propuesta generada automaticamente desde la capacidad `shared.tablaDB_formulario.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TablaDBFormulario. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `shared.pantalla.tablaDB_formulario_ver`

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
- `form.valor_depende`
- `html.<?= $nom_camp ?>`
- `post.aSerieBuscar`
- `post.clase_info`
- `post.datos_buscar`
- `post.id_pau`
- `post.k_buscar`
- `post.mod`
- `post.obj_pau`
- `post.permiso`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar_depende`
- `fnjs_cancelar`
- `fnjs_comprobar_fecha`
- `fnjs_grabar`

## Endpoints Del Flujo

- `/src/shared/tablaDB_formulario_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
