---
tipo: excepciones
fecha: 2026-05-21
---

# Excepciones de documentacion por modulo

Modulos que **no siguen** el pipeline completo por diseno.

| Modulo | Catalogo | Manual | OpenAPI | Motivo |
|--------|----------|--------|---------|--------|
| **permisos** | No | No | No | Solo capa dominio PHP; permisos HTTP documentados en `usuarios` y `_convenciones_api.md` |
| **devel_codegen** | No | No | No | `routes.php` vacio; generacion de codigo interna |
| **utils_database** | No | No | No | Herramienta CLI; sin controllers HTTP |
| **asignaturas** | Si (api) | Si (breve) | Si | 2 endpoints AJAX; sin `frontend/` propio |
| **tablonanuncios** | Si (api) | Si (breve) | Si | 1 endpoint; sin pantalla dedicada |

## Donde documentar permisos

- Convenciones: `docs/catalogo/_convenciones_api.md` (HashB, oficinas)
- Manual usuarios: login, roles, `perm_menu`, `perm_activ`
- Cada ficha API: campo permisos cuando aplique

## Regeneracion

Los modulos con excepcion no deben ejecutar `generar_documentacion_modulo.sh` hasta tener `routes.php` o frontend; usar generadores parciales (`generar_api_modulo_md.php`, etc.).
