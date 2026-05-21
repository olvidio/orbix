---
tipo: "manual_usuario"
modulo: "actividadtarifas"
flujos: 3
estado_revision: "revisado_parcial"
---

# Manual De Usuario - actividadtarifas

Manual revisado (secciones de flujo). Rutas de menú tomadas de `documentacion/Documentacion_Obix/menus.csv` y guía Dre §10.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario.

## Acceso Por Menu

Entrada habitual (roles con menú **Tarifas**, p. ej. Dre rol 7, Exterior 8/9, Calendario 20):

| Texto en menu | Pantalla | Manual |
|---------------|----------|--------|
| **Definir tarifa** / **Activ → tarifa** | `frontend/actividadtarifas/controller/tarifa.php` | Tipo Tarifa |
| **Tarifa ↔ tipo de actividad** / **Id_tarifa ↔ tipo actividad** | `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php` | Relacion Tarifa |
| **Tarifas por casa y año** | `frontend/actividadtarifas/controller/tarifa_ubi.php` | Tarifa Ubi |

También accesible: **estudio económico / calendario de casa** (`casas`) para actualización masiva de importes (Tarifa Ubi, endpoint `tarifa_ubi_update_inc`).

## Relacion Tarifa

> Sección revisada manualmente (`estado_revision: revisado`).

### Para Que Sirve

Definir **qué tipo de tarifa del catálogo corresponde a cada tipo de actividad** (relación tarifa ↔ tipo de actividad). Sirve para que, al calcular tarifas en actividades, Orbix sepa qué letra/modo de tarifa aplicar según el tipo de actividad.

### Donde Entrar

- Menú **Tarifas → Tarifa ↔ tipo de actividad** (`frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`).

### Tareas Habituales

#### Consultar el listado

1. Al abrir la pantalla, el listado se carga automáticamente en la zona central.
2. Revisar columnas: identificador, sección, tipo de actividad, tarifa asociada y enlace **modificar** (si el permiso lo permite).

#### Añadir una relación

1. Pulsar **añadir tarifa tipo** (solo si aparece el enlace; requiere permiso de alta).
2. En la ventana, elegir el **tipo de actividad** con los desplegables en cascada (sección, asistentes, actividad, etc.).
3. Elegir la **tarifa** en el desplegable.
4. Pulsar **Guardar**.
5. Comprobar que la nueva fila aparece en el listado.

#### Modificar una relación existente

1. Pulsar **modificar** en la fila deseada.
2. El formulario muestra el tipo de actividad (solo lectura) y un desplegable para cambiar la **tarifa**.
3. Pulsar **Guardar** o **Cancelar** para cerrar sin cambios.

#### Eliminar una relación

1. Abrir el formulario de modificación de la fila.
2. Pulsar **Eliminar** y confirmar el aviso (*¿Está seguro que desea quitar esta id_tarifa?*).
3. Verificar que la fila desaparece del listado.

### Errores O Avisos Frecuentes

| Mensaje | Qué significa / qué hacer |
|---------|---------------------------|
| `debe indicar el tipo de actividad` | Faltan desplegables del tipo de actividad (validación en pantalla antes de enviar). Completar sección/asistentes/actividad. |
| `debe indicar la tarifa` / `debe indicar la id_tarifa` | No se ha elegido tarifa en el desplegable. |
| `no se encuentra la relación` | El registro ya no existe; refrescar la pantalla (recargar página). |
| `hay un error, no se ha guardado` / `...borrado` | Error al persistir; anotar mensaje y contactar soporte si persiste. |
| `no sé cuál he de borrar` | Identificador de relación inválido; volver a abrir desde el listado. |
| `error de comunicación con el servidor` | Problema de red o sesión; reintentar o volver a entrar en Orbix. |

### Permisos

- Enlace **modificar** en el listado: permiso oficina **`adl`** (y tarifa de la misma sección SV/SF que el usuario).
- Enlace **añadir tarifa tipo**: permisos **`adl`**, **`pr`** o **`calendario`**.

### Referencias Internas

- Flujo: `actividadtarifas.relacion_tarifa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadtarifas/flujos/relacion_tarifa.md`

## Tarifa Ubi

> Sección revisada manualmente (`estado_revision: revisado`). Sirve de plantilla para redactar el resto del manual.

### Para Que Sirve

Consultar y mantener las **tarifas económicas de una casa (ubi) para un año concreto**: ver el listado, dar de alta una tarifa nueva, modificar importes, eliminar entradas, copiar tarifas del año anterior y, desde el estudio económico de la casa, actualizar importes en lote.

### Donde Entrar

- Menú **Tarifas → Tarifas por casa y año** (`frontend/actividadtarifas/controller/tarifa_ubi.php`).
- También desde **calendario / estudio económico de casa** (`frontend/casas/controller/calendario_ubi_resumen.php`).

### Tareas Habituales

#### Consultar el listado

1. Elegir la **casa** en el desplegable y el **año**.
2. Pulsar **Buscar**.
3. Revisar la tabla en la zona `#ficha` (tipo de tarifa, serie, cantidad, acciones).

#### Crear o modificar

