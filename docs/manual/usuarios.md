---
tipo: "manual_usuario"
modulo: "usuarios"
flujos: 33
estado_revision: "generado"
---

# Manual De Usuario - usuarios

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## App Login

### Para Que Sirve

Autenticación app móvil con credenciales y 2FA opcional.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario y contraseña obligatorios`
- `Esquema no indicado`
- `Esquema no válido`
- `Error de autenticación`

### Referencias Internas

- Flujo: `usuarios.app_login.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/app_login.md`

## App Session

### Para Que Sirve

Comprueba si hay sesión autenticada al arrancar la app móvil (sin credenciales).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.app_session.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/app_session.md`

## Borrar Pwd

### Para Que Sirve

- Herramienta de pruebas: resetea contraseñas al login en todos los esquemas (excepto superadmin id_role=1).
- Solo WEBDIR=pruebas o Docker.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se pudieron obtener esquemas`
- `Sólo se puede borrar en la base de datos de pruebas`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.borrar_pwd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/borrar_pwd.md`

## Check First Login 2fa

### Para Que Sirve

Tras login web, redirige a configuración 2FA si el usuario no la tiene activada; si no, continúa al home.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.check_first_login_2fa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/check_first_login_2fa.md`

## Grupo

### Para Que Sirve

Administración de grupos de permisos: listar, alta/edición y borrado.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `Grupo no encontrado`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.grupo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/grupo.md`

## Grupo Info

### Para Que Sirve

Devuelve el nombre de un grupo para el formulario de edición.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Grupo no encontrado`

### Referencias Internas

- Flujo: `usuarios.grupo_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/grupo_info.md`

## Mails Contactos Region

### Para Que Sirve

Devuelve contactos email de usuarios regionales con permisos de oficina relevantes (pantalla recuperación).

### Donde Entrar

- Mails Contactos Region (frontend/usuarios/controller/mails_contactos_region.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.mails_contactos_region.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/mails_contactos_region.md`

## Perm Activ

### Para Que Sirve

Gestión permisos actividad-proceso de un usuario (módulo procesos).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.perm_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/perm_activ.md`

## Perm Menu

### Para Que Sirve

Gestión permisos menú DL de un usuario desde su ficha.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.perm_menu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/perm_menu.md`

## Perm Menu Info

### Para Que Sirve

Carga formulario modal de permiso menú (nuevo o edición).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Grupo no encontrado`

### Referencias Internas

- Flujo: `usuarios.perm_menu_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/perm_menu_info.md`

## Preferencia Tabla

### Para Que Sirve

Devuelve preferencias de presentación de tablas (global y SlickGrid por id_tabla+idioma).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.preferencia_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/preferencia_tabla.md`

## Preferencias

### Para Que Sirve

Ajuste preferencias personales: layout, inicio, idioma, tablas y estilo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.preferencias.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/preferencias.md`

## Recuperar 2fa Mail

### Para Que Sirve

Recuperación 2FA: genera código/link y envía mail al usuario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Esquema no válido`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `No se encontró ningún usuario con ese nombre`

### Referencias Internas

- Flujo: `usuarios.recuperar_2fa_mail.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/recuperar_2fa_mail.md`

## Recuperar Password Mail

### Para Que Sirve

Recuperación contraseña: genera pwd temporal, marca cambio obligatorio y envía mail.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Esquema no válido`
- `Error al preparar la consulta`
- `Error al ejecutar la consulta`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `Error al actualizar la contraseña`
- `No se encontró ningún usuario con ese nombre`

### Referencias Internas

- Flujo: `usuarios.recuperar_password_mail.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/recuperar_password_mail.md`

## Role

### Para Que Sirve

Administración de roles: listar, crear/editar flags sf/sv/pau/dmz y asignar grupmenus.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.role.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role.md`

## Role Grupmenu Add

### Para Que Sirve

