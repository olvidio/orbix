---
tipo: "manual_usuario"
modulo: "zonassacd"
flujos: 5
estado_revision: "revisado"
---

# Manual De Usuario - zonassacd

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Consultar y reasignar los centros de una zona

### Para Que Sirve

Ver qué centros pertenecen a cada zona de sacerdotes y pasarlos de una zona a otra.

### Donde Entrar

- Zona Ctr (frontend/zonassacd/controller/zona_ctr.php)
- **Legacy:** dre > zonas > zonas-ctr
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-ctr

### Tareas Habituales

#### Consultar y reasignar los centros de una zona

1. Abrir «Lista de centros de la zona» y elegir la zona en el desplegable (por ejemplo Pedralbes, o «sin asignar zona» / «sin asignar zona sf»).
2. Aparece la tabla de centros con su zona (las filas sombreadas en azul claro son centros de la sf).
3. Para cambiarlos de zona, marcar los centros, elegir la zona de destino en «asignar los centros marcados a la zona» y pulsar **asignar** (solo si tu rol lo permite).

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado.`

### Referencias Internas

- Flujo: `zonassacd.zona_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_ctr.md`

## las zonas de un centro

### Para Que Sirve

Cargar la lista de centros de la zona elegida y guardar la nueva asignación.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Gestionar las zonas de un centro

1. Elegir la zona en «Lista de centros de la zona».
2. Marcar los centros, elegir la zona de destino y pulsar **asignar**.
3. La tabla se vuelve a cargar con las zonas actualizadas.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `zonassacd.zona_ctr_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_ctr_ajax.md`

## Consultar los sacerdotes de una zona

### Para Que Sirve

Ver los sacerdotes de cada zona, los días de la semana que atienden, y cambiarlos de zona.

### Donde Entrar

- Zona Sacd (frontend/zonassacd/controller/zona_sacd.php)
- **Legacy:** dre > zonas > zonas-sacd
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-sacd

### Tareas Habituales

#### Consultar los sacerdotes de una zona

1. Abrir «Lista de sacd de la zona» y elegir la zona (o «sin asignar zona»).
2. Aparece una tabla con sacerdote, zona, **propia** (1 si es su zona propia) y los días **L M X J V S D** (una «x» o un «-» en cada día: indica si el sacerdote atiende o no ese día, es decir, si está o no está).

#### Cambiar los días o la zona de un sacerdote

1. Marcar al sacerdote y pulsar **modificar**: se abre una ventana con su nombre, la casilla **propia** y los días (Lunes a Domingo). Pulsar **Grabar** para guardar o **Cancel** para salir.
2. Para cambiar de zona a varios sacerdotes: marcarlos, elegir la zona en «asignar los sacd marcados a la zona» y pulsar **cambiar asignación zona**; con **añadir asignación iglesia/cgi** en lugar de cambiar de zona se añade otra, de modo que el sacerdote queda con dos zonas.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `zonassacd.zona_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_sacd.md`

## las zonas de un sacerdote

### Para Que Sirve

Cargar la lista de sacerdotes de la zona elegida y guardar los cambios de días y de zona.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Gestionar las zonas de un sacerdote

1. Elegir la zona en «Lista de sacd de la zona».
2. Usar **modificar**, **cambiar asignación zona** o **añadir asignación iglesia/cgi** sobre los sacerdotes marcados.
3. La tabla se vuelve a cargar con los cambios.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `zonassacd.zona_sacd_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_sacd_ajax.md`

## Consultar los sacerdotes por zona

### Para Que Sirve

Ver todos los sacerdotes con la zona o zonas que tienen asignadas.

### Donde Entrar

- Zona Sacd Lista Ajax (frontend/zonassacd/controller/zona_sacd_lista_ajax.php)
- **Legacy:** dre > zonas > lista sacd-zona
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Lista sacd-zona

### Tareas Habituales

#### Consultar los sacerdotes por zona

1. Entrar en **lista sacd-zona**.
2. Se muestra una fila por cada sacerdote y zona asignada.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `zonassacd.zona_sacd_lista_tot.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_sacd_lista_tot.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/zonassacd/flujos/`.
