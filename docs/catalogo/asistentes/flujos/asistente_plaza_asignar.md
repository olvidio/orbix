---
id: "asistentes.asistente_plaza_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Asignar plaza a varios asistentes"
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

Asignar una plaza común a varios asistentes marcados a la vez.

## Punto De Entrada

Lista de asistentes de una actividad.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Asignar plaza a varios asistentes

Pasos propuestos:
1. En la lista de asistentes, marcar los que corresponda.
2. Elegir asignar plaza y la plaza que se quiere dar.
3. Confirmar.

Endpoints asociados:
- `/src/asistentes/asistente_plaza_asignar`

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
