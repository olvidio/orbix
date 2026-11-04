---
tipo: "manual_usuario"
modulo: "actividadescentro"
flujos: 7
estado_revision: "revisado_parcial"
---

# Manual De Usuario - actividadescentro

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Abrir la asignación de centros encargados

### Para Que Sirve

Preparar la pantalla de asignación de centros encargados antes de mostrar los filtros de periodo.

### Donde Entrar

- Activ Ctr (frontend/actividadescentro/controller/activ_ctr.php)
- Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):
- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
- agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
- asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.

### Tareas Habituales

#### Abrir la asignación de centros encargados

1. Entrar en la pantalla de centros encargados.
2. La pantalla se prepara sola y muestra los filtros de periodo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica control de permisos propio: solo compone rutas. La autorización real la

### Referencias Internas

- Flujo: `actividadescentro.activ_ctr_shell.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/activ_ctr_shell.md`

## Centro Encargado

### Para Que Sirve

El usuario quita un centro de la lista de encargados de una actividad.

### Donde Entrar

- Activ Ctr (frontend/actividadescentro/controller/activ_ctr.php)
- Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):
- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
- agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
- asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.

### Tareas Habituales

#### Eliminar

1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **borrar**.
3. El sistema lo elimina y refresca la celda de centros de la actividad.

### Errores O Avisos Frecuentes

- `el centro encargado ya no existe`
- `hay un error, no se ha eliminado el centro`
- `no se sabe cual borrar`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend (la

### Referencias Internas

- Flujo: `actividadescentro.centro_encargado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centro_encargado.md`

## Centro Encargado Asignar

### Para Que Sirve

- El usuario asigna un centro (elegido en el desplegable de candidatos) como encargado de una actividad.
- El centro queda al final del listado (`num_orden = max + 1`) con `encargo = 'organizador'`.

### Donde Entrar

- Activ Ctr (frontend/actividadescentro/controller/activ_ctr.php)
- Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):
- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
- agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
- asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.

### Tareas Habituales

#### Ejecutar

1. En una actividad, pulsar **nuevo** para ver los centros candidatos.
2. Pulsar el centro deseado.
3. El sistema lo guarda como encargado y refresca la celda de centros de la actividad.

### Errores O Avisos Frecuentes

- `faltan parametros id_activ / id_ubi`
- `hay un error, no se ha guardado el centro encargado`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividadescentro.centro_encargado_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centro_encargado_asignar.md`

## Centro Encargado Reordenar

### Para Que Sirve

- El usuario sube (**+ prioridad**) o baja (**- prioridad**) un centro encargado en el listado de una actividad.
- Internamente se intercambia el `num_orden` con el centro vecino.

### Donde Entrar

- Activ Ctr (frontend/actividadescentro/controller/activ_ctr.php)
- Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):
- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
- agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
- asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.

### Tareas Habituales

#### Ejecutar

1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **+ prioridad** o **- prioridad**.
3. El sistema reordena y refresca la celda de centros de la actividad.

### Errores O Avisos Frecuentes

- `direccion de orden incorrecta (mas / menos)`
- `faltan parametros id_activ / id_ubi`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend (la

### Referencias Internas

- Flujo: `actividadescentro.centro_encargado_reordenar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centro_encargado_reordenar.md`

## Asignar un centro encargado

### Para Que Sirve

Ver los centros candidatos para asignar como encargados de una actividad.

### Donde Entrar

- Activ Ctr (frontend/actividadescentro/controller/activ_ctr.php)
- Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):
- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
- agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
- asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.

### Tareas Habituales

#### Asignar un centro encargado

1. En la fila de la actividad, pulsar **nuevo**.
2. Se listan los centros candidatos (para sg, con el nº de actividades del periodo y la distancia en días a su actividad más próxima).
3. Elegir el centro para asignarlo.

### Errores O Avisos Frecuentes

- `tipo no valido`

### Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadescentro.centros_disponibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centros_disponibles.md`

## Ver los centros encargados actualizados

### Para Que Sirve

Refrescar la lista de centros encargados de una actividad tras asignar, reordenar o quitar uno.

### Donde Entrar

- Activ Ctr (frontend/actividadescentro/controller/activ_ctr.php)
- Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):
- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
- agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
- asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.

### Tareas Habituales

#### Ver los centros encargados actualizados

1. Asignar, reordenar o quitar un centro.
2. La celda de la actividad se refresca sola con la lista al día.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Resuelve el permiso `ctr` con `PermisosActividades` (`$_SESSION['oPermActividades']`) cuando

### Referencias Internas

- Flujo: `actividadescentro.centros_encargados.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centros_encargados.md`

## Consultar las actividades y sus centros encargados

### Para Que Sirve

Ver las actividades de un periodo con los centros encargados actuales.

### Donde Entrar

- Activ Ctr (frontend/actividadescentro/controller/activ_ctr.php)
- Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):
- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
- agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
- asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.

### Tareas Habituales

#### Consultar las actividades y sus centros encargados

1. Elegir el periodo.
2. Pulsar **buscar**.
3. Se muestra la tabla de actividades con sus centros encargados; según los permisos aparecen los botones para modificar o crear.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Se apoya en `PermisosActividades` (`$_SESSION['oPermActividades']`) cuando `procesos` está instalado;
- Por actividad exige `have_perm_activ('ocupado')` y `have_perm_activ('ver')` en la faceta `datos`;

### Referencias Internas

- Flujo: `actividadescentro.lista_actividades_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/lista_actividades_ctr.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/actividadescentro/flujos/`.
