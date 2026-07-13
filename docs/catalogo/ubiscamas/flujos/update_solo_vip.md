---
id: "ubiscamas.update_solo_vip.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Gestionar Update Solo Vip"
capacidad: "ubiscamas.update_solo_vip.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/ubiscamas/update_solo_vip"]
estado_revision: "revisado"
---

# Flujo - Update Solo Vip

## Objetivo De Usuario

Alternar el filtro de solo camas VIP en la actividad (`desc_activ=camasVIP`).

## Punto De Entrada

Checkbox «solo VIP» en `lista_habitaciones.phtml`. Sin entrada de menú en el índice.

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

- `/src/ubiscamas/update_solo_vip`

## Errores Conocidos

- `Operación no autorizada`
- `Actividad no encontrada`
- `Error al guardar el estado VIP de la actividad`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
