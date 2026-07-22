# Documentación Orbix

Todo el conocimiento del proyecto vive bajo `docs/`. No existe la carpeta `documentacion/` (eliminada).

## Estructura

| Carpeta | Contenido | Audiencia |
|---------|-----------|-----------|
| [`catalogo/`](catalogo/) | API, OpenAPI, pantallas, flujos (generado) | Desarrollo, IAs |
| [`manual/`](manual/) | Manuales de usuario | Usuarios finales |
| [`ai/`](ai/) | Resúmenes para RAG local | Asistentes IA |
| [`dev/`](dev/) | Refactor, baselines, arquitectura interna | Desarrollo |
| [`dev/reports/`](dev/reports/) | Informes JSON de herramientas | Desarrollo |
| [`legacy/obix/`](legacy/obix/) | Bóveda Obsidian legacy (solo consulta) | Referencia histórica |
| [`guias/`](guias/) | Guías de procesos (repo Git aparte) | Usuarios internos |
| [`scripts/`](scripts/) | Generadores de documentación | Desarrollo |

## Documentos de entrada

- [Qué es Orbix](QUE_ES_ORBIX.md) — visión global
- [Índice de módulos](00_indice_modulos.md) — enlaces por módulo
- [Índice de refactorización](dev/REFACTOR_INDICE.md) — estado migración DDD
- [Traducciones gettext](dev/traducciones_gettext.md) — Poedit, plantilla `.pot`, scripts IA
- [Plan de documentación](PLAN_DOCUMENTACION_MODULOS.md)
- [Cambios STGR plan 2026](manual/CambiosStgr2026.md) — notas/acta, tessera, convalidaciones (comunicación a usuarios)

## Regenerar documentación de un módulo

```bash
docs/scripts/generar_documentacion_modulo.sh <modulo> --force
```

## Dónde escribir documentación nueva

| Si el documento… | Carpeta |
|------------------|---------|
| Es catálogo API / OpenAPI / pantallas (generado) | `docs/catalogo/` |
| Es manual de usuario | `docs/manual/` |
| Es ayuda para IA local | `docs/ai/` |
| Es baseline de migración o arquitectura interna | `docs/dev/` |
| Es informe JSON de una herramienta | `docs/dev/reports/` |
| Es mapa legacy Obix (no escribir nuevo) | `docs/legacy/obix/` |

Regla Cursor: [`.cursor/rules/docs-layout.mdc`](../.cursor/rules/docs-layout.mdc).
