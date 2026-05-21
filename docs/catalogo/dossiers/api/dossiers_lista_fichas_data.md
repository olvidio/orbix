---
id: "dossiers.dossiers_lista_fichas_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/dossiers_lista_fichas_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/dossiers_lista_fichas_data.php"
entrada: ["post.id_pau:mixed", "post.obj_pau:mixed", "post.pau:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dossiers_DossiersListaFichasDataData"
respuesta_data: ["a_filas:list<array<string, mixed>>, web_icons: string"]
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/lista_dossiers.php"]
casos_uso: ["src\\dossiers\\application\\DossiersListaFichasData"]
tags: ["dossiers", "lista", "fichas", "data"]
estado_revision: "generado"
---

# Dossiers Lista Fichas Data

Filas de la tabla de relación de dossiers (modo lista en dossiers_ver). `href_ver` / `href_abrir` se firman en el borde HTTP (ver `dossiers_lista_fichas_data.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dossiers/dossiers_lista_fichas_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/dossiers_lista_fichas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_pau` | `mixed` | controller | No | controller |
| `obj_pau` | `mixed` | controller | No | controller |
| `pau` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `dossiers_DossiersListaFichasDataData`):
  - `a_filas` (`list<array<string, mixed>>, web_icons: string`)

## Efectos colaterales

- Filas de la tabla de relación de dossiers (modo lista en dossiers_ver).
- `href_ver` / `href_abrir` se firman en el borde HTTP (ver `dossiers_lista_fichas_data.php`).

## Casos De Uso

- `src\dossiers\application\DossiersListaFichasData`

## Frontend Relacionado

- `frontend/dossiers/controller/lista_dossiers.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.