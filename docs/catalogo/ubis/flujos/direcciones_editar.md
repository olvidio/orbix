---
id: "ubis.direcciones_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direcciones Editar"
capacidad: "ubis.direcciones_editar.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direcciones_editar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_editar"]
estado_revision: "revisado"
---

# Flujo - Direcciones Editar

## Objetivo De Usuario

Carga la ficha de edición de direcciones de un ubi, con navegación entre varias direcciones.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_editar`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.a_p`
- `form.act`
- `form.c_p`
- `form.direccion`
- `form.f_direccion`
- `form.id_direccion`
- `form.id_ubi`
- `form.latitud`
- `form.longitud`
- `form.nom_sede`
- `form.obj_dir`
- `form.observ`
- `form.pais`
- `form.poblacion`
- `form.provincia`
- `form.que`
- `html.a_p`
- `html.c_p`
- `html.cp_dcha`
- `html.direccion`
- `html.f_direccion`
- `html.latitud`
- `html.longitud`
- `html.nom_sede`
- `html.observ`
- `html.pais`
- `html.poblacion`
- `html.principal`
- `html.propietario`
- `html.provincia`
- `html.que`
- `post.id_direccion`
- `post.id_ubi`
- `post.idx`
- `post.inc`
- `post.mod`
- `post.obj_dir`
- `post.refresh`

Acciones JavaScript:
- `fnjs_add_dir`
- `fnjs_adjuntar`
- `fnjs_asignar_dir`
- `fnjs_eliminar`
- `fnjs_guardar_dir`
- `fnjs_otro`
- `fnjs_quitar_dir`
- `fnjs_update_div`
- `fnjs_ver_dir`
- `fnjs_ver_documento`

## Endpoints Del Flujo

- `/src/ubis/direcciones_editar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
