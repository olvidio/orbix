---
id: "ubiscamas.update_cama_asistente.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Gestionar Update Cama Asistente"
capacidad: "ubiscamas.update_cama_asistente.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/ubiscamas/update_cama_asistente"]
estado_revision: "revisado"
---

# Flujo - Update Cama Asistente

## Objetivo De Usuario

Persistir la asignación cama↔asistente en la actividad actual (requiere token HashB).

## Punto De Entrada

Acción AJAX en `lista_habitaciones.phtml` (flechas asignar/desasignar cama). Sin entrada de menú en el índice.

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

- `/src/ubiscamas/update_cama_asistente`

## Errores Conocidos

- `Operación no autorizada`
- `Asistencia no encontrada para id_nom`
- `Error al guardar la asignación de la cama`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
