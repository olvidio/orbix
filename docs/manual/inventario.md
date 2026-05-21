---
tipo: manual_usuario
modulo: inventario
flujos: 43
estado_revision: revisado_parcial
---

# Manual De Usuario - inventario

**Documentacion** fisica, equipajes, colecciones, traslados.

## Acceso Por Menu (rol 4 Documentos)

| Texto | Controller |
|-------|------------|
| **Inventario** (raiz) | — |
| **Asignar documento** | `docs_asignar_que.php` |
| **Nuevo equipaje** | `equipajes_nuevo.php` |
| **Hacer / imprimir / eliminar equipajes** | `equipajes_ver.php` |
| **Movimientos maletas** | `equipajes_movimientos_que.php` |
| **Buscar inventario** | `inventario_que.php` |
| Docs pendientes/perdidos/observ. | `docs_en_busqueda`, `docs_perdidos`, etc. |
| **Traslado doc** | `traslado_doc_que.php` |
| Tablas maestras | `shared/tablaDB` + `InfoColeccion`, `InfoTipoDoc`, … |

## Asignar Y Buscar Documentos

1. **Asignar documento** — vincular doc a persona/centro.
2. **Buscar** por centro o delegacion.
3. Listas pendientes, perdidos, con observaciones.

## Equipajes

1. Crear equipaje, anadir documentos.
2. Imprimir listado, registrar movimientos.
3. Eliminar equipaje (param `eliminar=1`).

## Modulos Relacionados

ubis (centros), personas, shared (tablas maestras).

Obix: `inventario/mapa_*`.
