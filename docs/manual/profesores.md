---
tipo: manual_usuario
modulo: profesores
flujos: 6
estado_revision: revisado_parcial
---

# Manual De Usuario - profesores

Datos de **profesores** STGR: congresos, docencia, asignacion a asignaturas.

## Acceso Por Menu (rol 12 STGR, 21)

| Texto en menu | Controller | Uso |
|---------------|------------|-----|
| **Asistencia a congresos** | `congresos.php` | Listado congresos asistidos |
| **Ver docencia** | `docencia.php` | Docencia del profesor |
| **Profesor para asignatura** | `profesor_asignatura_que.php` + ajax | Filtro y listado por asignatura |
| **Claustro** | `lista_por_departamentos.php` (legacy apps) | Listado por departamentos |
| **Tipos de profesor** | `shared/tablaDB_lista_ver.php` + `InfoProfesorTipo` | Catalogo tipos |

URLs canonicas: `frontend/profesores/controller/*` (ver `profesores_migracion_baseline.md`).

## Congresos

1. Abrir **Asistencia a congresos**.
2. Revisar tabla: dl (si ambito rstgr), apellidos/nombre, tipo, lugar, fechas.
3. Editar/alta segun botones de pantalla.

## Docencia

1. **Ver docencia**.
2. Consultar asignaturas/horas/cargos docentes del contexto seleccionado.

## Profesores Por Asignatura

1. **Profesor para asignatura** — filtros en `profesor_asignatura_que.php`.
2. Ejecutar busqueda (ajax `profesor_asignatura_ajax.php`).
3. Revisar profesores asignados a la asignatura.

## Claustro Y Tipos

- **Claustro** — listado por departamentos (migracion pendiente a frontend canonico).
- **Tipos de profesor** — mantenimiento tabla maestra via modulo **shared**.

## Modulos Relacionados

- **asignaturas** — API soporte
- **notas** — actas/docencia STGR
- **shared** — tablaDB generico

## Revision Pendiente

- Migrar **claustro** a `frontend/profesores` y actualizar menu.
- Ficha profesor STGR (`ficha_profesor_stgr` flujo) si tiene entrada menu aparte.
