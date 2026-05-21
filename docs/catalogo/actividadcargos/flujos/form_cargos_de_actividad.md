---
id: "actividadcargos.form_cargos_de_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Form Cargos De Actividad"
capacidad: "actividadcargos.form_cargos_de_actividad.gestionar"
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.form_cargos_de_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadcargos/form_cargos_de_actividad_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Form Cargos De Actividad

Propuesta generada automaticamente desde la capacidad `actividadcargos.form_cargos_de_actividad.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Asignar o editar el cargo de una persona en una actividad (tipo de cargo, AGD, observaciones y, en altas, si asiste).

Plantilla de redacción revisada en `docs/manual/actividadcargos.md` (sección Form Cargos De Actividad).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.form_cargos_de_actividad`

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
- `html.cancel`
- `html.guardar`
- `html.observ`
- `html.puede_agd`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_cargo_de_actividad_datos_ok`
- `fnjs_guardar_cargo_act`

## Endpoints Del Flujo

- `/src/actividadcargos/form_cargos_de_actividad_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
