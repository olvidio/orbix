---
tipo: "manual_usuario"
modulo: "dbextern"
flujos: 16
estado_revision: "revisado_parcial"
---

# Manual De Usuario - dbextern

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Refrescar la copia de la BDU

### Para Que Sirve

- Si los datos de las listas han cambiado después de la fecha que se muestra, actualizar la copia local de la BDU antes de sincronizar.
- Tarda varios minutos.

### Donde Entrar

- Sincronizar con los datos de Listas (frontend/dbextern/controller/sincro_index.php)
- sin entrada de menú en el índice (acción embebida en `sincro_index`)

### Tareas Habituales

#### Refrescar la copia de la BDU

1. En la pantalla de sincronización, pulsar **refrescar**.
2. Esperar a que termine (varios minutos).
3. La pantalla se recarga con los contadores al día. Si falla, sale «Error al refrescar la BDU» con el motivo.

### Errores O Avisos Frecuentes

- `Error al refrescar la BDU: …`

### Permisos

- Sin control propio; HashF en `sincro_index.phtml` (`h2`).

### Referencias Internas

- Flujo: `dbextern.refrescar_bdu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/refrescar_bdu.md`

## Crear una persona desde la BDU

### Para Que Sirve

Cuando una persona de la BDU no tiene coincidencia en Aquinate, crear una ficha nueva y vincularla automáticamente.

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Crear una persona desde la BDU

1. En «Revisar BDU no unidas», con la persona delante, pulsar **crear nuevo**.
2. Se crea la ficha y queda vinculada.
3. Pasa automáticamente a la siguiente persona de la lista.

### Errores O Avisos Frecuentes

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `hay un error, no se ha guardado`

### Permisos

- HashF en `ver_listas.phtml` (`h_crear`).

### Referencias Internas

- Flujo: `dbextern.sincro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro.md`

## Dar de baja una ficha de Aquinate

### Para Que Sirve

Dar de baja la ficha de Aquinate de una persona que ya no está en la BDU.

### Donde Entrar

