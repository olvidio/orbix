---
id: "asistentes.asistente_plaza_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Asistente Plaza Asignar"
capacidad: "asistentes.asistente_plaza_asignar.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/asistentes/asistente_plaza_asignar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente Plaza Asignar

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Asignar plaza común a varios asistentes seleccionados.


## Punto De Entrada

Pantalla `lista_asistentes` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/asistente_plaza_asignar`

## Errores Conocidos

- ``falta id_activ``
- ``falta lista de seleccion``

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
