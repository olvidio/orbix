---
tipo: relaciones_modulos
modulo: usuarios
estado_revision: revisado_parcial
---

# Modulos relacionados — usuarios

## Dependencias

| Modulo | Uso |
|--------|-----|
| permisos | Dominio permisos oficina (sin HTTP propio) |
| menus | Grupos menu, permisos por entrada |
| configuracion | Modulos registrados, apps |

## Dependientes

**Todos los modulos** — sesion, `have_perm_oficina`, preferencias, 2FA.

## Documentacion cruzada

- Manual: `docs/manual/usuarios.md`
- Convenciones: `docs/catalogo/_convenciones_api.md` (HashB, permisos)
- Legacy: `documentacion/Documentacion_Obix/usuarios/mapa_*.md`
- Excepciones: `docs/excepciones_modulos.md` (permisos)
