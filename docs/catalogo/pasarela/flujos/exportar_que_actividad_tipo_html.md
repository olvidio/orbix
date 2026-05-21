---
id: "pasarela.exportar_que_actividad_tipo_html.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Exportar Que Actividad Tipo Html"
capacidad: "pasarela.exportar_que_actividad_tipo_html.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.exportar_que"]
acciones: ["ejecutar"]
endpoints: ["/src/pasarela/exportar_que_actividad_tipo_html"]
estado_revision: "generado"
---

# Flujo - Gestionar Exportar Que Actividad Tipo Html

Propuesta generada automaticamente desde la capacidad `pasarela.exportar_que_actividad_tipo_html.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ExportarQueActividadTipoHtml. HTML del selector de tipo de actividad para la pantalla «exportar qué». Replica la configuración que antes hacía {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.exportar_que`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.cdc_sel`
- `form.empiezamax`
- `form.empiezamin`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_cdc`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.periodo`
- `form.year`
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_cdc_mas`
- `post.id_cdc_num`
- `post.id_tipo_activ`
- `post.inicio`
- `post.periodo`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`
- `post.stack`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/pasarela/exportar_que_actividad_tipo_html`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
