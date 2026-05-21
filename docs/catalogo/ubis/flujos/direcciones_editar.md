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
estado_revision: "generado"
---

# Flujo - Gestionar Direcciones Editar

Propuesta generada automaticamente desde la capacidad `ubis.direcciones_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DireccionesEditar. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
