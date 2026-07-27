---
tipo: "manual_usuario"
modulo: "configuracion"
flujos: 4
estado_revision: "revisado_parcial"
---

# Manual De Usuario - configuracion

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## módulo (ficha)

### Para Que Sirve

Dar de alta un módulo nuevo o editar nombre, descripción y dependencias (módulos/apps requeridos) de uno existente.

### Donde Entrar

- Ficha de módulo (frontend/configuracion/controller/modulos_form.php)
- Proxy AJAX modulos_update (frontend/configuracion/controller/modulos_update.php)
- Sin entrada de menú en el índice (subflujo de «definir módulos»).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado (solo en baja desde listado)`

### Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se

### Referencias Internas

- Flujo: `configuracion.modulos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/modulos.md`

## Definir módulos (listado)

### Para Que Sirve

Consultar los módulos definidos en el esquema y acceder a alta, edición o baja de cada uno.

### Donde Entrar

- Definir módulos (frontend/configuracion/controller/modulos_select.php)
- **Legacy:** sistema > Configuración > definir módulos
- **Pills2:** sistema > Configuración > definir módulos; ADMIN GLOBAL > Configuración > definir módulos

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado (+ texto de getErrorTxt() del repositorio)`

### Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se

### Referencias Internas

- Flujo: `configuracion.modulos_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/modulos_select.md`

## Configurar parámetros del esquema

### Para Que Sirve

Consultar y modificar los parámetros globales del esquema (curso escolar, certificados, idioma, ámbito territorial, gestión de calendario, etc.).

### Donde Entrar

- Configuración del esquema (frontend/configuracion/controller/parametros.php)
- **Legacy:** sistema > Configuración > config esquema
- **Pills2:** ADMIN LOCAL > Esquema; sistema > Configuración > config esquema

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No hay control de permisos propio en el controller; la autorización de oficina se

### Referencias Internas

- Flujo: `configuracion.parametros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/parametros.md`

## Periodo calendario escolar (interno)

### Para Que Sirve

No hay pantalla de usuario: el frontend obtiene fechas de inicio/fin de curso STGR y CRT (caché en sesión o BD) para que `Periodo` calcule rangos de fechas en listados y filtros de calendario.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- Sin entrada de menú en el índice (flujo técnico transversal; configuración en «config esquema»).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica control de permisos propio: se limita a leer configuración.

### Referencias Internas

- Flujo: `configuracion.periodo_calendario_escolar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/periodo_calendario_escolar.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/configuracion/flujos/`.
