---
id: "encargossacd.encargo_horario_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Consultar los horarios de un encargo"
capacidad: "encargossacd.encargo_horario_select.gestionar"
pantallas_principales: ["encargossacd.pantalla.encargo_horario_select"]
fragmentos: ["encargossacd.pantalla.encargo_horario_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_horario_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Encargo Horario Select

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_horario_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver los horarios de un encargo y añadir otro.

## Punto De Entrada

Ficha del encargo.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_horario_select`

## Escenarios Inferidos

### Consultar los horarios de un encargo

Pasos propuestos:
1. Abrir el encargo.
2. Aparece la lista **horario de** el encargo.
3. Pulsar **añadir horario** para crear uno nuevo, o un horario para modificarlo.

Endpoints asociados:
- `/src/encargossacd/encargo_horario_select_data`

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

