---
id: "notas.acta_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Select"
capacidad: "notas.acta_select.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Acta Select

Propuesta generada automaticamente desde la capacidad `notas.acta_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActaSelect. Lista de actas y mapa de asignaturas para acta_select (frontend sin repositorios).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `form.mod`
- `form.sel`
- `html.acta`
- `html.btn_ok`
- `html.mod`
- `html.refresh`
- `post.acta`
- `post.refresh`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/notas/acta_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
