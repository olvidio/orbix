---
id: "ubis.teleco_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Añadir o editar una telecomunicación"
capacidad: "ubis.teleco_editar.gestionar"
pantallas_principales: ["ubis.pantalla.teleco_editar"]
fragmentos: ["ubis.pantalla.teleco_editar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/teleco_editar"]
estado_revision: "revisado"
---

# Flujo - Teleco Editar

## Objetivo De Usuario

Añadir una telecomunicación o modificar una existente.

## Punto De Entrada

Menú **scdl > direcciones > buscar**: buscar, pulsar el nombre del centro o casa en la tabla y usar «Editar: **centro** / **dirección** / **telecos**» de la ficha.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_editar`

## Escenarios Inferidos

### Añadir o editar una telecomunicación

Pasos propuestos:
1. En **telecos** pulsar **nuevo**, o marcar un registro y pulsar **modificar**.
2. Elegir el **Tipo teleco** (correo electrónico, fax, página web, teléfono fijo o teléfono móvil); entonces el desplegable **Descripción** se rellena con las opciones de ese tipo (por ejemplo otros, de la casa, dirección, administración, sacd). Escribir el **Número** y, si hace falta, las **Observaciones**.
3. Pulsar **Guardar Cambios**. Vuelve a la ficha y el número aparece junto a los demás teléfonos.

Endpoints asociados:
- `/src/ubis/teleco_editar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_desc_teleco`
- `form.id_tipo_teleco`
- `form.mod`
- `form.num_teleco`
- `form.observ`
- `html.mod`
- `html.num_teleco`
- `html.observ`
- `post.id_ubi`
- `post.mod`
- `post.obj_pau`
- `post.s_pkey`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar_descripcion`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/teleco_editar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
