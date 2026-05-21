---
id: "notas.acta_listado_anual.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Listado Anual"
capacidad: "notas.acta_listado_anual.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_listado_anual"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_listado_anual_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Acta Listado Anual

Propuesta generada automaticamente desde la capacidad `notas.acta_listado_anual.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListadoAnualActas. Lista las actas en un rango de fechas (ISO) ordenadas por nivel y fecha. En ambito rstgr considera todas las delegaciones de la region de stgr; en los demas ambitos, solo la delegacion actual. Cada item es un array asociativo {id_nivel, acta, f_acta, nombre_corto}.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_listado_anual`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/acta_listado_anual_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
