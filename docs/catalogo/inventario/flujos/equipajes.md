---
id: "inventario.equipajes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Equipajes"
capacidad: "inventario.equipajes.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar"]
endpoints: ["/src/inventario/equipajes_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes

Propuesta generada automaticamente desde la capacidad `inventario.equipajes.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ciclo de vida de equipajes: alta (`equipajes_nuevo`), composición de maletas (EGM/Whereis), impresión y eliminación.

## Punto De Entrada

- `inventario.pantalla.equipajes_ver`



## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/inventario/equipajes_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_eliminar`

## Errores Conocidos

- ``falta id_equipaje``
- ``hay un error, no se ha eliminado``

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > hacer equipajes
- **Pills2:** —
