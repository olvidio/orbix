---
id: "profesores.pantalla.docencia"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "profesores"
nombre: "Ver docencia"
controller: "frontend/profesores/controller/docencia.php"
vistas: ["frontend/profesores/view/docencia.phtml"]
fragmentos_frontend: []
endpoints: ["/src/profesores/docencia"]
capacidades: ["profesores.docencia.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Ver docencia

Tabla global de docencia STGR: delegación (RSTGR), profesor, curso de inicio, asignatura, modo y
acta. Datos alimentados por **actualizar docencia** al cerrar cursos.

## Tipo

- Subtipo: `pantalla_principal` (menú `stgr2`)
- Controller: `frontend/profesores/controller/docencia.php`

## Vistas Relacionadas

- `frontend/profesores/view/docencia.phtml`

## Endpoints Usados

- `/src/profesores/docencia`

## Manual De Usuario

1. Abrir **ver docencia** desde el menú (ámbito `stgr2`).
2. Consultar la tabla de registros históricos.

Pantalla de solo consulta, sin filtros ni mutaciones.

## Ruta de menú

- **Legacy:** vest > actas... > ver docencia
- **Pills2:** sin entrada en el índice