- Aquinate con BDU desaparecida (frontend/dbextern/controller/ver_desaparecidos_de_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Dar de baja una ficha de Aquinate

1. En la lista de fichas Aquinate sin correspondencia en la BDU, pulsar **baja** en la persona.
2. La ficha pasa a situación de baja. Si no se pudo cambiar la situación, avisa «OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.»

### Errores O Avisos Frecuentes

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

### Permisos

- HashF en `ver_desaparecidos_de_listas.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_baja.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_baja.md`

## Crear todas las fichas pendientes

### Para Que Sirve

Crear de golpe todas las fichas pendientes del punto 4, sin revisarlas una a una.

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Crear todas las fichas pendientes

1. Abrir «Revisar BDU no unidas» (punto 4).
2. Pulsar **crear todos**.
3. Si todo va bien aparece «Ja está»; si alguna persona falla, se muestran los errores acumulados.

### Errores O Avisos Frecuentes

- `Errores de sincro_crear por cada persona fallida`

### Permisos

- HashF en `ver_listas.phtml` (`h_crear_todos`).

### Referencias Internas

- Flujo: `dbextern.sincro_crear_todos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_crear_todos.md`

## Desunir una persona de la BDU

### Para Que Sirve

Romper un vínculo incorrecto entre una persona de la BDU y una ficha de Aquinate, para poder unirla de nuevo o crear la ficha después.

### Donde Entrar

- BDU desaparecidas en Aquinate (frontend/dbextern/controller/ver_desaparecidos_de_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Desunir una persona de la BDU

1. En la lista «BDU sin ficha Aquinate», pulsar **desunir** en la persona.
2. Si se ha hecho bien, la fila aparece tachada.

### Errores O Avisos Frecuentes

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado`

### Permisos

- HashF en `ver_desaparecidos_de_orbix.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_desunir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_desunir.md`

## Ver la situación de la sincronización

### Para Que Sirve

Al abrir la pantalla de sincronización, ver los contadores de la situación entre la BDU y Aquinate y entrar en cada subpantalla de resolución.

### Donde Entrar

- Sincronizar con los datos de Listas (frontend/dbextern/controller/sincro_index.php)
- **Legacy:** vsm/dagd/vsg/dre > … > Actualizar datos desde BDU (según `tipo`)
- **Pills2:** PERSONAS > Numerarios / Agregados / Supernumerarios > Actualizar datos desde BDU

### Tareas Habituales

#### Ver la situación de la sincronización

1. Abrir **Actualizar datos desde BDU** y elegir el colectivo.
2. Aparece la tabla de 9 puntos con el número de casos de cada uno y los enlaces **ver** o **ejecutar**.
3. Si el usuario no tiene permiso o no se encuentra la delegación en listas, sale el aviso correspondiente.

### Errores O Avisos Frecuentes

- `No se encontró la delegación en listas`
- `no tiene permisos`
- `No existe la clase de la persona`

### Permisos

- tipo=n` → `have_perm_oficina('sm')
- tipo=a` → `have_perm_oficina('agd')
- tipo=s` → `have_perm_oficina('sg')
- tipo=sssc` → `have_perm_oficina('des')
- Vía `$_SESSION['oPerm']` (`XPermisos`).

### Referencias Internas

- Flujo: `dbextern.sincro_index.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_index.md`

## Sincronizar las fichas unidas

### Para Que Sirve

Actualizar en Aquinate los datos de todas las personas ya vinculadas a la BDU en la delegación.

### Donde Entrar

- Sincronizar con los datos de Listas (frontend/dbextern/controller/sincro_index.php)
- sin entrada de menú en el índice (acción embebida en `sincro_index`)

### Tareas Habituales

#### Sincronizar las fichas unidas

1. En la pantalla de sincronización, pulsar **ejecutar** en el punto 1.
2. Al terminar sale un aviso con el resumen; los fallos de algunas personas se indican en el mismo aviso.

### Errores O Avisos Frecuentes

- `Mensajes de syncro por persona (dentro de mensaje, no siempre success: false)`

### Permisos

- HashF (`h1`) en `sincro_index`; permisos de colectivo ya validados en bootstrap.

### Referencias Internas

- Flujo: `dbextern.sincro_syncro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_syncro.md`

## Traer una ficha a esta dl

### Para Que Sirve

- Traer una ficha a la delegación actual.
- La fecha de traslado queda en el día de hoy.

### Donde Entrar

- Traslados desde otra DL (frontend/dbextern/controller/ver_traslados.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Traer una ficha a esta dl

1. En el punto 2, pulsar **trasladar** en la persona.
2. Aparece un aviso sobre la fecha del traslado (hoy); aceptarlo.
3. Si se ha hecho bien, la fila aparece tachada.

### Errores O Avisos Frecuentes

- `Mensajes del dominio Trasladar / Error al trasladar`

### Permisos

- HashF en `ver_traslados.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_trasladar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_trasladar.md`

## Trasladar una ficha a otra dl

### Para Que Sirve

Mover la ficha de Aquinate a la delegación donde la persona está en las listas de la BDU.

### Donde Entrar

- Trasladar a otra DL (frontend/dbextern/controller/ver_orbix_otradl.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Trasladar una ficha a otra dl

1. En el punto 7, pulsar **trasladar** en la persona.
2. Solo se puede si la región de destino es la misma que la actual; si no, avisa «Este traslado debe hacerse desde el dossier de traslados».

### Errores O Avisos Frecuentes

- `No se encontró la delegación destino`
- `Este traslado debe hacerse desde el dossier de traslados`

### Permisos

- HashF en `ver_orbix_otradl.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_trasladar_a.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_trasladar_a.md`

## Unir una persona de la BDU con una de Aquinate

### Para Que Sirve

Confirmar la correspondencia entre una persona de la BDU y una de Aquinate cuando el sistema propone candidatos (puntos 4 y 9).

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- Personas Aquinate sin BDU (frontend/dbextern/controller/ver_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Unir una persona de la BDU con una de Aquinate

1. En la pantalla de revisión, comprobar que el candidato propuesto es la misma persona.
2. Pulsar **unir**.
3. La persona sale de la cola y pasa a la siguiente.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- HashF en pantallas `ver_listas` / `ver_orbix`.

### Referencias Internas

- Flujo: `dbextern.sincro_unir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_unir.md`

## Revisar fichas Aquinate con la BDU vacía

### Para Que Sirve

Revisar las fichas de Aquinate cuya correspondencia en la BDU ya no existe.

### Donde Entrar

- Aquinate con BDU desaparecida (frontend/dbextern/controller/ver_desaparecidos_de_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Revisar fichas Aquinate con la BDU vacía

1. Pulsar **ver** en el punto 8.
2. Aparece la tabla de personas, con la acción **baja** y un enlace para hacer el traslado externo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_desaparecidos_de_listas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_desaparecidos_de_listas.md`

## Revisar personas de la BDU sin ficha Aquinate

### Para Que Sirve

Revisar las personas de la BDU que tienen vínculo pero ya no tienen ficha activa en esta delegación.

### Donde Entrar

- BDU desaparecidas en Aquinate (frontend/dbextern/controller/ver_desaparecidos_de_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Revisar personas de la BDU sin ficha Aquinate

1. Pulsar **ver** en el punto 3.
2. Aparece la tabla de personas con la acción **desunir**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_desaparecidos_de_orbix.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_desaparecidos_de_orbix.md`

## Revisar las personas de la BDU no unidas

### Para Que Sirve

Revisar una a una las personas de la BDU que no están unidas a ninguna ficha, viendo las posibles coincidencias en Aquinate.

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Revisar las personas de la BDU no unidas

1. Pulsar **ver** en el punto 4: en la primera carga se hacen las uniones automáticas evidentes.
2. Aparece la primera persona pendiente con sus candidatos de Aquinate.
3. Elegir **unir** con un candidato o **crear nuevo** si no hay coincidencia.
4. Usar los botones anterior y siguiente para moverse por la lista.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; acceso desde pantalla `ver_listas` abierta desde `sincro_index` (permisos ya

### Referencias Internas

- Flujo: `dbextern.ver_listas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_listas.md`

## Revisar las personas de Aquinate sin BDU

### Para Que Sirve

Revisar las personas activas de Aquinate sin correspondencia en la BDU y unirlas si hay candidato.

### Donde Entrar

- Personas Aquinate sin BDU (frontend/dbextern/controller/ver_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Revisar las personas de Aquinate sin BDU

1. Pulsar **ver** en el punto 9.
2. Aparece la primera persona con sus posibles candidatos de la BDU.
3. Pulsar **unir** si hay coincidencia.
4. Usar anterior y siguiente para recorrer la lista.

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`

### Permisos

- Sin control propio en el caso de uso.

### Referencias Internas

- Flujo: `dbextern.ver_orbix.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_orbix.md`

## Revisar los traslados del punto 7

### Para Que Sirve

Ver quién está activo en esta delegación pero tiene su correspondencia en la BDU en otra delegación.

### Donde Entrar

- Trasladar a otra DL (frontend/dbextern/controller/ver_orbix_otradl.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Revisar los traslados del punto 7

1. Pulsar **ver** en el punto 7.
2. Aparece la tabla con el nombre, la delegación de destino y la acción **trasladar**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_orbix_otradl.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_orbix_otradl.md`

## Revisar los traslados del punto 2

### Para Que Sirve

Identificar a quién hay que trasladar a esta delegación desde otra.

### Donde Entrar

- Traslados desde otra DL (frontend/dbextern/controller/ver_traslados.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Revisar los traslados del punto 2

1. Pulsar **ver** en el punto 2.
2. Aparece la tabla con el nombre, la delegación actual y la acción **trasladar**.

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_traslados.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_traslados.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/dbextern/flujos/`.
