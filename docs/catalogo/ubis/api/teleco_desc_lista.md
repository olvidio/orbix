---
id: "ubis.teleco_desc_lista"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_desc_lista"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_desc_lista.php"
entrada: ["post.id_tipo_teleco:integer"]
entrada_obligatoria: ["id_tipo_teleco"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_TelecoDescListaData"
respuesta_data: ["a_desc:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/teleco_desc_lista_ajax.php"]
casos_uso: ["src\\ubis\\application\\TelecoDescLista"]
tags: ["ubis", "teleco", "desc", "lista"]
estado_revision: "revisado"
errores: []
---

# Teleco Desc Lista

Devuelve descripciones de telecomunicación dependientes del tipo seleccionado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve descripciones de telecomunicación dependientes del tipo seleccionado.

## Endpoint

- URL: `/src/ubis/teleco_desc_lista`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_desc_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_teleco` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_desc`: map id_desc=>descripción según tipo teleco

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\TelecoDescLista`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/teleco_desc_lista_ajax.php"]`).
