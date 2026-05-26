---
id: "misas.ver_iniciales_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_iniciales_zona_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/ver_iniciales_zona_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: ["id_zona"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["id_zona:integer", "columns:array", "rows:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\VerInicialesZonaData"]
tags: ["misas", "ver", "iniciales", "zona", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Ver Iniciales Zona Data

Tabla de sacerdotes con iniciales y color por zona.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Previo: [`modificar_iniciales_sacd_zona_data.md`](modificar_iniciales_sacd_zona_data.md)

## Endpoint

- URL: `/src/misas/ver_iniciales_zona_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_iniciales_zona_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_zona` | int | **Sí** | Zona de `a_opciones` |

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `rows` | array | `{ id_sacd, nombre_sacd, iniciales, color }` — `color` hex 6 sin `#` o vacío |
| `columns` | array | SlickGrid (legacy) |

## Cliente de referencia

- `orbix-android`: `fetchVerInicialesZona()` — `SimpleRowsTable`.
