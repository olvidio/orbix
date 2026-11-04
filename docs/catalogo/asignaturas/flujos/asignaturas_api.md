---
id: "asignaturas.api_soporte.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asignaturas"
nombre: "Flujo - Datos de asignaturas"
pantallas_principales: ["asignaturas.pantalla.api_soporte"]
endpoints: ["/src/asignaturas/asignaturas_map_data", "/src/asignaturas/asignaturas_con_separador_data"]
estado_revision: "revisado"
---

# Flujo - API asignaturas

## Objetivo De Usuario

Datos de asignaturas que usan otros módulos (notas, profesores); no tiene pantalla propia.

## Endpoints

Ver `docs/catalogo/asignaturas/api/`.

## Punto De Entrada

Automático: no requiere acción del usuario.

## Escenarios Inferidos

### Datos de asignaturas

Pasos propuestos:
1. Al elegir una asignatura en notas o profesores, la lista se carga automáticamente desde este servicio.

Endpoints asociados:
- `/src/asignaturas/asignaturas_map_data`
- `/src/asignaturas/asignaturas_con_separador_data`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (API de soporte para notas/profesores)
- **Pills2:** sin entrada de menú en el índice (API de soporte para notas/profesores)
