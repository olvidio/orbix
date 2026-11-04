---
id: "encargossacd.horario_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Ver o crear un horario de encargo"
capacidad: "encargossacd.horario_ver.gestionar"
pantallas_principales: ["encargossacd.pantalla.horario_ver"]
fragmentos: ["encargossacd.pantalla.horario_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_ver_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Horario Ver

Propuesta generada automaticamente desde la capacidad `encargossacd.horario_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver el formulario de horario de un encargo (días, horas, nº de sacerdotes, periodo de vigencia).

## Punto De Entrada

Ficha del encargo, **añadir horario**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.horario_ver`

## Escenarios Inferidos

### Ver o crear un horario de encargo

Pasos propuestos:
1. En la lista de horarios, pulsar **añadir horario** o un horario existente.
2. Indicar **dia**, **hora inicio**, **hora fin**, **nº de sacd** y el periodo (**activo desde** / **hasta**).

Endpoints asociados:
- `/src/encargossacd/horario_ver_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.f_fin`
- `html.f_ini`
- `html.h_fin`
- `html.h_ini`
- `html.n_sacd`
- `post.desc_enc`
- `post.id_enc`
- `post.id_item_h`
- `post.mod`
- `post.origen`
- `post.refresh`

Acciones JavaScript:
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/encargossacd/horario_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

