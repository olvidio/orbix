---
id: "encargossacd.encargo_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Modificar un encargo"
capacidad: "encargossacd.encargo_ver.gestionar"
pantallas_principales: ["encargossacd.pantalla.encargo_ver"]
fragmentos: ["encargossacd.pantalla.encargo_select", "encargossacd.pantalla.encargo_ver"]
acciones: ["crear", "eliminar", "obtener_datos"]
endpoints: ["/src/encargossacd/encargo_ver_data", "/src/encargossacd/encargo_ver_eliminar", "/src/encargossacd/encargo_ver_nuevo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Encargo Ver

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Crear, ver, modificar o eliminar un encargo (atención de un centro, zona, etc.).

## Punto De Entrada

Menú **dre > Encargos > ver encargo** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_select`
- `encargossacd.pantalla.encargo_ver`

## Escenarios Inferidos

### Modificar un encargo

Pasos propuestos:
1. Abrir el encargo desde **ver encargo**.
2. Cambiar el **grupo**, el **tipo encargo**, el **centro** o la **zona**, el **lugar**, la **descripción** y el **idioma**.
3. Pulsar **modificar encargo**.

Endpoints asociados:
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

### Crear un encargo nuevo

Pasos propuestos:
1. En **ver encargo** pulsar **añadir encargo**.
2. Elegir el **tipo de encargo** («escoger el tipo de encargo»), el centro o zona y la descripción.
3. Pulsar **nuevo encargo** (o **generar**).

Endpoints asociados:
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

### Eliminar un encargo

Pasos propuestos:
1. En la tabla de encargos, marcar el encargo y eliminarlo; confirmar.

Endpoints asociados:
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.desc_enc`
- `form.desc_lugar`
- `form.filtro_ctr`
- `form.grupo`
- `form.id_activ`
- `form.id_enc`
- `form.id_nom`
- `form.id_tipo_enc`
- `form.id_zona`
- `form.idioma_enc`
- `form.lst_ctrs`
- `form.nom_tipo`
- `form.observ`
- `form.que`
- `form.scroll_id`
- `form.sel`
- `html.desc_enc`
- `html.desc_lugar`
- `html.ok`
- `html.que`
- `post.desc_enc`
- `post.desc_lugar`
- `post.filtro_ctr`
- `post.grupo`
- `post.id_enc`
- `post.id_tipo_enc`
- `post.id_zona`
- `post.que`
- `post.refresh`
- `post.sel`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_construir_desplegable`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_generarNomEnc`
- `fnjs_guardar`
- `fnjs_horario`
- `fnjs_lista_ctrs`
- `fnjs_lista_ctrs_por_zona`
- `fnjs_lista_zonas`
- `fnjs_lst_tipo_enc`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_strip_hash_params`
- `fnjs_strip_hash_sel`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

