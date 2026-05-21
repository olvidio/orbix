---
id: "asistentes.activ_pendientes_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Activ Pendientes Select"
capacidad: "asistentes.activ_pendientes_select.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.activ_pendientes_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/activ_pendientes_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Activ Pendientes Select

Propuesta generada automaticamente desde la capacidad `asistentes.activ_pendientes_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActivPendientesSelect. Actividades pendientes por curso (activ_pendientes_select.php). Datos y link_spec sin firmar; hash, firmas y tablas en {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.activ_pendientes_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.any`
- `html.ok`
- `html.sactividad`
- `html.tipo_personas`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Endpoints Del Flujo

- `/src/asistentes/activ_pendientes_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
