---
id: "encargossacd.encargo_horario_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Encargo Horario Select"
capacidad: "encargossacd.encargo_horario_select.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_horario_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_horario_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Encargo Horario Select

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_horario_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoHorarioSelect. Datos para la lista de horarios de un encargo (encargo_horario_select). Se devuelven ya precalculados el texto descriptivo del horario y las fechas formateadas para que el frontend solo arme frontend\shared\web\Lista.

## Punto De Entrada

Fragmento AJAX embebido; sin entrada de menú directa.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_horario_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.desc_enc`
- `html.mod`
- `html.origen`
- `post.id_enc`
- `post.mod`
- `post.origen`
- `post.sel`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/encargo_horario_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

