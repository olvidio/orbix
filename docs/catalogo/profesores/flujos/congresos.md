---
id: "profesores.congresos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Consultar congresos"
capacidad: "profesores.congresos.gestionar"
pantallas_principales: ["profesores.pantalla.congresos"]
fragmentos: []
acciones: ["consultar"]
endpoints: ["/src/profesores/congresos"]
estado_revision: "revisado"
---

# Flujo - Consultar congresos

Consulta global de asistencia a congresos del claustro STGR.

## Objetivo De Usuario

Revisar congresos registrados por profesor (tipo, lugar, fechas, organizador).

## Punto De Entrada

Pantalla `congresos` (`frontend/profesores/controller/congresos.php`).

## Escenarios Inferidos

### Consultar

Pasos:
1. Abrir **asistencia a congresos** desde el menú `stgr2`.
2. Revisar la tabla `tabla_congreso`.

Endpoints asociados:
- `/src/profesores/congresos`

## Endpoints Del Flujo

- `/src/profesores/congresos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > asitencia a congresos
- **Pills2:** sin entrada en el índice
