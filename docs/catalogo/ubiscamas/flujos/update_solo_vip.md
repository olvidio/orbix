---
id: "ubiscamas.update_solo_vip.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Filtrar las camas VIP"
capacidad: "ubiscamas.update_solo_vip.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/ubiscamas/update_solo_vip"]
estado_revision: "revisado"
---

# Flujo - Update Solo Vip

## Objetivo De Usuario

Mostrar solo las camas VIP en la distribución de habitaciones.

## Punto De Entrada

Pantalla de habitaciones de la actividad.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Filtrar las camas VIP

Pasos propuestos:
1. Marcar la casilla «solo VIP».
2. La lista muestra solo las camas VIP; desmarcarla para ver todas.

Endpoints asociados:
- `/src/ubiscamas/update_solo_vip`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubiscamas/update_solo_vip`

## Errores Conocidos

- `Operación no autorizada`
- `Actividad no encontrada`
- `Error al guardar el estado VIP de la actividad`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
