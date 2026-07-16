---
tipo: relaciones_modulos
modulo: notas
estado_revision: revisado_parcial
---

# Modulos relacionados — notas

## Dependencias

| Modulo | Uso |
|--------|-----|
| personas | Ficha alumno, actas por persona |
| asignaturas | Mapas y separadores (`asignaturas_map_data`, `asignaturas_con_separador_data`) |
| profesores | Examinadores, docencia STGR |
| actividadestudios | Matriculas, contexto curricular |
| actividades | Busqueda actividades en actas |
| certificados | Informes relacionados STGR |

## Dependientes

Ninguno directo; notas es consumidor de datos maestros.

## Documentacion cruzada

- Manual: `docs/manual/notas.md`
- Legacy: `docs/legacy/obix/notas/mapa_*.md`
- Baseline: `docs/dev/notas_migracion_baseline.md`
