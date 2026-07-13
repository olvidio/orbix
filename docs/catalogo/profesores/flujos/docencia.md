---
id: "profesores.docencia.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Ver docencia global"
capacidad: "profesores.docencia.gestionar"
pantallas_principales: ["profesores.pantalla.docencia"]
fragmentos: []
acciones: ["consultar"]
endpoints: ["/src/profesores/docencia"]
estado_revision: "revisado"
---

# Flujo - Ver docencia global

Consulta del registro histórico de docencia STGR de todo el claustro.

## Objetivo De Usuario

Revisar qué docencia consta registrada por profesor, curso, asignatura y acta.

## Punto De Entrada

Pantalla `docencia` (`frontend/profesores/controller/docencia.php`).

## Escenarios Inferidos

### Consultar

Pasos:
1. Abrir **ver docencia** desde el menú `stgr2`.
2. Revisar la tabla `tabla_docencia`.

Endpoints asociados:
- `/src/profesores/docencia`

Relacionado: alimentar datos con **actualizar docencia** (`actividadestudios`) al cerrar el CA.

## Endpoints Del Flujo

- `/src/profesores/docencia`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > ver docencia
- **Pills2:** sin entrada en el índice
