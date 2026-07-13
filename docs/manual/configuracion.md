---
tipo: "manual_usuario"
modulo: "configuracion"
flujos: 4
estado_revision: "generado"
---

# Manual De Usuario - configuracion

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## módulo (ficha)

### Para Que Sirve

Dar de alta un módulo nuevo o editar nombre, descripción y dependencias (módulos/apps requeridos) de uno existente.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica control de permisos propio: se limita a leer configuración.

### Referencias Internas

- Flujo: `configuracion.periodo_calendario_escolar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/periodo_calendario_escolar.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
