---
id: "ubiscamas.update_cama_asistente.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Asignar una cama a un asistente"
capacidad: "ubiscamas.update_cama_asistente.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/ubiscamas/update_cama_asistente"]
estado_revision: "revisado"
---

# Flujo - Update Cama Asistente

## Objetivo De Usuario

Guardar la asignación de cama a un asistente en la distribución de habitaciones.

## Punto De Entrada

Pantalla de habitaciones de la actividad.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Asignar una cama a un asistente

Pasos propuestos:
1. En la lista de habitaciones, usar las flechas de asignar o desasignar cama.
2. La asignación se guarda al momento.

Endpoints asociados:
- `/src/ubiscamas/update_cama_asistente`

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
