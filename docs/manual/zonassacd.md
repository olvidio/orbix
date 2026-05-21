---
tipo: "manual_usuario"
modulo: "zonassacd"
flujos: 5
estado_revision: "revisado_parcial"
---

# Manual De Usuario - zonassacd

Gestión de **zonas geográficas** y asignación de SACD y centros a zonas. Rutas de menú desde `documentacion/Documentacion_Obix/menus.csv` (rol Exterior 8 / Dre).

## Acceso Por Menu

| Texto en menu | Pantalla | Seccion manual |
|---------------|----------|----------------|
| **Zonas geogr.** / **Zonas** | `shared/tablaDB_lista_ver.php` (`InfoZona`) | Mantenimiento tablas zonas (modulo `shared`) |
| **Zonas-ctr** | `frontend/zonassacd/controller/zona_ctr.php` | Zona centros |
| **Zonas-sacd** | `frontend/zonassacd/controller/zona_sacd.php` | Zona SACD |
| **Lista sacd-zona** | Endpoint `/src/zonassacd/zona_sacd_lista_tot` (legacy menu apuntaba a ajax) | Lista total SACD |

Permisos de escritura habituales: oficina **`des`** o **`vcsd`**.

## Zona SACD

> Seccion revisada manualmente.

### Para Que Sirve

Consultar los **SACD asignados a una zona** (o sin zona), reasignarlos a otra zona, añadir asignación iglesia/CGI, y editar **días de la semana** (L–D) de un SACD en la zona (modal).

### Donde Entrar

- Menu **Zonas-sacd** → `frontend/zonassacd/controller/zona_sacd.php`

### Tareas Habituales

#### Consultar SACD de una zona

1. Elegir zona en **Lista de SACD de la zona** (incluye *sin asignar zona*).
2. Revisar la tabla cargada en `#lst_sacds`.

#### Reasignar SACD marcados

1. Marcar filas en la tabla.
2. Elegir zona destino en **Asignar los SACD marcados a la zona**.
3. Pulsar **Cambiar asignación zona** (sustituye) o **Añadir asignación iglesia/CGI** (acumula).
4. Revisar aviso si aparece; la lista se refresca sola.

#### Editar dias de la semana de un SACD

1. Marcar **una** fila y abrir el modal (accion modificar del listado).
2. Marcar/desmarcar Lunes–Domingo.
3. Pulsar **Grabar** (llama modulo **misas**: `zona_sacd_datos_get` / `zona_sacd_datos_put`).

### Errores O Avisos Frecuentes

| Mensaje | Que hacer |
|---------|-----------|
| Texto en alert **respuesta:** | Leer mensaje del servidor tras update o edicion dias |
| Lista vacia | Comprobar zona seleccionada |
| Sin botones de asignacion | Usuario sin permiso `des` |

### Referencias Internas

- Flujo: `zonassacd.zona_sacd.gestionar.flujo`
- Modulo relacionado: `misas` (dias semana SACD)

## Zona Centros

> Seccion revisada manualmente.

### Para Que Sirve

Consultar **centros** asignados a una zona (o sin zona / sin zona SF) y **reasignar** centros marcados a otra zona.

### Donde Entrar

- Menu **Zonas-ctr** → `frontend/zonassacd/controller/zona_ctr.php`

### Tareas Habituales

1. Elegir zona en el desplegable (opciones *sin asignar zona*, *sin asignar zona sf* si hay permiso `des`).
2. Revisar tabla de centros.
3. Marcar centros, elegir zona destino, pulsar **Asignar**.
4. Comprobar que la lista se actualiza.

### Errores O Avisos Frecuentes

- Alert **respuesta:** tras guardar — leer texto y corregir seleccion.

### Referencias Internas

- Flujo: `zonassacd.zona_ctr.gestionar.flujo`

## Lista Total SACD-Zona

> Seccion revisada manualmente (API; pantalla legacy en migracion).

### Para Que Sirve

Obtener listado **global** de todos los SACD de la delegacion con sus zonas (y flag *propia*), para consulta/exportacion.

### Donde Entrar

- Menu **Lista sacd-zona** (rol Dre: refmenu `que=get_lista_tot`)
- Endpoint JSON: `/src/zonassacd/zona_sacd_lista_tot`

### Notas

- El menu legacy apuntaba a `zona_sacd_ajax.php` con `que=get_lista_tot`; la ruta `/src/zonassacd/zona_sacd_ajax` esta registrada pero el controller HTTP puede estar pendiente de migracion — usar `zona_sacd_lista_tot` como endpoint canonico.

## Revision Pendiente

- Confirmar pantalla frontend definitiva para **lista sacd-zona** tras migracion.
- Documentar errores concretos desde application layer en fichas API.
- Enlazar flujos `*_ajax` tecnicos con pantallas (ver `relaciones/pantallas_api.md`).
