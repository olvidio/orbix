---
tipo: manual_usuario
modulo: actividadestudios
flujos: 27
estado_revision: revisado_parcial
---

# Manual De Usuario - actividadestudios

**Matriculas**, docencia STGR, listados por curso.

## Acceso Por Menu (rol 12 STGR, 8)

| Texto | Controller |
|-------|------------|
| **Matricular a todos** | `matricular.php` |
| **Resto** (matriculas) | `matriculas_lista.php` |
| Actualizar docencia | `actualizar_docencia.php` |
| CA posibles | `ca_posibles_que.php` |

## Matricular

1. **Matricular a todos** — accion masiva en contexto curso/actividad.
2. Revisar resultado y errores en alert.

## Listados De Matriculas

1. **Matriculas lista** — filtrar por curso/tipo.
2. Editar estado matricula segun pantalla.

## Modulos Relacionados

actividades, notas, profesores, personas.

Legacy: `documentacion/actividadestudios_migracion_baseline.md`, Obix `actividadestudios/mapa_*`.
