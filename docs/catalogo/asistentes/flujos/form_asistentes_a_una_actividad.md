---
id: "asistentes.form_asistentes_a_una_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Form Asistentes A Una Actividad"
capacidad: "asistentes.form_asistentes_a_una_actividad.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.form_asistentes_a_una_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/form_asistentes_a_una_actividad_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Form Asistentes A Una Actividad

Propuesta generada automaticamente desde la capacidad `asistentes.form_asistentes_a_una_actividad.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona FormAsistentesAUnaActividad. Dossier asistentes a una actividad (3101). Datos puros; la UI vive en {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.form_asistentes_a_una_actividad`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.est_ok`
- `html.falta`
- `html.guardar`
- `html.guardar2`
- `html.observ`
- `html.observ_est`
- `html.propio`
- `post.actualizar`

Acciones JavaScript:
- `fnjs_cmb_propietario`
- `fnjs_construir_desplegable_propietario`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_nuevo`

## Endpoints Del Flujo

- `/src/asistentes/form_asistentes_a_una_actividad_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
