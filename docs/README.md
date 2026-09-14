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
| [`guias/`](guias/) | Guías de procesos ([submodule](#submodule-docs-guias) `orbix_docs`) | Usuarios internos |
| [`scripts/`](scripts/) | Generadores de documentación | Desarrollo |

## Documentos de entrada

### Supervisión técnica (Aquinate)

Tres documentos para presentar el estado actual a técnicos que supervisan o replantean el sistema. No sustituyen `AGENTS.md` ni los manuales de usuario.

| Documento | Contenido |
|-----------|-----------|
| [Arquitectura técnica](dev/supervision_arquitectura.md) | Front/back, lenguajes, infra, tests, calidad, instalaciones |
| [Acceso y autorización](dev/supervision_acceso_autorizacion.md) | Login, 2FA, roles, DMZ, menús, permisos por fase |
| [Módulos, menús y procesos](dev/supervision_modulos_menus_procesos.md) | Cómo se implementa un módulo, menús, catálogo, diagramas |

### Resto

- [Qué es Orbix](QUE_ES_ORBIX.md) — visión global
- [Guía técnica (onboarding)](dev/guia_tecnica_onboarding.md) — PHP, DDD, tests, PostgreSQL/esquemas/réplicas
- [Índice de módulos](00_indice_modulos.md) — enlaces por módulo
- [Índice de refactorización](dev/REFACTOR_INDICE.md) — estado migración DDD
- [Copias entre bases](dev/copias_entre_bases.md) — tablas `cp_*` / `cd_*` / `cu_*` en comun: sincronización, reconciliación, cron unificado
- [Zonas SACD a comun (plan)](dev/zonas_a_comun_plan.md) — mover `zonas*` de sv-e a comun y tabla de relación `zonas_ctr`
- [Traducciones gettext](dev/traducciones_gettext.md) — Poedit, plantilla `.pot`, scripts IA
- [Plan de documentación](PLAN_DOCUMENTACION_MODULOS.md)
- [Handoff repaso manual/catálogo (en curso)](dev/MANUAL_REPASO_HANDOFF.md)
- [Cambios STGR plan 2026](manual/CambiosStgr2026.md) — notas/acta, tessera, convalidaciones (comunicación a usuarios)
- [Guía de inicio: plan de misas](manual/misas_inicio.md) — usuario `p-sacd`, zonas, consulta vs organización

## Regenerar documentación de un módulo

```bash
docs/scripts/generar_documentacion_modulo.sh <modulo> --force
```

## Submodule `docs/guias`

Las **guías de proceso** (oficina, lenguaje de usuario) viven en el repositorio [olvidio/orbix_docs](https://github.com/olvidio/orbix_docs) y se anidan aquí como Git submodule.

Al clonar Orbix:

```bash
git clone --recurse-submodules git@orbix:olvidio/orbix.git
```

Si el clone ya existía sin el submodule:

```bash
git submodule update --init --recursive
```

Editar una guía: commit y push **dentro de `docs/guias`** (repo `orbix_docs`), luego en Orbix `git add docs/guias` para actualizar el puntero. Regenerar `_referencia_menus.md` escribe en el submodule; hay que commitearlo allí.

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
