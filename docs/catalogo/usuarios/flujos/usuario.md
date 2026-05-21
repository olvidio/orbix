---
id: "usuarios.usuario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario"
capacidad: "usuarios.usuario.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_lista"]
acciones: ["eliminar", "guardar", "listar", "ver_formulario"]
endpoints: ["/src/usuarios/usuario_eliminar", "/src/usuarios/usuario_form", "/src/usuarios/usuario_guardar", "/src/usuarios/usuario_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Usuario

Propuesta generada automaticamente desde la capacidad `usuarios.usuario.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona usuario, usuariosLista. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_lista`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/usuarios/usuario_eliminar`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/usuarios/usuario_lista`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/usuarios/usuario_form`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_usuario`
- `form.password`
- `form.sel`
- `form.username`
- `form.usuario`
- `html.btn_ok`
- `html.cambio_password`
- `html.has_2fa`
- `html.password`
- `post.id_sel`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`
- `post.username`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_grup`
- `fnjs_buscar`
- `fnjs_chk_passwd`
- `fnjs_del_grup`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_left_side_hide`
- `fnjs_lst_add_grup`
- `fnjs_lst_del_grup`
- `fnjs_mas_casas`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
