---
tipo: "manual_usuario"
modulo: "cartaspresentacion"
flujos: 6
estado_revision: "revisado_parcial"
---

# Manual De Usuario - cartaspresentacion

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Dar de alta o modificar la carta de un centro

### Para Que Sirve

Dar de alta, modificar o quitar los datos de presentación de un centro.

### Donde Entrar

- Cartas Presentacion (frontend/cartaspresentacion/controller/cartas_presentacion.php)
- sin entrada de menú en el índice (subflujo de `cartas_presentacion` > modificar).

### Tareas Habituales

#### Dar de alta o modificar la carta de un centro

1. En la lista, pulsar **director** en la fila del centro.
2. Se abre el formulario; si el centro no tiene carta, sale vacío y se crea al guardar. No se pueden modificar datos de otra delegación.
3. Rellenar nombre, teléfono, e-mail, zona y observaciones.
4. Pulsar **Guardar**.

#### Quitar la carta de un centro

1. En una fila que ya tiene carta, pulsar **quitar**.
2. Confirmar el aviso.

### Errores O Avisos Frecuentes

- `Faltan id_ubi o id_direccion`
- `No puede modificar datos de otra dl`
- `Carta de presentacion no encontrada`
- `Hay un error, no se ha guardado. / Hay un error, no se ha borrado.`

### Permisos

- Sin control de permisos propio en el caso de uso; la acción solo se ofrece en el listado para
- Validación en el caso de uso: solo centros de la propia delegación (`ConfigGlobal::mi_delef()`) o
- Al crear: solo centros de la propia dl o `tipo_ctr=cr` (`resolveWriteRepo`). Al actualizar una carta

### Referencias Internas

- Flujo: `cartaspresentacion.carta_presentacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/carta_presentacion.md`

## Ver el listado de cartas

### Para Que Sirve

Consultar todas las cartas de presentación agrupadas por tipo de labor, delegación y población.

### Donde Entrar

- Cartas Presentacion (frontend/cartaspresentacion/controller/cartas_presentacion.php)
- **Legacy:** scdl > direcciones > cartas presentacion > lista dl / lista todo
- **Pills2:** scdl > direcciones > cartas presentacion > lista dl / lista todo

### Tareas Habituales

#### Ver el listado de cartas

1. Abrir **lista dl** o **lista todo**.
2. Aparecen las tablas agrupadas con los datos de contacto.
3. Los centros con el tipo de labor mal configurado salen en un aviso al final de la página.

#### Ver el listado filtrado

1. Abrir **buscar**, rellenar los filtros y pulsar **buscar**.
2. Los resultados aparecen debajo.

### Errores O Avisos Frecuentes

- `Centros con tipo_labor mal configurado aparecen en aviso al pie (html_errores), no como error AJAX.`

### Permisos

- Sin control de permisos propio en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.cartas_presentacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/cartas_presentacion.md`

## Buscar cartas de presentación

### Para Que Sirve

Encontrar cartas de presentación según criterios geográficos o de delegación.

### Donde Entrar

- Cartas Presentacion Buscar (frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php)
- **Legacy:** scdl > direcciones > cartas presentacion > buscar
- **Pills2:** scdl > direcciones > cartas presentacion > buscar

### Tareas Habituales

#### Buscar cartas de presentación

1. Abrir **buscar**: se cargan los desplegables de filtro.
2. Rellenar los filtros y pulsar **buscar**.
3. Los resultados aparecen debajo, en el mismo formato que las listas.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.
- Sin control de permisos propio en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.cartas_presentacion_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/cartas_presentacion_buscar.md`

## Buscar los centros

### Para Que Sirve

Mantener los datos de presentación (director, contacto, zona) de los centros de la delegación o de regiones extranjeras.

### Donde Entrar

- Cartas Presentacion (frontend/cartaspresentacion/controller/cartas_presentacion.php)
- **Legacy:** scdl > direcciones > cartas presentacion > modificar
- **Pills2:** scdl > direcciones > cartas presentacion > modificar

### Tareas Habituales

#### Buscar los centros

1. Abrir **modificar**: sale el formulario de selección.
2. Elegir «según dl» y, si procede, la población (con «según r» no hay población).
3. Pulsar **buscar**: debajo aparece la tabla de centros.

#### Modificar la carta de un centro

1. Pulsar **director** en la fila del centro: se abre una ventana.
2. Rellenar los campos y pulsar **guardar**; la lista se actualiza.

#### Quitar una carta

1. En las filas con carta, pulsar **quitar** y confirmar.

### Errores O Avisos Frecuentes

- `Formulario: No puede modificar datos de otra dl, Centro no encontrado.`
- `Update: Hay un error, no se ha guardado.`
- `Eliminar: Carta de presentacion no encontrada, Hay un error, no se ha borrado.`

### Permisos

- Sin control de permisos propio; solo compone rutas. La autorización la ejercen los endpoints destino
- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.
- Validación en el caso de uso: solo centros de la propia delegación (`ConfigGlobal::mi_delef()`) o
- Al crear: solo centros de la propia dl o `tipo_ctr=cr` (`resolveWriteRepo`). Al actualizar una carta
- Sin control de permisos propio en el caso de uso; la acción solo se ofrece en el listado para

### Referencias Internas

- Flujo: `cartaspresentacion.cartas_presentacion_shell.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/cartas_presentacion_shell.md`

## Elegir la población

### Para Que Sirve

Filtrar la lista de centros por población dentro de la delegación.

### Donde Entrar

- Cartas Presentacion (frontend/cartaspresentacion/controller/cartas_presentacion.php)
- sin entrada de menú en el índice (auxiliar de `cartas_presentacion` > modificar).

### Tareas Habituales

#### Elegir la población

1. En «según dl» elegir la opción de la delegación: se rellena el desplegable de poblaciones.
2. Elegir la población. Si se elige «según r», el desplegable de población se oculta.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.poblaciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/poblaciones.md`

## Ver los centros y sus cartas

### Para Que Sirve

Ver qué centros tienen carta de presentación y acceder a modificarla, ver la ficha o quitarla.

### Donde Entrar

- Cartas Presentacion (frontend/cartaspresentacion/controller/cartas_presentacion.php)
- sin entrada de menú en el índice (fragmento de `cartas_presentacion` > modificar).

### Tareas Habituales

#### Ver los centros y sus cartas

1. Elegir el filtro (delegación o regiones y, en el primer caso, la población).
2. Pulsar **buscar**.
3. Aparece la tabla con las columnas director, centro, carta de presentación y dirección.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/ubis.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/cartaspresentacion/flujos/`.
