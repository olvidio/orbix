---
tipo: "manual_usuario"
modulo: "usuarios"
flujos: 33
estado_revision: "revisado"
---

# Manual De Usuario - usuarios

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Entrar desde la app móvil

### Para Que Sirve

Iniciar sesión desde la aplicación móvil con usuario, contraseña y, si procede, código 2FA.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Entrar desde la app móvil

1. Abrir la app e indicar usuario, contraseña y esquema.
2. Si el usuario tiene 2FA, escribir el código de verificación.
3. Si falta usuario o contraseña aparece «Usuario y contraseña obligatorios».

### Errores O Avisos Frecuentes

- `Usuario y contraseña obligatorios`
- `Esquema no indicado`
- `Esquema no válido`
- `Error de autenticación`

### Referencias Internas

- Flujo: `usuarios.app_login.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/app_login.md`

## Reabrir la app con sesión activa

### Para Que Sirve

Al arrancar la app móvil, comprobar si ya hay una sesión abierta para no pedir de nuevo las credenciales.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Reabrir la app con sesión activa

1. Abrir la app.
2. Si la sesión sigue vigente entra directamente; si no, muestra la pantalla de acceso.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.app_session.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/app_session.md`

## Borrar las contraseñas en pruebas

### Para Que Sirve

Herramienta de pruebas: borra las contraseñas de todos los usuarios (salvo el superadministrador) para poder entrar sin ellas.

### Donde Entrar

- Borrar Todos Pwd (frontend/usuarios/controller/borrar_todos_pwd.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Borrar las contraseñas en pruebas

1. Entrar en la opción del menú **sistema**.
2. Confirmar la acción: afecta a todos los esquemas.
3. Solo funciona en la base de pruebas; en producción aparece «Sólo se puede borrar en la base de datos de pruebas».

### Errores O Avisos Frecuentes

- `No se pudieron obtener esquemas`
- `Sólo se puede borrar en la base de datos de pruebas`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.borrar_pwd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/borrar_pwd.md`

## Primer acceso sin 2FA

### Para Que Sirve

Tras iniciar sesión, llevar al usuario a configurar la verificación en dos pasos si aún no la tiene.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Primer acceso sin 2FA

1. Iniciar sesión con login y contraseña.
2. Si el usuario no tiene la verificación en dos pasos activa, se le lleva a la pantalla de configuración.
3. Si ya la tiene, entra directamente a la página de inicio.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.check_first_login_2fa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/check_first_login_2fa.md`

## Buscar un grupo

### Para Que Sirve

Administrar los grupos de permisos que se asignan a los usuarios: crear, renombrar y eliminar.

### Donde Entrar

- Grupo Lista (frontend/usuarios/controller/grupo_lista.php)
- **Legacy:** sistema > usuarios web > grupos
- **Pills2:** ADMIN LOCAL > usuarios web > grupos

### Tareas Habituales

#### Buscar un grupo

1. Entrar en **grupos**.
2. Escribir parte del nombre en **Nombre** y pulsar **Buscar**.

#### Crear un grupo

1. Pulsar **NUEVO GRUPO**.
2. Escribir el nombre. Convención de la pantalla: poner entre paréntesis si el grupo se refiere a menús (m), permisos (p) o ambos (m+p).
3. Guardar.

#### Cambiar el nombre de un grupo

1. Pulsar el grupo en la lista, modificar el nombre y guardar.

#### Eliminar un grupo

1. Marcar el grupo en la lista y pulsar eliminar.
2. Confirmar. Si hay un error aparece «hay un error, no se ha eliminado».

### Errores O Avisos Frecuentes

- `Grupo no encontrado`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.grupo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/grupo.md`

## Abrir un grupo para editarlo

### Para Que Sirve

Cargar el nombre de un grupo en su formulario de edición.

### Donde Entrar

