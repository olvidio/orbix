---
id: "profesores.pantalla.congresos"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "profesores"
nombre: "Asistencia a congresos"
controller: "frontend/profesores/controller/congresos.php"
vistas: ["frontend/profesores/view/congresos.phtml"]
fragmentos_frontend: []
endpoints: ["/src/profesores/congresos"]
capacidades: ["profesores.congresos.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Asistencia a congresos

Tabla global de congresos del claustro: delegación (RSTGR), profesor, tipo, lugar, fechas y
organizador.

## Tipo

- Subtipo: `pantalla_principal` (menú `stgr2`)
- Controller: `frontend/profesores/controller/congresos.php`

## Vistas Relacionadas

- `frontend/profesores/view/congresos.phtml`

## Endpoints Usados

- `/src/profesores/congresos`

## Manual De Usuario

1. Abrir **asistencia a congresos** desde el menú (ámbito `stgr2`; el índice conserva el typo
   «asitencia»).
2. Consultar la tabla de asistencias registradas.

Pantalla de solo consulta.

## Ruta de menú

- **Legacy:** vest > actas... > asitencia a congresos
- **Pills2:** sin entrada en el índice
