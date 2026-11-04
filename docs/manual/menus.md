---
tipo: "manual_usuario"
modulo: "menus"
flujos: 15
estado_revision: "revisado"
---

# Manual De Usuario - menus

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Grupmenu

### Para Que Sirve

Alta/edición/baja de grupos de menú.

### Donde Entrar

- Grupmenu Form (frontend/menus/controller/grupmenu_form.php)
- Grupmenu Lista (frontend/menus/controller/grupmenu_lista.php)
- Menus Get (frontend/menus/controller/menus_get.php)
- Menus Que (frontend/menus/controller/menus_que.php)
- Entrada de menú (TablaDB, no `grupmenu_lista.php`):
- **Legacy:** sistema > usuarios web > grup menu
- **Pills2:** sistema > usuarios web > grup menu · ADMIN LOCAL > usuarios web > grup menu
- Pantallas `grupmenu_*`: sin entrada de menú en el índice.

### Tareas Habituales

#### Via pantallas Orbix

1. En la lista de grupos de menú, pulsar **nuevo** o seleccionar uno y pulsar **modificar**: se abre el formulario.
2. En un alta el formulario sale vacío; al modificar sale con los datos del grupo. Pulsar **guardar**.
3. Para eliminar un grupo, seleccionarlo y pulsar **eliminar**.

### Errores O Avisos Frecuentes

- `No encuentro el grupmenu`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `debe poner un nombre`

### Permisos

- Sin control propio; autorización vía menú de administración (`frontend/menus/controller/grupmenu_lista.php`).
- Menú administración usuarios web / grupmenu.
- Menú administración.
- Menú administración grupmenu.

### Referencias Internas

- Flujo: `menus.grupmenu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/grupmenu.md`

## Ver mi menú lateral

### Para Que Sirve

Cargar los grupos e ítems de menú que puede ver el usuario en el menú lateral.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver mi menú lateral

1. Iniciar sesión.
2. El menú lateral muestra solo los grupos y opciones autorizados para el rol del usuario.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Filtrado por `aux_grupmenu_rol` del rol de sesión y `PermisoMenu::visible()` en cada ítem.

### Referencias Internas

- Flujo: `menus.grupmenu_coleccion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/grupmenu_coleccion.md`

## Editar un grupo de menús

### Para Que Sirve

Cargar el formulario de edición de un grupo de menús.

### Donde Entrar

