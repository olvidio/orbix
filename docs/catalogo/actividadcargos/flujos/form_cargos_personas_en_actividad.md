---
id: "actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Form Cargos Personas En Actividad"
capacidad: "actividadcargos.form_cargos_personas_en_actividad.gestionar"
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.form_cargos_personas_en_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadcargos/form_cargos_personas_en_actividad_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Form Cargos Personas En Actividad

Propuesta generada automaticamente desde la capacidad `actividadcargos.form_cargos_personas_en_actividad.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Pendiente de revisar. Redactar aqui el objetivo en lenguaje de usuario, no tecnico.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.form_cargos_personas_en_actividad`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.asis`
- `html.asis_presente`
- `html.guardar`
- `html.id_activ`
- `html.observ`
- `html.puede_agd`

Acciones JavaScript:
- `fnjs_cargos_pers_datos_ok`
- `fnjs_guardar_cargo_pers`

## Endpoints Del Flujo

- `/src/actividadcargos/form_cargos_personas_en_actividad_data`

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
