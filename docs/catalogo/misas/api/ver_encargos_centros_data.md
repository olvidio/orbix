---
id: "misas.ver_encargos_centros_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_encargos_centros_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/ver_encargos_centros_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: ["id_zona"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["id_zona:integer", "columns:array", "rows:array", "a_opciones_zona:object", "a_centros_zona:object"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\VerEncargosCentrosData"]
tags: ["misas", "ver", "encargos", "centros", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Ver Encargos Centros Data

Relación encargo ↔ centro visible para cada fila de la zona. El desplegable dinámico de encargos del modal web usa [`desplegable_encargos.md`](desplegable_encargos.md), no este endpoint.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Previo: [`modificar_encargos_centros_data.md`](modificar_encargos_centros_data.md)

## Endpoint

- URL: `/src/misas/ver_encargos_centros_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_encargos_centros_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_zona` | int | **Sí** | Zona seleccionada |

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `rows` | array | `{ id_item, id_encargo, encargo, id_centro, centro }` |
| `a_opciones_zona` | object | Todas las zonas (filtro modal) |
| `a_centros_zona` | object | Centros de la zona actual |
| `columns` | array | SlickGrid (legacy) |

## Cliente de referencia

- `orbix-android`: `fetchVerEncargosCentros()` — tabla de filas.
