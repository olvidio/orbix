---
id: "pasarela.exportar_actividades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Exportar actividades al exterior"
capacidad: "pasarela.exportar_actividades.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.exportar_select"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/exportar_actividades_data"
estado_revision: "revisado"
---

# Flujo - Exportar actividades al exterior

## Objetivo De Usuario

Generar listado tabular con datos de actividades filtradas para sistemas externos.

## Punto De Entrada

`exportar_que.php` (menú Pasarela).

## Escenarios

1. Elegir filtros y casas.
2. Submit → `exportar_select` calcula periodo ISO.
3. Muestra tabla; avisos en `errores` (tarifas/activación faltantes).

## Endpoints Del Flujo

- `/src/pasarela/exportar_actividades_data`

## Errores Conocidos

- `Periodo no válido`
- `valor no válido para la activación del tipo de actividad %s`
- `No está definido el tipo tarifa...`
- `No está definida la id_tarifa...`

## Ruta de menú

- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades
