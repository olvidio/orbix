---
tipo: "manual_usuario"
modulo: "zonassacd"
flujos: 5
estado_revision: "revisado_parcial"
---

# Manual De Usuario - zonassacd

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Zona Ctr

### Para Que Sirve

Consultar y reasignar centros (dl y sf) a zonas geográficas desde el desplegable de zona.

### Donde Entrar

- Zona Ctr (frontend/zonassacd/controller/zona_ctr.php)
- Zona Ctr Lista Ajax (frontend/zonassacd/controller/zona_ctr_lista_ajax.php)
- Zona Ctr Update Ajax (frontend/zonassacd/controller/zona_ctr_update_ajax.php)
- **Legacy:** dre > zonas > zonas-ctr
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-ctr

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado.`

### Referencias Internas

- Flujo: `zonassacd.zona_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_ctr.md`

## Zona Ctr Ajax

### Para Que Sirve

Endpoint legacy sin implementación; funcionalidad en zona_ctr_lista y zona_ctr_update.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `zonassacd.zona_ctr_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_ctr_ajax.md`

## Zona Sacd

### Para Que Sirve

Consultar y gestionar la asignación de sacerdotes (sacd) a zonas geográficas: listado por zona, cambio de zona propia, asignaciones iglesia/cgi y edición de días de atención semanal.

### Donde Entrar

- Zona Sacd (frontend/zonassacd/controller/zona_sacd.php)
- Zona Sacd Lista Ajax (frontend/zonassacd/controller/zona_sacd_lista_ajax.php)
- Zona Sacd Update Ajax (frontend/zonassacd/controller/zona_sacd_update_ajax.php)
- **Legacy:** dre > zonas > zonas-sacd
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-sacd

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `zonassacd.zona_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_sacd.md`

## Zona Sacd Ajax

### Para Que Sirve

Endpoint legacy sin implementación; funcionalidad repartida en zona_sacd_lista, zona_sacd_update y zona_sacd_lista_tot.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `zonassacd.zona_sacd_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_sacd_ajax.md`

## Zona Sacd Lista Tot

### Para Que Sirve

- Ver el listado global sacd–zona de toda la delegación (una fila por asignación).
- Entrada de menú Lista sacd-zona.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** dre > zonas > lista sacd-zona
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Lista sacd-zona

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `zonassacd.zona_sacd_lista_tot.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/zonassacd/flujos/zona_sacd_lista_tot.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/zonassacd/flujos/`.
