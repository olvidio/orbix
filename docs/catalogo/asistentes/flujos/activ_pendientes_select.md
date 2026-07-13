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
estado_revision: "revisado"
---

# Flujo - Gestionar Activ Pendientes Select

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Identificar personas sin ca/crt en el curso.


## Punto De Entrada

Pantalla `activ_pendientes_select` (`frontend/asistentes/controller/`).


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

## Ruta de menú

- **Legacy:** vsm/vest/dagd/dre > pendientes según `sactividad` y `tipo_personas`
- **Pills2:** ACTIVIDADES > Listados > Listado de personas sin ca/crt
