---
id: "encargossacd.encargo_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Encargo Select"
capacidad: "encargossacd.encargo_select.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Encargo Select

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoSelect. Datos para la lista de encargos (encargo_select). El frontend construye la frontend\shared\web\Lista y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

## Punto De Entrada

Menú: dre > Encargos > ver encargo.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.id_nom`
- `form.que`
- `form.scroll_id`
- `form.sel`
- `html.desc_enc`
- `html.ok`
- `html.que`
- `post.desc_enc`
- `post.id_tipo_enc`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_horario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_strip_hash_sel`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/encargo_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** dre > Encargos > ver encargo
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos


## Ruta de menú

- **Legacy:** dre > Encargos > ver encargo
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos

