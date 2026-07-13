---
id: "actividadestudios.asistente_observ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Asistente Observ"
capacidad: "actividadestudios.asistente_observ.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/asistente_observ"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente Observ

Guardado del campo `observ` de un asistente en una actividad de estudios.

## Objetivo De Usuario

El usuario guarda el texto de observaciones generales (`observ`) de un asistente en una
actividad. Sustituye al case `observ` de `update_3103.php`.

## Punto De Entrada

Endpoint de mutación invocado por AJAX desde el dossier de matrículas de una actividad
(3103) en el sistema legacy. En el frontend actual **no hay referencia activa** a esta
URL (solo existe el caso de uso y la ruta en `src/`).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados en `frontend/` actual.

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Desde el contexto de un asistente en una actividad, editar el campo `observ`.
2. Enviar `id_activ`, `id_nom` (o `id_pau`) y `observ` al endpoint.
3. El sistema localiza al asistente y persiste el texto.

Endpoints asociados:
- `/src/actividadestudios/asistente_observ`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado en frontend actual.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/asistente_observ`

## Errores Conocidos

- ``falta id_activ o id_nom``
- ``hay un error, no se ha guardado``
- ``no encuentro al asistente``

## Ruta de menú

Sin entrada de menú en el índice. Subflujo previsto desde dossier 3103 (matrículas de una
actividad), accesible vía búsqueda de actividad CA.

- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n.
