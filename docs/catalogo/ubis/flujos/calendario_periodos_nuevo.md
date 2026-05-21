---
id: "ubis.calendario_periodos_nuevo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Calendario Periodos Nuevo"
capacidad: "ubis.calendario_periodos_nuevo.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.calendario_periodos_nuevo"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/calendario_periodos_nuevo_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Calendario Periodos Nuevo

Propuesta generada automaticamente desde la capacidad `ubis.calendario_periodos_nuevo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CalendarioPeriodosNuevo. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.calendario_periodos_nuevo`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.f_fin`
- `form.f_ini`
- `form.sfsv`
- `post.id_ubi`
- `post.year`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/calendario_periodos_nuevo_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
