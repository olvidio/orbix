---
id: "encargossacd.sacd_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_select_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_select_data.php"
entrada: ["post.filtro_sacd:string", "post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["opciones:object", "selected:integer", "label_prefix:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdSelectData"]
tags: ["encargossacd", "sacd", "select", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Sacd Select Data

Desplegable de sacerdotes filtrados por tabla (`id_tabla`), usado en la ficha SACD y en **Ausencias SACD** (`sacd_ausencias.php` vía `sacd_ficha_ajax?que=get_select`).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Ausencias: [`sacd_ausencias_get_data.md`](sacd_ausencias_get_data.md)

## Endpoint

- URL: `/src/encargossacd/sacd_select_data`
- Métodos: `POST` (recomendado)
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_select_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `filtro_sacd` | string | No | `n`, `a` (agd), `sssc` (sss+), `cp_sss` (cp) — ver `SacdFichaAjaxHashes::opcionesFiltroSacd()` |
| `id_nom` | int | No | SACD preseleccionado; `0` si ninguno |

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `opciones` | object | Mapa `id_nom → nombre` |
| `selected` | int | Eco de `id_nom` |
| `label_prefix` | string | Etiqueta HTML legacy; móvil puede ignorar |

## Ejemplo

```http
POST /orbix/src/encargossacd/sacd_select_data HTTP/1.1
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

filtro_sacd=n&id_nom=0
```

## Cliente de referencia

- `orbix-android`: `fetchSacdSelectPage()` — menú `sacd_ausencias.php`, modo `SacdLista`.
