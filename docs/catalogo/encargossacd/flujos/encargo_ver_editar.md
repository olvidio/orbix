---
id: "encargossacd.encargo_ver_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Guardar los cambios de un encargo"
capacidad: "encargossacd.encargo_ver_editar.gestionar"
pantallas_principales: ["encargossacd.pantalla.encargo_ver"]
fragmentos: ["encargossacd.pantalla.encargo_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/encargossacd/encargo_ver_editar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Encargo Ver Editar

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_ver_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Guardar los cambios hechos en un encargo.

## Punto De Entrada

Menú **dre > Encargos > ver encargo** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_ver`

## Escenarios Inferidos

### Guardar los cambios de un encargo

Pasos propuestos:
1. Modificar los campos del encargo.
2. Pulsar **modificar encargo**.

Endpoints asociados:
- `/src/encargossacd/encargo_ver_editar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.desc_enc`
- `form.desc_lugar`
- `form.filtro_ctr`
- `form.grupo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_zona`
- `form.idioma_enc`
- `form.lst_ctrs`
- `form.nom_tipo`
- `form.observ`
- `form.que`
- `html.desc_enc`
- `html.desc_lugar`
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

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_generarNomEnc`
- `fnjs_guardar`
- `fnjs_lista_ctrs`
- `fnjs_lista_ctrs_por_zona`
- `fnjs_lista_zonas`
- `fnjs_lst_tipo_enc`
- `fnjs_strip_hash_params`

## Endpoints Del Flujo

- `/src/encargossacd/encargo_ver_editar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

