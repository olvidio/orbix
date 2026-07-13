---
id: "notas.actividades_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Actividades Buscar"
capacidad: "notas.actividades_buscar.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.actividad_buscar_form"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/actividades_buscar_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Actividades Buscar

Propuesta generada automaticamente desde la capacidad `notas.actividades_buscar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Seleccionar actividad CA vinculada al acta.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.actividad_buscar_form`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.observ`
- `form.pres_mail`
- `form.pres_nom`
- `form.pres_telf`
- `form.zona`
- `post.dl_org`
- `post.f_acta_iso`
- `post.id_activ`

Acciones JavaScript:
- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_update_activ`

## Endpoints Del Flujo

- `/src/notas/actividades_buscar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