Asocia grupmenu a rol (tokens sel `id_role#id_grupmenu`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `debe seleccionar uno`

### Referencias Internas

- Flujo: `usuarios.role_grupmenu_add.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_grupmenu_add.md`

## Role Grupmenu Del

### Para Que Sirve

Quita asociación grupmenu↔rol por id_item.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`
- `debe seleccionar uno`

### Referencias Internas

- Flujo: `usuarios.role_grupmenu_del.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_grupmenu_del.md`

## Role Grupmenu Info

### Para Que Sirve

Lista grupmenus disponibles para añadir a un rol.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Rol no encontrado`

### Referencias Internas

- Flujo: `usuarios.role_grupmenu_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_grupmenu_info.md`

## Role Info

### Para Que Sirve

Carga ficha rol: datos, permiso de edición y tabla grupmenus ya asignados.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `Rol no encontrado`

### Referencias Internas

- Flujo: `usuarios.role_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_info.md`

## Usuario

### Para Que Sirve

Administración de usuarios web: listar, alta/edición en ficha, borrado y asignación grupos/permisos.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.usuario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario.md`

## Usuario 2fa

### Para Que Sirve

Configuración autenticación dos factores del usuario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `Se requiere un código de verificación para activar 2FA`
- `Código de verificación inválido`
- `Hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_2fa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_2fa.md`

## Usuario 2fa Info

### Para Que Sirve

Estado 2FA del usuario para formulario configuración.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Id de usuario no válido`
- `Usuario no encontrado`

### Referencias Internas

- Flujo: `usuarios.usuario_2fa_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_2fa_info.md`

## Usuario 2fa Verify

### Para Que Sirve

Valida código TOTP contra secret provisional (paso previo a activar 2FA).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Código de verificación o clave secreta no válidos`
- `Código de verificación inválido`

### Referencias Internas

- Flujo: `usuarios.usuario_2fa_verify.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_2fa_verify.md`

## Usuario Ayuda Info

### Para Que Sirve

Ayuda acceso login: email ofuscado del usuario y contacto admin regional.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Esquema no válido`
- `Debe ingresar un nombre de usuario válido`
- `No hay email asociado a este usuario`

### Referencias Internas

- Flujo: `usuarios.usuario_ayuda_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_ayuda_info.md`

## Usuario Check Pwd

### Para Que Sirve

Valida fortaleza de contraseña (JsonResponse directo, no envelope ContestarJson).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.usuario_check_pwd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_check_pwd.md`

## Usuario Grupo Add

### Para Que Sirve

Asocia grupo permisos a usuario (ctx HashB `usuario_grupo_add`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_add.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_add.md`

## Usuario Grupo Del

### Para Que Sirve

Quita grupo permisos del usuario (ctx HashB `usuario_grupo_del`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_del.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_del.md`

## Usuario Grupo Del Lst

### Para Que Sirve

Lista grupos ya asignados al usuario con acción quitar.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_del_lst.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_del_lst.md`

## Usuario Grupo Lst

### Para Que Sirve

Lista grupos disponibles para asignar al usuario (id ~ ^5, excluye ya asignados).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_lst.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_lst.md`

## Usuario Guardar Mail

### Para Que Sirve

Actualiza email del usuario (preferencias o admin).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_guardar_mail.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_guardar_mail.md`

## Usuario Guardar Pwd

### Para Que Sirve

Cambia contraseña tras validar fortaleza; limpia flag cambio_password.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_guardar_pwd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_guardar_pwd.md`

## Usuario Info

### Para Que Sirve

Resumen usuario para cabecera ficha (grupos, login, email).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Id de usuario no válido`
- `Usuario no encontrado`

### Referencias Internas

- Flujo: `usuarios.usuario_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_info.md`

## Usuario Preferencias

### Para Que Sirve

Carga datos iniciales de la pantalla preferencias.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.usuario_preferencias.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_preferencias.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
