---
tipo: "manual_usuario"
modulo: "devel_db_admin"
flujos: 15
estado_revision: "generado"
---

# Manual De Usuario - devel_db_admin

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Absorber Esquema

### Para Que Sirve

Unir un esquema DL disuelto en otro esquema matriz.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `Delegación origen no encontrada.`
- `hay un error, no se ha guardado`

### Permisos

- Sin control propio en el caso de uso; acceso vía menú de administración DB (`sistema > DB`).

### Referencias Internas

- Flujo: `devel_db_admin.absorber_esquema.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/absorber_esquema.md`

## Apptables Apps

### Para Que Sirve

Cargar apps y ejecutar operaciones de tablas globales/esquema.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; pantalla restringida al menú de configuración de desarrollo.

### Referencias Internas

- Flujo: `devel_db_admin.apptables_apps.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/apptables_apps.md`

## Copiar Esquema

### Para Que Sirve

Importar datos al nuevo esquema desde referencia (paso 3 nuevo esquema).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `Faltan región o delegación destino para copiar datos.`
- `Esquema de referencia no válido.`

### Permisos

- Sin control propio; invocado desde `db_crear_esquema_que` (menú DB).

### Referencias Internas

- Flujo: `devel_db_admin.copiar_esquema.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/copiar_esquema.md`

## Corregir Renombrar Esquema

### Para Que Sirve

Reparar renombre de esquema a medias.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `No se aplicó ninguna corrección: parámetros inválidos.`

### Permisos

- Sin control propio; fragmento de `db_cambiar_nombre_que`.

### Referencias Internas

- Flujo: `devel_db_admin.corregir_renombrar_esquema.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/corregir_renombrar_esquema.md`

## Crear Esquema

### Para Que Sirve

Crear estructura PostgreSQL de un nuevo esquema DL (paso 2).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `Esquema de referencia no válido.`
- `No se puede crear «%s»: el esquema destino ya existe en alguna base (intento anterior del paso 2 o alta duplicada). Los roles del paso «crear usuarios» no impiden continuar.`
- `No se puede crear: falta el esquema de referencia «%s» en:`
- `Aviso: no se puede crear la estructura de esquemas para «%s». Primero ejecute el paso «1º crear usuarios» (misma región y delegación) y, si hace falta, copie las entradas en los ficheros .inc que indica ese paso.`

### Permisos

- Sin control propio; menú `sistema > DB > nuevo esquema`.

### Referencias Internas

- Flujo: `devel_db_admin.crear_esquema.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/crear_esquema.md`

## Crear Usuarios

### Para Que Sirve

Crear roles PostgreSQL para nuevo esquema (paso 1).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; menú DB desarrollo.

### Referencias Internas

- Flujo: `devel_db_admin.crear_usuarios.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/crear_usuarios.md`

## Db Lugar

### Para Que Sirve

Recargar desplegable de delegación al cambiar región en formularios DB.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; invocado vía `HashFront` al cambiar región en pantallas DB.

### Referencias Internas

- Flujo: `devel_db_admin.db_lugar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/db_lugar.md`

## Db Propiedades

### Para Que Sirve

Cargar desplegables de esquemas/tablas según operación (`op`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `op no válida`

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `devel_db_admin.db_propiedades.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/db_propiedades.md`

## Eliminar Esquema

### Para Que Sirve

Eliminar esquema DL y trasladar datos a resto.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `No se pudo eliminar el esquema «%1$s» en %2$s: %3$s`
- `El rol «%s» ya no existía; no se intentó borrarlo.`
- `Aviso: no se pudo eliminar el rol «%1$s» (los esquemas ya se borraron): %2$s`

### Permisos

- Sin control propio; menú `sistema > DB > eliminar esquema`.

### Referencias Internas

- Flujo: `devel_db_admin.eliminar_esquema.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/eliminar_esquema.md`

## Migraciones

### Para Que Sirve

Revisar y aplicar migraciones SQL del repositorio.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `devel_db_admin.migraciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/migraciones.md`

## Migraciones Ejecutar

### Para Que Sirve

Ejecutar migraciones seleccionadas o hasta prefijo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `No hay migraciones para ejecutar.`
- `No se puede leer %s`
- `Database no soportada: %s`
- `No se han encontrado esquemas activos para %s`
- `Error ejecutando SQL de migracion (%s): %s`
- `La migracion no se aplico en ningun esquema: todos omitidos por esquema inexistente (catalogo PostgreSQL / SQLSTATE 3F000 / 42P01).`

### Permisos

- Sin control propio; menú `sistema > DB > actualizar DB`.

### Referencias Internas

- Flujo: `devel_db_admin.migraciones_ejecutar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/migraciones_ejecutar.md`

## Migraciones Quitar Registro

### Para Que Sirve

Quitar registro de migración aplicada para poder re-ejecutar.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `No hay migraciones seleccionadas.`
- `Ninguna migracion seleccionada tenia registro en migracion_aplicada.`
- `No se elimino ningun registro.`

### Permisos

- Sin control propio; acción desde `migraciones_lista`.

### Referencias Internas

- Flujo: `devel_db_admin.migraciones_quitar_registro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/migraciones_quitar_registro.md`

## Mover Tabla

### Para Que Sirve

Mover tabla de sv a sv-e en todos los esquemas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `Error para %s`
- `error al escribir el fichero`

### Permisos

- Sin control propio; menú `sistema > DB > mover tabla a otra DB`.

### Referencias Internas

- Flujo: `devel_db_admin.mover_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/mover_tabla.md`

## Renombrar Esquema

### Para Que Sirve

Renombrar esquema region-dl (cambiar delegación/región).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- `El esquema «%1$s» no tiene entrada de conexión en «%2$s.inc» (ni con el nombre antiguo ni con el nuevo «%3$s»). El listado de origen sale de PostgreSQL; hace falta la misma clave en el fichero de passwords (p. ej. tras «Crear esquema») para poder renombrar.`

### Permisos

- Sin control propio; menú `sistema > DB > cambiar nombre esquema`.

### Referencias Internas

- Flujo: `devel_db_admin.renombrar_esquema.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/renombrar_esquema.md`

## Verificar Renombrar Esquema

### Para Que Sirve

Comprobar estado del renombre antes/después.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; fragmento de cambiar nombre esquema.

### Referencias Internas

- Flujo: `devel_db_admin.verificar_renombrar_esquema.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/devel_db_admin/flujos/verificar_renombrar_esquema.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
