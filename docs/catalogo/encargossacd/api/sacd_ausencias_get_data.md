---
id: "encargossacd.sacd_ausencias_get_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ausencias_get_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_get_data.php"
entrada: ["post.id_nom:integer", "post.historial:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["array_tipo_ausencias:object", "filas:array"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ausencias_get.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasGetData"]
tags: ["encargossacd", "sacd", "ausencias", "get", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Sacd Ausencias Get Data

Ausencias y tareas personales de un sacerdote (`id_nom`). Encargos con tipo `7…` / `4…`.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md)

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_get_data`
- Métodos: `POST` (recomendado)
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_get_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_nom` | int/string | **Sí** | ID del sacerdote |
| `historial` | int | No | `0` (default): solo vigentes (`f_ini >= hoy` o `f_fin` abierto). `1`: todas |

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `array_tipo_ausencias` | object | Tipos añadibles (`id_enc → descripción`) — edición web |
| `filas` | array | Ausencias existentes |

### Elemento `filas[]`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_enc`, `id_tipo_enc`, `desc_enc`, `id_item` | mixed | Identificadores |
| `inicio`, `fin` | string \| null | Fechas localizadas |
| `dedic_m`, `dedic_t`, `dedic_v` | string | Horario mañana / tarde / vespertina |

La mutación de fechas usa [`sacd_ausencias_update.md`](sacd_ausencias_update.md) (no implementada en móvil).

## Ejemplo

```http
POST /orbix/src/encargossacd/sacd_ausencias_get_data HTTP/1.1
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

id_nom=42&historial=0
```

```json
{
  "success": true,
  "data": "{\"array_tipo_ausencias\":{\"701\":\"Permiso\"},\"filas\":[{\"id_enc\":701,\"desc_enc\":\"Permiso\",\"id_item\":99,\"inicio\":\"2026-06-01\",\"fin\":\"2026-06-15\",\"dedic_m\":\"\",\"dedic_t\":\"\",\"dedic_v\":\"\"}]}"
}
```

## Cliente de referencia

- `orbix-android`: `fetchSacdAusencias()` — tarjetas por fila; toggle historial.
