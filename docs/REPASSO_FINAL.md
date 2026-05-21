---
tipo: repaso_final
fecha: 2026-05-21
estado: cerrado_parcial
---

# Repaso final — documentacion modulos Orbix

Auditoria ejecutada al cerrar el plan en `docs/PLAN_DOCUMENTACION_MODULOS.md`.

## Resumen ejecutivo

| Metrica | Valor | Objetivo plan |
|---------|------:|---------------|
| Carpetas catalogo | 33 | 33 con API (+3 excepciones) |
| Manuales | 33 | 33 modulos con endpoints/FE |
| OpenAPI | 33 | 1 por modulo con endpoints |
| `modulos_relacionados.md` | 33 | 33 (incl. hubs) |
| Indices AI (`docs/ai/*/00_indice.md`) | 33 | 33 |
| Mapas legacy indexados | 4 modulos + parcial | Ampliacion continua |

**Estado global:** pipeline completado para todos los modulos con HTTP API. Revision manual en `revisado_parcial` salvo pilotos ola 0 y modulos API-only minimos.

---

## A. Cobertura

| Criterio | Estado | Notas |
|----------|--------|-------|
| 36 modulos listados | OK | Ver tabla plan |
| Catalogo `api/` | OK (33) | Excepciones: permisos, devel_codegen, utils_database |
| `pantallas/` o nota solo-API | OK | asignaturas/tablonanuncios: pantalla virtual `solo_api` |
| `openapi.yaml` | OK (33) | Generado; validacion CLI bloqueada en entorno |
| `docs/manual/*.md` | OK (33) | Todos `revisado_parcial` minimo |
| `docs/ai/*/00_indice.md` | OK (33) | asignaturas/tablonanuncios regenerados 2026-05-21 |

---

## B. Calidad transversal

| Criterio | Estado | Notas |
|----------|--------|-------|
| `_convenciones_api.md` | OK | Enlazado desde manuales hub y usuarios |
| Endpoints huerfanos | **Aceptado** | 31 modulos normalizados: consumo AJAX/forms documentado |
| Errores en manuales | Parcial | Tablas en pilotos; resto hereda generador |
| Objetivos flujo castellano | Parcial | Flujos auto-generados; revision manual pendiente en olas 4–6 |

### B.1 Patron de endpoints «huerfanos»

En Orbix es habitual que endpoints aparezcan sin controller dedicado porque se consumen desde:

- `PostRequest::getDataFromUrl` en `.phtml` / `.twig`
- Forms embebidos (`*_form.js`, widgets dossier)
- Login/sesion (`usuarios`)

Accion del repaso: todas las secciones «Endpoints sin pantalla directa ni capacidad relacionada» en `relaciones/pantallas_api.md` apuntan a este documento y declaran **Ninguno** (lista duplicada eliminada).

---

## C. Conexiones entre modulos

| Criterio | Estado | Notas |
|----------|--------|-------|
| `modulos_relacionados.md` | OK (33) | 12 creados en repaso final |
| `tipos_dossier.md` | Parcial | 5 tipos; ampliar con baselines |
| `legacy_mapping.md` | Parcial | actividadtarifas, zonassacd, actividadcargos + olas 1–4 |
| Grafo configuracion | Documentado | En plan § Conexiones |

---

## D. Publicacion

| Criterio | Estado | Notas |
|----------|--------|-------|
| OpenAPI Redocly | **Bloqueado** | `npx` falla: `ENOENT` en path Cursor helpers (Node 20.9) |
| OpenAPI Generator validate | **Bloqueado** | Mismo entorno; usar `--skip-openapi-validation` |
| `estado_revision` manuales | Parcial | Ola 0: revisado_parcial+; olas 4–6: revisado_parcial |
| `docs/00_indice_modulos.md` | OK | Actualizado en repaso |

### Validar OpenAPI cuando el entorno lo permita

```bash
# Con NVM en PATH limpio (ver validar_openapi.sh)
docs/scripts/validar_openapi.sh actividadtarifas
```

---

## E. Excepciones aceptadas

Ver `docs/excepciones_modulos.md`.

---

## Modulos por ola (estado repaso)

| Ola | Modulos | Catalogo | Manual | Cross-ref |
|-----|---------|----------|--------|-----------|
| 0 | actividadtarifas, actividadcargos | OK | REV+ | OK |
| 1 | zonassacd … profesores (7) | OK | REV | OK |
| 2 | pasarela … misas (8) | OK | REV | OK |
| 3 | actividades, personas, dossiers, ubis | OK | REV | OK |
| 4 | notas … menus (6) | OK | REV | OK (repaso) |
| 5 | devel_db_admin, dbextern, shared, configuracion | OK | REV breve | OK (repaso) |
| 6 | asignaturas, tablonanuncios | OK | REV breve | OK |
| — | permisos, devel_codegen, utils_database | Excepcion | — | excepciones_modulos.md |

---

## Pendiente post-repaso (no bloqueante)

1. Validar OpenAPI en CI o maquina con Node/npm sano.
2. Ampliar `legacy_mapping.md` modulo a modulo (120 mapas Obix disponibles).
3. Completar `tipos_dossier.md` desde baselines `Select*`.
4. Pasar manuales ola 4–6 de `revisado_parcial` a `revisado` tras prueba usuario.
5. Revisar objetivos de flujo auto-generados (lenguaje usuario).

---

## Comando de regeneracion

```bash
docs/scripts/generar_documentacion_modulo.sh <modulo> --force --skip-openapi-validation
```

**No regenerar** manuales ya revisados sin backup — el generador sobrescribe ediciones manuales.
