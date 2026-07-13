---
tipo: "manual_usuario"
modulo: "dossiers"
flujos: 5
estado_revision: "generado"
---

# Manual De Usuario - dossiers

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Dossiers Lista Fichas

### Para Que Sirve

Mostrar la tabla de carpetas de dossiers disponibles para la entidad actual, con iconos de permiso y enlace a cada ficha (`href_ver` firmado en frontend).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- No hay control de acceso propio en el endpoint. La visibilidad de cada tipo de dossier depende de

### Referencias Internas

- Flujo: `dossiers.dossiers_lista_fichas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/dossiers_lista_fichas.md`

## Dossiers Ver Pantalla

### Para Que Sirve

- Abrir y navegar los dossiers de una persona, actividad o ubi: cabecera, relación de carpetas o ficha con widgets embebidos (matrículas, asistentes, certificados, tablas genéricas).
- Reutilizado desde `home_persona`, `home_ubis`, `actividad_ver` y otras pantallas vía `fnjs_update_div`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `clase_info invalida`
- `No encuentro a nadie con id_nom: <id>`
- `ubi no encontrada`
- `actividad no encontrada`
- `pau desconocido`
- `El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers).`

### Permisos

- El endpoint no aplica un middleware de permisos propio; el `permiso` viaja como parámetro y se

### Referencias Internas

- Flujo: `dossiers.dossiers_ver_pantalla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/dossiers_ver_pantalla.md`

## Perm Dossier Ver

### Para Que Sirve

Consultar o modificar la definición y máscaras de permiso de un `TipoDossier` concreto; volver al listado tras guardar o eliminar.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `No se encuentra el dossier: <id>`

### Permisos

- El caso de uso consulta `$_SESSION['oPerm']`: si el usuario tiene permiso de oficina `admin_sv`

### Referencias Internas

- Flujo: `dossiers.perm_dossier_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/perm_dossier_ver.md`

## Perm Dossiers

### Para Que Sirve

Elegir el ámbito de tipos de dossier (personas/ubis/actividades) y abrir la edición de permisos de cada tipo desde el menú de administración.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- El caso de uso no aplica un control de permisos propio: la autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `dossiers.perm_dossiers.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/perm_dossiers.md`

## Tipo Dossier

### Para Que Sirve

Persistir cambios (`tipo_dossier_guardar`) o eliminar (`tipo_dossier_eliminar`) un tipo de dossier desde el formulario `perm_dossier_ver` (solo administradores `admin_sv`/`admin_sf`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `falta id_tipo_dossier`
- `No se encuentra el dossier: <id>`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

### Permisos

- El caso de uso no aplica un control de permisos propio. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `dossiers.tipo_dossier.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dossiers/flujos/tipo_dossier.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
