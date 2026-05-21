---
id: "asistentes.form_actividades_de_una_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Form Actividades De Una Persona"
capacidad: "asistentes.form_actividades_de_una_persona.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.form_actividades_de_una_persona"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/form_actividades_de_una_persona_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Form Actividades De Una Persona

Propuesta generada automaticamente desde la capacidad `asistentes.form_actividades_de_una_persona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona FormActividadesDeUnaPersona. Dossier actividades de una persona (1301). Datos puros para el formulario; la UI (HashFront, Desplegable) se compone en frontend.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.form_actividades_de_una_persona`

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
- `html.observ`
- `html.propio`

Acciones JavaScript:
- `fnjs_cmb_propietario`
- `fnjs_construir_desplegable_propietario`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/asistentes/form_actividades_de_una_persona_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
