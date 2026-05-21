---
id: "procesos.fases_activ_cambio_tipo_html.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Fases Activ Cambio Tipo Html"
capacidad: "procesos.fases_activ_cambio_tipo_html.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.fases_activ_cambio"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/fases_activ_cambio_tipo_html"]
estado_revision: "generado"
---

# Flujo - Gestionar Fases Activ Cambio Tipo Html

Propuesta generada automaticamente desde la capacidad `procesos.fases_activ_cambio_tipo_html.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona FasesActivCambioTipoActividadHtml. Payload para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.fases_activ_cambio`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.accion`
- `form.dl_propia`
- `form.empiezamax`
- `form.empiezamin`
- `form.entrada`
- `form.extendida`
- `form.id_fase_nueva`
- `form.id_fase_sel`
- `form.id_tipo_activ`
- `form.modo`
- `form.periodo`
- `form.salida`
- `form.year`
- `post.dl_propia`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_fase_nueva`
- `post.id_tipo_activ`
- `post.inicio`
- `post.periodo`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.stack`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/fases_activ_cambio_tipo_html`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
