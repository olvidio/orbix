---
id: "actividades.actividad_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad Ver"
capacidad: "actividades.actividad_ver.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_ver", "actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_ver_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Ver

Propuesta generada automaticamente desde la capacidad `actividades.actividad_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadVerDatos. Devuelve los fragmentos HTML y valores auxiliares que necesita el formulario "ver/editar actividad" para renderizarse sin que el frontend acceda directamente a src/.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl_org`
- `form.isfsv`
- `form.ssfsv`
- `post.id_activ`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.mod`
- `post.obj_pau`
- `post.refresh`
- `post.sactividad`
- `post.sasistentes`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/actividad_ver_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
