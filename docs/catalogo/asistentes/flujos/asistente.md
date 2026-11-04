---
id: "asistentes.asistente.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar los asistentes de una actividad"
capacidad: "asistentes.asistente.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/asistentes/asistente_eliminar", "/src/asistentes/asistente_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver y gestionar los asistentes de una actividad: quién está, estado de su plaza, añadir, cambiar de actividad y quitar.

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **asistentes**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Gestionar los asistentes de una actividad

Pasos propuestos:
1. Marcar la actividad y pulsar **asistentes**. Se abre «Relación de asistentes» con un enlace **( dossiers )** y una tabla (nombre, dl, propio, estudios, observaciones, teléfono, mail, ctr, propietario de la plaza…).
2. El color de cada fila indica su estado: **pedida**, **en espera**, **asignada** o **confirmada** (la leyenda está debajo).
3. Marcar una o varias filas (Ctrl+clic o Mayús+clic para varias) y pulsar **modificar asistencia**, **cambiar actividad**, **borrar asistencia**, **transferir a históricos**, **añadir cargo**, **modificar cargo**, **quitar cargo**, **plan estudios** o **E43**.
4. Bajo la tabla se indican las plazas (por ejemplo «plazas actividad: 31» y «dlb: 1 (de las 1 cedidas por dlmE) => ocupadas=1/(1) libres=0»).
5. Para añadir a alguien de la dl, pulsar **+ n** o **+ agd** (según el colectivo) en «Añadir asistente de la dl».

Endpoints asociados:
- `/src/asistentes/asistente_eliminar`
- `/src/asistentes/asistente_guardar`

### Mover un asistente a otra actividad

Pasos propuestos:
1. Marcar al asistente y pulsar **cambiar actividad**.
2. Se abre «Mover a:» con las actividades del mismo tipo, cada una con sus «plazas libres/concedidas».
3. Elegir la actividad de destino y confirmar.

Endpoints asociados:
- `/src/asistentes/asistente_eliminar`
- `/src/asistentes/asistente_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/asistente_eliminar`
- `/src/asistentes/asistente_guardar`

## Errores Conocidos

- ``falta id_activ_old``
- ``faltan parametros id_activ / id_nom``
- ``hay un error, no se ha eliminado``
- ``hay un error, no se ha guardado``
- ``los datos de asistencia los modifica la dl del asistente``

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
