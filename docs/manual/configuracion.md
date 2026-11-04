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

## Añadir o modificar un módulo

### Para Que Sirve

Dar de alta un módulo nuevo, o cambiar el nombre, la descripción y las dependencias (módulos y aplicaciones que necesita) de uno existente.

### Donde Entrar

- Ficha de módulo (frontend/configuracion/controller/modulos_form.php)
- Proxy AJAX modulos_update (frontend/configuracion/controller/modulos_update.php)
- Sin entrada de menú en el índice (subflujo de «definir módulos»).

### Tareas Habituales

#### Añadir o modificar un módulo

1. En la lista de módulos, pulsar **añadir módulo**, o seleccionar uno y pulsar **modificar**.
2. Aparece el formulario con los módulos y aplicaciones disponibles. Las aplicaciones que necesitan otros módulos salen marcadas y no se pueden desmarcar.
3. Rellenar o cambiar el nombre, la descripción y las dependencias (al marcar o desmarcar una casilla, el cambio se guarda al momento).
4. Pulsar **Guardar cambios** para volver a la lista.

#### Eliminar un módulo

1. Se hace desde la lista de módulos, no desde el formulario (ver «Consultar los módulos definidos»).

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado (solo en baja desde listado)`

### Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se

### Referencias Internas

- Flujo: `configuracion.modulos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/modulos.md`

## Consultar los módulos definidos

### Para Que Sirve

Ver los módulos definidos en el esquema y acceder al alta, la modificación o la baja de cada uno.

### Donde Entrar

- Definir módulos (frontend/configuracion/controller/modulos_select.php)
- **Legacy:** sistema > Configuración > definir módulos
- **Pills2:** sistema > Configuración > definir módulos; ADMIN GLOBAL > Configuración > definir módulos

### Tareas Habituales

#### Consultar los módulos definidos

1. Abrir **definir módulos**: aparece la tabla de módulos con sus dependencias.

#### Añadir un módulo

1. Pulsar **añadir módulo** y seguir los pasos de «Añadir o modificar un módulo».

#### Modificar un módulo

1. Seleccionar una sola fila.
2. Pulsar **modificar**.

#### Eliminar un módulo

1. Seleccionar la fila y pulsar el botón de eliminar.
2. Confirmar.
3. La lista se refresca; si no se puede borrar, sale un aviso con el motivo.

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado (+ texto de getErrorTxt() del repositorio)`

### Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se

### Referencias Internas

- Flujo: `configuracion.modulos_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/modulos_select.md`

## Ver los parámetros del esquema

### Para Que Sirve

Consultar y cambiar los parámetros generales del esquema: curso escolar, certificados, idioma, ámbito territorial, gestión de calendario, etc.

### Donde Entrar

- Configuración del esquema (frontend/configuracion/controller/parametros.php)
- **Legacy:** sistema > Configuración > config esquema
- **Pills2:** ADMIN LOCAL > Esquema; sistema > Configuración > config esquema

### Tareas Habituales

#### Ver los parámetros del esquema

1. Abrir **config esquema**: aparece un bloque por cada parámetro con su valor actual.

#### Cambiar un parámetro

1. Editar el bloque que interese.
2. Los periodos del stgr y del crt se escriben como día y mes de inicio y de fin; el resto de parámetros llevan un solo valor (texto, opción o desplegable).
3. Pulsar **Guardar**: sale el aviso «se ha guardado correctamente».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No hay control de permisos propio en el controller; la autorización de oficina se

### Referencias Internas

- Flujo: `configuracion.parametros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/configuracion/flujos/parametros.md`

## Cambiar las fechas del curso

### Para Que Sirve

- No hay pantalla de usuario: es un dato interno.
- Los listados y filtros de calendario usan las fechas de inicio y fin del curso del stgr y del crt para calcular sus periodos.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- Sin entrada de menú en el índice (flujo técnico transversal; configuración en «config esquema»).

### Tareas Habituales

#### Cambiar las fechas del curso

1. Abrir **config esquema** (ver «Cambiar un parámetro»).
2. Cambiar el periodo del curso del stgr o del crt y guardar; los listados de calendario usarán las nuevas fechas.

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
