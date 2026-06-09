---
tipo: indice
titulo: Indice modulos documentacion
fecha: 2026-05-21
---

# Indice modulos — documentacion Orbix

| Modulo | Catalogo | Manual | AI | OpenAPI | Ola |
|--------|----------|--------|-----|---------|-----|
| actividadtarifas | [catalogo](catalogo/actividadtarifas/) | [manual](manual/actividadtarifas.md) | [ai](ai/actividadtarifas/) | si | 0 |
| actividadcargos | [catalogo](catalogo/actividadcargos/) | [manual](manual/actividadcargos.md) | [ai](ai/actividadcargos/) | si | 0 |
| zonassacd | [catalogo](catalogo/zonassacd/) | [manual](manual/zonassacd.md) | [ai](ai/zonassacd/) | si | 1 |
| cartaspresentacion | si | [manual](manual/cartaspresentacion.md) | [ai](ai/cartaspresentacion/) | si | 1 |
| cambios | si | [manual](manual/cambios.md) | [ai](ai/cambios/) | si | 1 |
| actividadplazas | si | [manual](manual/actividadplazas.md) | [ai](ai/actividadplazas/) | si | 1 |
| actividadessacd | si | [manual](manual/actividadessacd.md) | [ai](ai/actividadessacd/) | si | 1 |
| actividadescentro | si | [manual](manual/actividadescentro.md) | [ai](ai/actividadescentro/) | si | 1 |
| profesores | si | [manual](manual/profesores.md) | [ai](ai/profesores/) | si | 1 |
| pasarela … misas | si | si | si | si | 2 |
| actividades, personas, dossiers, ubis | si | si | si | si | 3 |
| notas, inventario, encargossacd, actividadestudios, usuarios, menus | si | si | si | si | 4 |
| devel_db_admin, dbextern, shared, configuracion | si | si | si | si/parcial | 5 |
| asignaturas, tablonanuncios | [catalogo](catalogo/asignaturas/) | si | si | si | 6 |
| permisos, devel_codegen, utils_database | — | — | — | — | excepcion |

Documentos transversales:

- **Resumen / presentación:** [QUE_ES_ORBIX.md](QUE_ES_ORBIX.md)
- Plan: [PLAN_DOCUMENTACION_MODULOS.md](PLAN_DOCUMENTACION_MODULOS.md)
- **Repaso final:** [REPASSO_FINAL.md](REPASSO_FINAL.md)
- Excepciones: [excepciones_modulos.md](excepciones_modulos.md)
- Convenciones API: [catalogo/_convenciones_api.md](catalogo/_convenciones_api.md)
- **Clientes nativos (Android):** [catalogo/_clientes_nativos.md](catalogo/_clientes_nativos.md) · [Endpoints móvil revisados](catalogo/_endpoints_cliente_movil.md)
- Legacy Obix: [legacy_mapping.md](legacy_mapping.md)

Pipeline: `docs/scripts/generar_documentacion_modulo.sh <modulo> --force --skip-openapi-validation`
