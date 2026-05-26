---
id: "misas.ver_encargos_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_encargos_zona_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/ver_encargos_zona_data.php"
entrada: ["post.id_zona:integer", "post.orden:string"]
entrada_obligatoria: ["id_zona"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["id_zona:integer", "orden:string", "columns:array", "rows:array", "tipos_encargo:object", "centros:object", "idiomas:object"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\VerEncargosZonaData"]
tags: ["misas", "ver", "encargos", "zona", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Ver Encargos Zona Data

Grid de encargos de una zona (`id_tipo_enc >= 8100`) y metadatos para el modal de edición web.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Previo: [`modificar_encargos_data.md`](modificar_encargos_data.md)

## Endpoint

- URL: `/src/misas/ver_encargos_zona_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_encargos_zona_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_zona` | int | **Sí** | Zona de `a_opciones_zona` |
| `orden` | string | No | Default web `orden`; valores: `orden`, `prioridad`, `desc_enc` |

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `rows` | array | Filas con `encargo`, `id_enc`, `tipo_encargo`, `lugar`, `idioma_enc`, `orden`, `prioridad`, `observ`, … |
| `columns` | array | Definición SlickGrid (legacy); móvil puede ignorarla |
| `tipos_encargo`, `centros`, `idiomas` | object | Desplegables del modal web |

## Cliente de referencia

- `orbix-android`: `fetchVerEncargosZona()` — `SimpleRowsTable` con columnas dinámicas de `rows[]`.
