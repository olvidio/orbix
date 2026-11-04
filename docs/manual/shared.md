---
tipo: "manual_usuario"
modulo: "shared"
flujos: 6
estado_revision: "revisado_parcial"
---

# Manual De Usuario - shared

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Elegir un idioma en un desplegable

### Para Que Sirve

Cargar la lista de idiomas disponibles para un desplegable, en las pantallas que lo necesitan (certificados y preferencias de usuario).

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- sin entrada de menú en el índice (endpoint embebido; las pantallas host sí tienen menú propio en
- usuarios/certificados).

### Tareas Habituales

#### Elegir un idioma en un desplegable

1. Abrir las preferencias de usuario o un certificado emitido.
2. El desplegable de idioma se rellena con los idiomas activos.
3. Elegir el idioma que corresponda.

### Errores O Avisos Frecuentes

- `Ninguno documentado.`

### Permisos

- Sin control en el endpoint; la pantalla que lo invoca aplica sus permisos.

### Referencias Internas

- Flujo: `shared.locales_posibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/locales_posibles.md`

## Guardar o eliminar un registro de una tabla

### Para Que Sirve

Dar de alta, modificar o eliminar un registro de cualquier tabla mantenida con la pantalla genérica de mantenimiento.

### Donde Entrar

- Mantenimiento genérico de tablas (formulario) (frontend/shared/controller/tablaDB_formulario_ver.php)
- sin entrada directa; mutación desde listado/formulario cuya ruta depende de `clase_info`.

### Tareas Habituales

#### Guardar o eliminar un registro de una tabla

1. En el formulario, pulsar guardar: se graba el registro nuevo o los cambios del existente.
2. En la tabla, seleccionar una o varias filas y pulsar **eliminar** para borrarlas.
3. Algunas tablas tienen reglas propias; por ejemplo, al activar un módulo se crean sus tablas.

### Errores O Avisos Frecuentes

- `no se ha ejecutado la acción`
- `Errores de repositorio y validación de módulos (ver API tablaDB_update).`

### Permisos

- Sin `perm_*` en el controller; el nivel de edición lo fija el frontend (`permiso`, botón nuevo solo

### Referencias Internas

- Flujo: `shared.tablaDB.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB.md`

## Buscar antes de listar

### Para Que Sirve

Filtrar los registros antes de ver la tabla, en los mantenimientos que piden criterios de búsqueda.

### Donde Entrar

- Mantenimiento genérico de tablas (listado) (frontend/shared/controller/tablaDB_lista_ver.php)
- Misma entrada que el listado destino (variante por `clase_info` en `_referencia_menus.md`).

### Tareas Habituales

#### Buscar antes de listar

1. Abrir el mantenimiento desde el menú: aparece primero el formulario de criterios.
2. Rellenar los criterios que interesen.
3. Pulsar buscar: se muestra la tabla con los registros que cumplen.

### Errores O Avisos Frecuentes

- `Ninguno documentado en el builder.`

### Permisos

- Sin control en el endpoint.

### Referencias Internas

- Flujo: `shared.tablaDB_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_buscar.md`

## Cambiar un campo que condiciona otro

### Para Que Sirve

Actualizar las opciones de un campo cuando cambia el valor de otro del que depende en un formulario de mantenimiento (por ejemplo, al cambiar el centro se actualizan los lugares en inventario).

### Donde Entrar

- Mantenimiento genérico de tablas (formulario) (frontend/shared/controller/tablaDB_formulario_ver.php)
- sin entrada de menú en el índice (subflujo del formulario).

### Tareas Habituales

#### Cambiar un campo que condiciona otro

1. En el formulario, cambiar el desplegable principal (por ejemplo el centro).
2. El desplegable dependiente (por ejemplo el lugar) se rellena con las opciones válidas para ese valor.

### Errores O Avisos Frecuentes

- `Error AJAX mostrado en alert con json.mensaje.`

### Permisos

- Sin control en el endpoint.

### Referencias Internas

- Flujo: `shared.tablaDB_depende.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_depende.md`

## Crear o modificar un registro

### Para Que Sirve

Crear o modificar un registro en un mantenimiento genérico de tablas.

### Donde Entrar

- Mantenimiento genérico de tablas (formulario) (frontend/shared/controller/tablaDB_formulario_ver.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Crear o modificar un registro

1. En la tabla, pulsar **nuevo**, o seleccionar una fila y pulsar **modificar**.
2. Aparece el formulario con los campos del registro; en una modificación salen con sus valores.
3. Rellenar los campos; las fechas se comprueban antes de enviar.
4. Pulsar guardar: se vuelve a la tabla.

### Errores O Avisos Frecuentes

- `Mensajes de tablaDB_update en alert vía json.mensaje.`

### Permisos

- Sin control en el endpoint; edición condicionada en vista por `permiso` del listado.
- Sin `perm_*` en el controller; el nivel de edición lo fija el frontend (`permiso`, botón nuevo solo

### Referencias Internas

- Flujo: `shared.tablaDB_formulario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_formulario.md`

## Buscar y listar los registros

### Para Que Sirve

Consultar y mantener los registros de tablas de configuración que se abren desde el menú (asignaturas, ubicaciones, inventario, procesos…) con la misma pantalla genérica.

### Donde Entrar

- Mantenimiento genérico de tablas (listado) (frontend/shared/controller/tablaDB_lista_ver.php)
- Variante según `clase_info` — ver `docs/guias/_referencia_menus.md` (entradas `tablaDB_lista_ver.php`).
- Ejemplos: `global > estudios > asignaturas`, `sistema > menus > meta menus`, `dre > zonas > zonas`.

### Tareas Habituales

#### Buscar y listar los registros

1. Abrir la tabla desde el menú.
2. Si el mantenimiento pide criterios, rellenarlos y pulsar buscar.
3. Aparece la tabla; se puede ordenar por varias columnas.

#### Añadir, modificar o eliminar un registro

1. Pulsar **nuevo** para añadir, o seleccionar una fila y pulsar **modificar** (ver «Crear o modificar un registro»).
2. Para eliminar, seleccionar la fila, pulsar **eliminar** y confirmar.

### Errores O Avisos Frecuentes

- `Errores de tablaDB_update (ver ficha API).`
- `Sin errores propios en builders de lista.`

### Permisos

- Sin control en el endpoint.
- Sin control en el endpoint; permisos y visibilidad del botón «nuevo» en frontend (`permiso`).
- Sin `perm_*` en el controller; el nivel de edición lo fija el frontend (`permiso`, botón nuevo solo

### Referencias Internas

- Flujo: `shared.tablaDB_lista.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_lista.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/shared/flujos/`.