1. En el listado, pulsar **modificar** en una fila existente, o la acción equivalente a **nueva tarifa** (abre el formulario en una ventana superpuesta).
2. Completar **tipo de tarifa**, **serie** e **importe (cantidad)**; opcionalmente observaciones.
3. Pulsar **Guardar**.
4. Si la operación es correcta, la ventana se cierra y el listado se refresca solo.

#### Eliminar

1. Abrir el formulario de una tarifa existente (no disponible en alta nueva).
2. Pulsar **Eliminar** y confirmar el aviso.
3. Comprobar que la fila desaparece del listado tras refrescar.

#### Copiar tarifas del año anterior

1. Cargar el listado de la casa y año destino (pasos de consulta).
2. Pulsar el botón de **copiar** (solo visible si el backend lo permite para ese contexto).
3. Confirmar el diálogo.
4. Verificar que aparecen las tarifas copiadas en el listado.

#### Actualizar importes en lote

1. Entrar desde el **estudio económico de la casa** (pantalla que envía el array `inc_cantidad`).
2. Ajustar los importes en la rejilla o formulario de incrementos.
3. Guardar la actualización masiva.
4. Revisar que las cantidades mostradas coinciden con lo esperado.

### Errores O Avisos Frecuentes

| Mensaje | Qué significa / qué hacer |
|---------|---------------------------|
| `Operación no autorizada` | La sesión o la cápsula de seguridad (`ctx_*`) ha caducado o es inválida. Cerrar el formulario, volver a cargar el listado o el formulario y repetir. |
| `no se encuentra la tarifa` | El registro ya no existe (otro usuario lo borró o el listado está desactualizado). Refrescar con **Buscar**. |
| `hay un error, no se ha guardado` / `...borrado` | Error de base de datos o validación. Anotar el mensaje completo y contactar con soporte si persiste. |
| `no sé qué casa/año tengo que copiar` | Falta contexto en la petición de copia. Volver a buscar casa+año y repetir desde el listado. |
| `función de copiar tarifas pendiente de reimplementar` | La acción de copiar no está disponible en este entorno; usar alta manual o consultar con administración. |
| `token de autorización no disponible, vuelva a cargar la pantalla` | Recargar la página y pulsar **Buscar** de nuevo antes de copiar. |

### Permisos

- Listado y acciones habituales: permisos de oficina **`adl`**, **`pr`** o **`calendario`** (según operación).
- Desplegable de casas: usuarios con permiso **`des`** o **`vcsd`** ven todas las casas; el resto solo las de su sección (SV/SF).

### Referencias Internas

- Flujo: `actividadtarifas.tarifa_ubi.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadtarifas/flujos/tarifa_ubi.md`
- Convenciones API (HashB, `ctx_update`): `docs/catalogo/_convenciones_api.md`

## Tipo Tarifa

> Sección revisada manualmente (`estado_revision: revisado`).

### Para Que Sirve

Mantener el **catálogo maestro de tipos de tarifa** (letra identificativa, modo de cálculo y observaciones). Estas tarifas se usan después en las relaciones con tipos de actividad y en las tarifas por casa/año.

### Donde Entrar

- Menú **Tarifas → Definir tarifa** (`frontend/actividadtarifas/controller/tarifa.php`).

### Tareas Habituales

#### Consultar el listado

1. Al abrir la pantalla, el listado se carga solo (columnas: id, sección, letra, modo, observaciones).
2. Revisar las filas de la sección SV/SF que corresponda al usuario.

#### Crear un tipo de tarifa

1. Pulsar el enlace **nueva tarifa** al pie del listado (visible con permiso de alta).
2. En la ventana: indicar **letra** (código corto), **modo** (desplegable) y **observaciones** si procede.
3. Pulsar **Guardar**.
4. Comprobar que la nueva fila aparece en el listado.

#### Modificar un tipo de tarifa

1. Pulsar **modificar** en la fila deseada (solo filas de la propia sección y con permiso `adl`).
2. Ajustar letra, modo u observaciones.
3. Pulsar **Guardar** o **Cancelar**.

#### Eliminar un tipo de tarifa

1. Abrir el formulario de una tarifa existente (no en alta nueva).
2. Pulsar **Eliminar** y confirmar (*¿Está seguro de borrar esta tarifa?*).
3. Verificar que desaparece del listado.

### Errores O Avisos Frecuentes

| Mensaje | Qué significa / qué hacer |
|---------|---------------------------|
| `no se encuentra la tarifa` | El registro fue borrado o el listado está desactualizado. Recargar la página. |
| `hay un error, no se ha guardado` / `...borrado` | Conflicto o error de base de datos; revisar si la letra ya existe o contactar soporte. |
| `no sé cuál he de borrar` | Identificador inválido; volver a abrir desde el listado. |
| `error de comunicación con el servidor` | Reintentar; comprobar sesión activa. |

### Permisos

- **Modificar** en el listado: permiso **`adl`** y tarifa de la misma sección (SV/SF) que el usuario.
- **Nueva tarifa**: permisos **`adl`**, **`pr`** o **`calendario`**.

### Referencias Internas

- Flujo: `actividadtarifas.tipo_tarifa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadtarifas/flujos/tipo_tarifa.md`

## Revision Pendiente

- Validar textos de menu en instalaciones con roles distintos a Dre/Exterior/Calendario.
- Añadir capturas si se publica para usuarios finales.
- Las tres secciones de tarifas (Relación, Ubi, Tipo) están revisadas.