- Grupmenu Form (frontend/menus/controller/grupmenu_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Editar un grupo de menús

1. En el gestor de menús, abrir el grupo.
2. Cambiar su nombre y guardar.

### Errores O Avisos Frecuentes

- `No encuentro el grupmenu`

### Permisos

- Menú administración grupmenu.

### Referencias Internas

- Flujo: `menus.grupmenu_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/grupmenu_info.md`

## Elegir el destino de un menú

### Para Que Sirve

Ofrecer los destinos posibles (módulo o URL) al editar una entrada de menú.

### Donde Entrar

- Menus Get (frontend/menus/controller/menus_get.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir el destino de un menú

1. En la ficha de la entrada, abrir el desplegable de destino.
2. Elegir la pantalla o módulo al que debe llevar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.lista_meta_menus.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/lista_meta_menus.md`

## Ver las plantillas de menú

### Para Que Sirve

Mostrar las plantillas de menú disponibles para importar.

### Donde Entrar

- Menus Importar Form (frontend/menus/controller/menus_importar_form.php)
- **Legacy:** sistema > menus > importar
- **Pills2:** sistema > menus > importar · ADMIN LOCAL > Importar menús · ADMIN GLOBAL > menus > importar

### Tareas Habituales

#### Ver las plantillas de menú

1. Entrar en **importar**.
2. Aparece la lista de plantillas de referencia; elegir una.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.lista_templates.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/lista_templates.md`

## Modificar una entrada de menú

### Para Que Sirve

Ver, cambiar, copiar, mover, crear o borrar entradas de los menús de un grupo.

### Donde Entrar

- Menus Get (frontend/menus/controller/menus_get.php)
- **Legacy:** sistema > menus > seleccionar
- **Pills2:** sistema > menus > seleccionar · ADMIN GLOBAL > menus > seleccionar

### Tareas Habituales

#### Modificar una entrada de menú

1. Entrar en **seleccionar** y elegir el **grupo** (vsm, scdl, dre, sistema…). Se muestra el árbol de entradas con su orden (por ejemplo 10,50,40), el nombre, «[ok]» y los roles que lo ven entre paréntesis.
2. Pulsar una entrada para editarla: **orden**, **es ok?**, **texto menu**, **meta** (la pantalla a la que lleva), **parametros** y los **permisos** (una casilla por rol; **todos** y **ninguno** marcan o desmarcan todas).
3. Pulsar **guardar**, o **cancelar** para salir.
4. Con **eliminar** se borra la entrada; con **nuevo** se crea otra en el grupo elegido (si no hay grupo avisa «debe seleccionar un grupo de menu para poder añadir un menu»); **mover a** y **copiar en** la pasan o copian a otro grupo.

### Errores O Avisos Frecuentes

- `hay un error. Debe indicar el destino`
- `No encuentro el menu`
- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

### Permisos

- Gestor de menús (`menus_que`); bits `perm_menu` en el propio ítem.

### Referencias Internas

- Flujo: `menus.menu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menu.md`

## Mover una entrada a otro grupo

### Para Que Sirve

Pasar una entrada de menú a otro grupo.

### Donde Entrar

- Menus Get (frontend/menus/controller/menus_get.php)
- **Legacy:** sistema > menus > seleccionar
- **Pills2:** sistema > menus > seleccionar · ADMIN GLOBAL > menus > seleccionar
- (Acción de ficha del gestor; no tiene menú propio.)

### Tareas Habituales

#### Mover una entrada a otro grupo

1. Abrir la entrada en el gestor de menús.
2. Elegir el grupo de destino en el desplegable junto a **mover a**.
3. Pulsar **mover a**.

### Errores O Avisos Frecuentes

- `hay un error. Debe indicar el destino`
- `No encuentro el menu`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `menus.menu_mover.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menu_mover.md`

## Ver el menú Burger o Pills

### Para Que Sirve

Cargar el árbol de menús de los diseños modernos (Burger, Pills, Pills 2).

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver el menú Burger o Pills

1. Elegir el layout en **preferencias**.
2. Al entrar, el menú aparece anidado según el rol del usuario.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.menus_burger_layout.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_burger_layout.md`

## Exportar los menús

### Para Que Sirve

Guardar el menú actual como plantilla de referencia en la base pública.

### Donde Entrar

- Menus Exportar Form (frontend/menus/controller/menus_exportar_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ADMIN LOCAL > Exportar menús · sistema > menus > Exportar · ADMIN GLOBAL > menus > Exportar

### Tareas Habituales

#### Exportar los menús

1. Entrar en **Exportar** (menú **sistema > menus > Exportar**). Se abre un formulario con el campo **Guardar como** y el botón **guardar**.
2. Escribir el nombre de la plantilla y pulsar **guardar**: el menú local pasa a ser la plantilla de referencia que otras delegaciones pueden importar.
3. Si ya existe una plantilla con ese nombre, la pantalla pregunta si se quiere reemplazar.

### Errores O Avisos Frecuentes

- `ya existe`

### Referencias Internas

- Flujo: `menus.menus_exportar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_exportar.md`

## Menús a/desde ficheros SQL

### Para Que Sirve

Respaldar o restaurar la referencia de menús vía ficheros SQL en disco.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sistema > menus > importar desde ficheros
- **Pills2:** sistema > menus > importar desde ficheros
- (aviso: URL en BD = `menus_ficheros.php` muerto → usar `/src/menus/menus_exportar_ref_a_ficheros`)

### Tareas Habituales

#### Exportar a ficheros

1. Lanzar la acción de exportar: se escriben ficheros SQL con los menús en la carpeta `log/menus/` del servidor (debe tener permiso de escritura).

#### Importar desde ficheros

1. Lanzar la acción de importar: se cargan en la base de datos los menús de los ficheros SQL; el resultado queda anotado en `log/menus/menus.log`.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú «importar desde ficheros» (operación de servidor).

### Referencias Internas

- Flujo: `menus.menus_exportar_ref_a_ficheros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_exportar_ref_a_ficheros.md`

## Regenerar los textos de los menús

### Para Que Sirve

Regenerar el fichero de textos traducibles de las etiquetas de menú.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sistema > traducciones > menus a texto
- **Pills2:** sistema > traducciones > menus a texto

### Tareas Habituales

#### Regenerar los textos de los menús

1. Ejecutar la opción de generar textos.
2. Se actualiza el fichero que se usa para traducir los nombres de los menús.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú `sistema > traducciones > menus a texto` (`_referencia_menus.md`).

### Referencias Internas

- Flujo: `menus.menus_generar_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_generar_txt.md`

## Ver el listado y la ficha de un menú

### Para Que Sirve

Mostrar, dentro del gestor de menús, el listado de un grupo o la ficha de una entrada.

### Donde Entrar

- Menus Get (frontend/menus/controller/menus_get.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver el listado y la ficha de un menú

1. En el gestor de menús, elegir el grupo.
2. El listado se carga en la misma pantalla; pulsar una entrada para abrir su ficha.

### Errores O Avisos Frecuentes

- `No encuentro el menu`

### Referencias Internas

- Flujo: `menus.menus_get_page.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_get_page.md`

## Importar una plantilla de menús

### Para Que Sirve

Sustituir los menús locales por los de una plantilla de referencia.

### Donde Entrar

- Menus Importar Form (frontend/menus/controller/menus_importar_form.php)
- **Legacy:** sistema > menus > importar
- **Pills2:** sistema > menus > importar · ADMIN LOCAL > Importar menús · ADMIN GLOBAL > menus > importar

### Tareas Habituales

#### Importar una plantilla de menús

1. Entrar en **importar**: «Escoger menus de referencia».
2. Elegir la plantilla en el desplegable (por ejemplo Aes, Ceb, Aut-sep-2025…).
3. Pulsar **importar**. Los menús locales se sustituyen por los de la plantilla y se pierden los cambios locales.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.menus_importar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_importar.md`

## Restaurar menús ref→DL

### Para Que Sirve

Dejar los menús del esquema (o de todas las DL si dlb) como la referencia por defecto.

### Donde Entrar

- Restaurar menús ref→DL (frontend/menus/controller/menus_importar_de_ficheros_a_ref.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Confirmar (`seguro=2`)

1. Abrir la pantalla: salen los mensajes de advertencia y el enlace **continuar**.
2. Si la delegación es dlb, aparece además el enlace **Poner todas las dl igual**.

#### Ejecutar (`seguro=1`)

1. El proceso recorre las delegaciones (salta la `H-Hv`).
2. Copia los menús de referencia a los de cada delegación; en la sf no toca los grupos de menú por rol.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú `sistema > menus > importar desde ficheros`.

### Referencias Internas

- Flujo: `menus.menus_importar_de_ficheros_a_ref.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_importar_de_ficheros_a_ref.md`

## Ver el menú Legacy

### Para Que Sirve

Cargar los ítems visibles del menú del diseño Legacy.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver el menú Legacy

1. Iniciar sesión con layout Legacy (se elige en **preferencias**).
2. La barra lateral muestra los grupos permitidos para el usuario.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.menus_legacy_layout_items.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_legacy_layout_items.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/menus/flujos/`.
