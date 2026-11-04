---
id: "encargossacd.encargo_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Buscar un encargo"
capacidad: "encargossacd.encargo_select.gestionar"
pantallas_principales: ["encargossacd.pantalla.encargo_select"]
fragmentos: ["encargossacd.pantalla.encargo_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Encargo Select

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Buscar los encargos existentes y acceder a su ficha, o crear uno nuevo.

## Punto De Entrada

Menú **dre > Encargos > ver encargo** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_select`

## Escenarios Inferidos

### Buscar un encargo

Pasos propuestos:
1. Entrar en **ver encargo**.
2. Rellenar los criterios de **buscar un encargo** y pulsar **buscar**.
3. Pulsar el encargo en la tabla para abrirlo (o usar **Buscar en todas las columnas**).

Endpoints asociados:
- `/src/encargossacd/encargo_select_data`

### Crear un encargo

Pasos propuestos:
1. Pulsar **añadir encargo**.
2. Rellenar el formulario (ver «Modificar un encargo») y guardar.

Endpoints asociados:
- `/src/encargossacd/encargo_select_data`

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