- Grupo Form (frontend/usuarios/controller/grupo_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Abrir un grupo para editarlo

1. En **grupos**, pulsar el grupo.
2. Se carga su nombre en el formulario.
3. Si el grupo ya no existe aparece «Grupo no encontrado».

### Errores O Avisos Frecuentes

- `Grupo no encontrado`

### Referencias Internas

- Flujo: `usuarios.grupo_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/grupo_info.md`

## Ver los contactos de la región

### Para Que Sirve

Mostrar los correos de contacto de los administradores de la región para pedir ayuda de acceso.

### Donde Entrar

- Mails Contactos Region (frontend/usuarios/controller/mails_contactos_region.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver los contactos de la región

1. Abrir la ayuda de acceso.
2. Consultar la lista de contactos y escribir al que corresponda.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.mails_contactos_region.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/mails_contactos_region.md`

## Añadir un permiso en actividades

### Para Que Sirve

Dar o quitar a un usuario permisos sobre un tipo de actividad (ver, modificar, crear, borrar) según la fase en que esté la actividad.

### Donde Entrar

- Perm Activ Lista (frontend/usuarios/controller/perm_activ_lista.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Añadir un permiso en actividades

1. En la ficha del usuario, en «Permisos en actividades», pulsar **añadir permiso**. Se abre «Añadir nuevo permiso a …».
2. Elegir si es para actividades que organiza **la propia dl** u **otras dl**, y el tipo de actividad con **sf/sv**, **asistentes**, **actividad** y **tipo de actividad** (cuanto más concreto, más manda: «sv n crt» manda sobre «sv n»).
3. Marcar en **afecta a** lo que se controla: datos, economic, sacd, ctr, id_tarifa, cargos, asistentes o asistentesSacd.
4. Para cada uno elegir la **fase de referencia** y el **permiso off (sin marcar)** y **permiso on (marcado)**: nada, ocupado, ver, modificar, crear o borrar. «Ocupado» es un punto intermedio entre nada y ver: se ve que la actividad está ocupada, pero no se puede ver la información de qué es lo que ocupa. Si falta la fase de referencia, avisa «OJO! Comprobar la fase de referencia.» (el aviso es intencionado y puede salir más de una vez al cambiar los desplegables).
5. Pulsar **guardar**. La ficha vuelve a mostrar la tabla con dl propia, tipo de actividad, afecta a, fase de referencia, permiso off y permiso on.

#### Modificar o eliminar un permiso en actividades

1. En la tabla «propios», marcar la fila.
2. Pulsar **modificar** para cambiarla, o **eliminar** y confirmar «¿Esta seguro que desea borrar este permiso?».
3. A igual nivel, los permisos propios mandan sobre los de los grupos del usuario.

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.perm_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/perm_activ.md`

## Añadir un permiso de menú

### Para Que Sirve

Ver, añadir y quitar los permisos sobre menús de un usuario desde su ficha.

### Donde Entrar

- Perm Menu Form (frontend/usuarios/controller/perm_menu_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Añadir un permiso de menú

1. Abrir la ficha del usuario.
2. Pulsar **añadir grup menu** y elegir el grupo de menús.
3. Guardar.

#### Quitar un permiso de menú

1. Elegir la fila en la tabla de la ficha y pulsar quitar.
2. Confirmar «¿Esta seguro que desea borrar este permiso?».

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.perm_menu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/perm_menu.md`

## Abrir el formulario de permiso de menú

### Para Que Sirve

Cargar el formulario (nuevo o de edición) de un permiso de menú.

### Donde Entrar

- Perm Menu Form (frontend/usuarios/controller/perm_menu_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Abrir el formulario de permiso de menú

1. En la ficha del usuario, pulsar añadir (o la fila a editar).
2. Se abre el formulario con el grupo de menús elegible.
3. Si el grupo ya no existe aparece «Grupo no encontrado».

### Errores O Avisos Frecuentes

- `Grupo no encontrado`

### Referencias Internas

- Flujo: `usuarios.perm_menu_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/perm_menu_info.md`

## Reutilizar la presentación de una tabla

### Para Que Sirve

Recordar cómo prefiere el usuario ver cada tabla (anchos, orden) para presentarla igual la próxima vez.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias

### Tareas Habituales

#### Reutilizar la presentación de una tabla

1. Abrir cualquier listado en formato SlickGrid.
2. Ajustar columnas si se desea; se guardan para las siguientes veces.
3. La opción global se elige en **preferencias**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.preferencia_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/preferencia_tabla.md`

## Cambiar las preferencias personales

### Para Que Sirve

Cambiar las preferencias personales: diseño del menú, página de inicio, estilo, presentación de tablas, nombres e idioma.

### Donde Entrar

- Preferencias (frontend/usuarios/controller/preferencias.php)
- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias

### Tareas Habituales

#### Cambiar las preferencias personales

1. Abrir **preferencias** en el menú de usuario.
2. En **Preferencias personales** elegir: Layout (Legacy, Burger, Pills, Pills 2), Página central de inicio (Home, Oficina, Avisos cambios actividades), Menú principal de la oficina, Estilo, Disposición menú, Presentación tablas (SlickGrid o Html), Presentación nombre/apellidos, Idioma y Zona horaria.
3. Pulsar guardar. Algunos cambios (layout, idioma) exigen volver a entrar: la pantalla avisa «deberá volver a entrar».

#### Gestionar los avisos de cambios en actividades

1. En la parte superior de preferencias, usar **ver lista**, **ver mails sin enviar** o **gestionar avisos en cambios actividades**.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.preferencias.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/preferencias.md`

## Reiniciar el 2FA por correo

### Para Que Sirve

Recuperar el acceso cuando se ha perdido la aplicación de autenticación: envía un enlace por correo para reiniciar el 2FA.

### Donde Entrar

- Recuperar 2fa (frontend/usuarios/controller/recuperar_2fa.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Reiniciar el 2FA por correo

1. En la pantalla de acceso, pulsar el enlace de ayuda del 2FA.
2. Indicar el login y el esquema.
3. Se envía un correo con un enlace; al usarlo se desactiva el 2FA y hay que configurarlo de nuevo.
4. Si el usuario no tiene correo registrado aparece «No hay email asociado a este usuario»; hay que pedir al administrador que lo restablezca.

### Errores O Avisos Frecuentes

- `Esquema no válido`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `No se encontró ningún usuario con ese nombre`

### Referencias Internas

- Flujo: `usuarios.recuperar_2fa_mail.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/recuperar_2fa_mail.md`

## Recuperar la contraseña por correo

### Para Que Sirve

Recuperar el acceso cuando se ha olvidado la contraseña: envía por correo una contraseña temporal.

### Donde Entrar

- Recuperar Password (frontend/usuarios/controller/recuperar_password.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Recuperar la contraseña por correo

1. En la pantalla de acceso, pulsar el enlace de ayuda para recuperar la contraseña.
2. Indicar el login.
3. El sistema envía al correo del usuario una contraseña temporal y marca que debe cambiarla al entrar.
4. Con la temporal, iniciar sesión y elegir una contraseña nueva.

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

## Consultar los roles

### Para Que Sirve

Consultar los roles y asignar a cada uno los grupos de menús de la delegación.

### Donde Entrar

- Role Lista (frontend/usuarios/controller/role_lista.php)
- **Legacy:** sistema > usuarios web > lista de roles
- **Pills2:** ADMIN LOCAL > usuarios web > lista de roles

### Tareas Habituales

#### Consultar los roles

1. Entrar en **lista de roles**.
2. Los roles son comunes a todas las delegaciones y solo los modifica el superusuario.
3. Pulsar un rol para ver su ficha y los grupos de menús que tiene.

#### Asignar grupos de menús a un rol

1. Abrir la ficha del rol.
2. Pulsar **añadir grup menu** y marcar los grupos de menús que se quieren dar.
3. Guardar. Los grupos de menús son propios de cada delegación (sv y sf distintos) y los puede modificar el administrador.

#### Quitar un grupo de menús a un rol

1. En la ficha del rol, en la tabla de grupos de menús, elegir el que se quiere quitar y confirmar.

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.role.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role.md`

## Añadir grupos de menús a un rol

### Para Que Sirve

Añadir a un rol los grupos de menús marcados.

### Donde Entrar

- Role Grupmenu (frontend/usuarios/controller/role_grupmenu.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Añadir grupos de menús a un rol

1. Pulsar **añadir grup menu** en la ficha del rol.
2. Marcar uno o varios grupos de la lista.
3. Guardar. Si no se marca ninguno aparece «debe seleccionar uno».

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `debe seleccionar uno`

### Referencias Internas

- Flujo: `usuarios.role_grupmenu_add.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_grupmenu_add.md`

## Quitar un grupo de menús

### Para Que Sirve

Quitar un grupo de menús de un rol.

### Donde Entrar

- Role Form (frontend/usuarios/controller/role_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Quitar un grupo de menús

1. En la ficha del rol, elegir el grupo en la tabla.
2. Pulsar quitar y confirmar.
3. Si el registro ya no existe aparece «no existe el registro».

### Errores O Avisos Frecuentes

- `no existe el registro`
- `hay un error, no se ha eliminado`
- `debe seleccionar uno`

### Referencias Internas

- Flujo: `usuarios.role_grupmenu_del.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_grupmenu_del.md`

## Ver los grupos disponibles

### Para Que Sirve

Mostrar los grupos de menús que se pueden añadir a un rol.

### Donde Entrar

- Role Grupmenu (frontend/usuarios/controller/role_grupmenu.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver los grupos disponibles

1. En la ficha del rol, pulsar **añadir grup menu**.
2. Aparece la lista de grupos de menús que el rol aún no tiene.

### Errores O Avisos Frecuentes

- `Rol no encontrado`

### Referencias Internas

- Flujo: `usuarios.role_grupmenu_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_grupmenu_info.md`

## Abrir la ficha de un rol

### Para Que Sirve

Cargar la ficha de un rol: sus datos, si se puede editar y los grupos de menús que ya tiene.

### Donde Entrar

- Role Form (frontend/usuarios/controller/role_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Abrir la ficha de un rol

1. En **lista de roles**, pulsar el rol.
2. Se muestran sus datos (sf, sv, pau, sacd, casas, dmz) y la tabla de grupos de menús asignados.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `Rol no encontrado`

### Referencias Internas

- Flujo: `usuarios.role_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/role_info.md`

## Buscar un usuario

### Para Que Sirve

Dar de alta, consultar, modificar y eliminar usuarios web de la delegación, y asignarles grupos de permisos y permisos propios.

### Donde Entrar

- Usuario Lista (frontend/usuarios/controller/usuario_lista.php)
- **Legacy:** sistema > usuarios web > lista usuarios
- **Pills2:** ADMIN LOCAL > usuarios web > lista usuarios

### Tareas Habituales

#### Buscar un usuario

1. Entrar en **sistema > usuarios web > lista usuarios**.
2. Escribir parte del nombre en **Nombre** y pulsar **Buscar**, o usar **Buscar en todas las columnas** sobre la tabla.
3. Pulsar el login del usuario para abrir su ficha.

#### Crear un usuario

1. En la lista, pulsar **NUEVO USUARIO**.
2. Rellenar **Login** y elegir el **Role** (admin, casa, centro sv, director, oficial_dl, p-sacd, supervisor).
3. Escribir un **Password** (debe cumplir los requisitos de seguridad; si se deja vacío en un usuario existente no se cambia).
4. Marcar **Cambio password** si se quiere obligar a cambiarlo al primer acceso, y **¿tiene doble factor activado?** si procede.
5. Escribir el **Email** y pulsar **guardar datos usuario**.
6. Al guardar aparece la ficha completa con grupos y permisos.

#### Modificar un usuario

1. Abrir la ficha desde la lista.
2. Cambiar login, nombre a mostrar, role, email o las casillas, y pulsar **guardar datos usuario**.
3. Para cambiar la contraseña, escribir la nueva en **Password** (se comprueba su fortaleza antes de guardar).

#### Asignar grupos de permisos

1. En la ficha, apartado **grupos**, pulsar **añadir un grupo de permisos** y, en la tabla, **añadir** en la fila del grupo (se asigna al instante).
2. Para retirarlo, pulsar **quitar de un grupo de permisos** y **quitar** en la fila del grupo.
3. Aviso de la pantalla: si el usuario tiene varios grupos, sus permisos deberían ser independientes porque no hay preferencia entre ellos.

#### Eliminar un usuario

1. En la lista, marcar el usuario y pulsar el botón de eliminar.
2. Confirmar el mensaje «¿Está seguro que desea borrar este usuario?».

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.usuario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario.md`

## Activar la verificación en dos pasos

### Para Que Sirve

Activar o desactivar la verificación en dos pasos (2FA) de un usuario.

### Donde Entrar

- Usuario Form 2fa (frontend/usuarios/controller/usuario_form_2fa.php)
- Usuario Reset 2fa (frontend/usuarios/controller/usuario_reset_2fa.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Activar la verificación en dos pasos

1. Descargar una aplicación de autenticación (Google Authenticator, Microsoft Authenticator o Authy).
2. Escanear el **Código QR** o escribir la **Clave secreta**.
3. Escribir el **Código de verificación** que muestra la aplicación y marcar **Activar autenticación de dos factores**.
4. Guardar. Desde ese momento, en cada acceso se pide contraseña y código.
5. Si falta el código aparece «Se requiere un código de verificación para activar 2FA».

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `Se requiere un código de verificación para activar 2FA`
- `Código de verificación inválido`
- `Hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_2fa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_2fa.md`

## Ver el estado y el QR

### Para Que Sirve

Mostrar el estado de la verificación en dos pasos del usuario y los datos para configurarla.

### Donde Entrar

- Usuario Form 2fa (frontend/usuarios/controller/usuario_form_2fa.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver el estado y el QR

1. Abrir la configuración de **autenticación de dos factores (2FA)**.
2. Se muestra si está activada y, si no, el **Código QR** y la **Clave secreta** para configurarla.

### Errores O Avisos Frecuentes

- `Id de usuario no válido`
- `Usuario no encontrado`

### Referencias Internas

- Flujo: `usuarios.usuario_2fa_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_2fa_info.md`

## Verificar el código

### Para Que Sirve

Comprobar el código de la aplicación de autenticación antes de activar la verificación en dos pasos.

### Donde Entrar

- Usuario Form 2fa (frontend/usuarios/controller/usuario_form_2fa.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Verificar el código

1. Escanear el QR con la aplicación.
2. Escribir en **Código de verificación** el número que muestra.
3. Si es correcto se permite activar 2FA; si no, aparece «Código de verificación inválido».

### Errores O Avisos Frecuentes

- `Código de verificación o clave secreta no válidos`
- `Código de verificación inválido`

### Referencias Internas

- Flujo: `usuarios.usuario_2fa_verify.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_2fa_verify.md`

## Consultar el correo asociado

### Para Que Sirve

En la pantalla de ayuda de acceso, mostrar el correo (ofuscado) asociado al login y el contacto del administrador regional.

### Donde Entrar

- Ayuda 2fa Reset (frontend/usuarios/controller/ayuda_2fa_reset.php)
- Ayuda Acceso (frontend/usuarios/controller/ayuda_acceso.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar el correo asociado

1. Abrir la ayuda de acceso.
2. Escribir el login válido.
3. Se muestra el correo parcialmente oculto al que llegarán los avisos y el contacto del administrador.
4. Si el login no es válido aparece «Debe ingresar un nombre de usuario válido».

### Errores O Avisos Frecuentes

- `Esquema no válido`
- `Debe ingresar un nombre de usuario válido`
- `No hay email asociado a este usuario`

### Referencias Internas

- Flujo: `usuarios.usuario_ayuda_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_ayuda_info.md`

## Validar una contraseña al escribirla

### Para Que Sirve

Comprobar, antes de guardar, que la contraseña escrita cumple los requisitos.

### Donde Entrar

- Usuario Form (frontend/usuarios/controller/usuario_form.php)
- Usuario Form Pwd (frontend/usuarios/controller/usuario_form_pwd.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Validar una contraseña al escribirla

1. Escribir la contraseña en el formulario.
2. Al pulsar guardar se comprueba automáticamente.
3. Si no cumple, sale un aviso «password error» con el motivo y no se guarda. La regla: más de 8 caracteres e incluir al menos una letra mayúscula, un número y un carácter especial. Ejemplos: «abc» falla por corta; «abcdefgh1» falla por no tener mayúscula ni carácter especial.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.usuario_check_pwd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_check_pwd.md`

## Asignar un grupo a un usuario

### Para Que Sirve

Asignar un grupo de permisos a un usuario.

### Donde Entrar

- Usuario Form (frontend/usuarios/controller/usuario_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Asignar un grupo a un usuario

1. Abrir la ficha del usuario y pulsar **añadir un grupo de permisos**.
2. En la tabla pulsar **añadir** en la fila del grupo.
3. La lista de grupos de la ficha se actualiza.
4. Si la operación caduca o no está permitida aparece «Operación no autorizada»; hay que volver a abrir la ficha.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_add.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_add.md`

## Quitar un grupo a un usuario

### Para Que Sirve

Quitar un grupo de permisos a un usuario.

### Donde Entrar

- Usuario Form (frontend/usuarios/controller/usuario_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Quitar un grupo a un usuario

1. Abrir la ficha del usuario y pulsar **quitar de un grupo de permisos**.
2. En la tabla pulsar **quitar** en la fila del grupo (sin confirmación).
3. La lista de grupos se actualiza.
4. Si aparece «Operación no autorizada», volver a abrir la ficha y repetir.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_del.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_del.md`

## Ver los grupos que se pueden quitar

### Para Que Sirve

Mostrar los grupos que el usuario ya tiene, para poder quitarle alguno.

### Donde Entrar

- Usuario Grupo Del Lst (frontend/usuarios/controller/usuario_grupo_del_lst.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver los grupos que se pueden quitar

1. En la ficha del usuario, pulsar **quitar de un grupo de permisos**.
2. Aparece la lista de grupos asignados.
3. En la tabla pulsar **quitar** en la fila del grupo: se retira al instante, sin pedir confirmación (ver «Quitar un grupo a un usuario»).

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_del_lst.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_del_lst.md`

## Elegir un grupo para añadir

### Para Que Sirve

Mostrar los grupos de permisos que se pueden añadir a un usuario (los que aún no tiene).

### Donde Entrar

- Usuario Grupo Lst (frontend/usuarios/controller/usuario_grupo_lst.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir un grupo para añadir

1. En la ficha del usuario, pulsar **añadir un grupo de permisos**.
2. Aparece la lista de grupos disponibles, sin los que el usuario ya tiene.
3. En la tabla (columnas **grupo**, **sección**, **acción**) pulsar **añadir** en la fila del grupo: se asigna al instante (ver «Asignar un grupo a un usuario»).

### Errores O Avisos Frecuentes

- `Usuario no encontrado`

### Referencias Internas

- Flujo: `usuarios.usuario_grupo_lst.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_grupo_lst.md`

## Cambiar el correo

### Para Que Sirve

Cambiar el correo electrónico de un usuario.

### Donde Entrar

- Usuario Form Mail (frontend/usuarios/controller/usuario_form_mail.php)
- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias

### Tareas Habituales

#### Cambiar el correo

1. Abrir la ficha del usuario (o las preferencias personales).
2. Escribir el nuevo correo en **Email** y pulsar **guardar**.
3. El correo se usa para la recuperación de contraseña y de la verificación en dos pasos.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_guardar_mail.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_guardar_mail.md`

## Cambiar la contraseña

### Para Que Sirve

Cambiar la contraseña de un usuario cumpliendo los requisitos de seguridad.

### Donde Entrar

- Usuario Form Pwd (frontend/usuarios/controller/usuario_form_pwd.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Cambiar la contraseña

1. Escribir la nueva contraseña en **password** y repetirla en **confirma password**.
2. La contraseña debe tener más de 8 caracteres e incluir al menos una mayúscula, un número y un carácter especial (no se admiten comillas dobles).
3. Pulsar **guardar**. Si no coinciden o no es válida aparecen «error: passwords no coincidentes» o «error: password no válido».
4. Al guardar bien se muestra «paswword guardado» y se quita la obligación de cambiarla.

### Errores O Avisos Frecuentes

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `usuarios.usuario_guardar_pwd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_guardar_pwd.md`

## Ver el resumen de un usuario en su ficha

### Para Que Sirve

Calcular el resumen del usuario (grupos, login, correo) que se muestra en la cabecera de su ficha.

### Donde Entrar

- Usuario Form (frontend/usuarios/controller/usuario_form.php)
- Usuario Form 2fa (frontend/usuarios/controller/usuario_form_2fa.php)
- Usuario Form Mail (frontend/usuarios/controller/usuario_form_mail.php)
- Usuario Form Pwd (frontend/usuarios/controller/usuario_form_pwd.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver el resumen de un usuario en su ficha

1. Abrir la ficha de un usuario desde **lista usuarios**.
2. La cabecera muestra el login, el correo y los grupos asignados; no hay que hacer nada más.
3. Si el usuario no existe aparece «Usuario no encontrado».

### Errores O Avisos Frecuentes

- `Id de usuario no válido`
- `Usuario no encontrado`

### Referencias Internas

- Flujo: `usuarios.usuario_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_info.md`

## Abrir las preferencias

### Para Que Sirve

Cargar los valores actuales de las preferencias personales al abrir la pantalla.

### Donde Entrar

- Preferencias (frontend/usuarios/controller/preferencias.php)
- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias

### Tareas Habituales

#### Abrir las preferencias

1. Abrir **preferencias** en el menú de usuario.
2. La pantalla muestra los valores que ya tiene guardados el usuario.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `usuarios.usuario_preferencias.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/usuarios/flujos/usuario_preferencias.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/usuarios/flujos/`.
