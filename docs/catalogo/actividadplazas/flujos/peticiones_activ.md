---
id: "actividadplazas.peticiones_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Peticiones Activ"
capacidad: "actividadplazas.peticiones_activ.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/peticiones_activ_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Peticiones Activ

Propuesta generada automaticamente desde la capacidad `actividadplazas.peticiones_activ.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PeticionesActiv. Lista de actividades candidatas + peticiones actuales para una persona+tipo.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.peticiones_activ`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.actividades`
- `form.actividades_mas`
- `form.actividades_num`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.id_nom`
- `post.na`
- `post.que`
- `post.sactividad`
- `post.sel`
- `post.stack`
- `post.todos`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_slide_atras`
- `fnjs_mas_actividades`

## Endpoints Del Flujo

- `/src/actividadplazas/peticiones_activ_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
