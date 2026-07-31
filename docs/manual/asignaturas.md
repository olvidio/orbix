---
tipo: "manual_usuario"
modulo: "asignaturas"
flujos: 1
estado_revision: "revisado_parcial"
---

# Manual De Usuario - asignaturas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## API asignaturas (soporte)

### Para Que Sirve

Datos de asignaturas consumidos por notas y profesores (sin pantalla propia).

### Donde Entrar

- API soporte asignaturas ((sin controller propio))
- **Legacy:** sin entrada de menú en el índice (API de soporte para notas/profesores)
- **Pills2:** sin entrada de menú en el índice (API de soporte para notas/profesores)

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica ningún control de permisos propio: la autorización se resuelve en el

### Referencias Internas

- Flujo: `asignaturas.api_soporte.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asignaturas/flujos/asignaturas_api.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/asignaturas/flujos/`.
