---
id: "dossiers.dossiers_ver_pantalla_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/dossiers_ver_pantalla_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/dossiers_ver_pantalla_data.php"
entrada: ["post.clase_info:string", "post.id_activ:integer", "post.id_dossier:string", "post.id_pau:integer", "post.mod:string", "post.modo_curso:integer", "post.obj_pau:string", "post.pau:string", "post.permiso:string", "post.que:string", "post.queSel:string", "post.refresh:integer", "post.restored_id_sel:mixed", "post.restored_scroll_id:integer", "post.scroll_id:integer", "post.sel:mixed", "post.stack:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/dossiers_ver.php"]
casos_uso: ["src\\dossiers\\application\\DossiersVerPantallaData"]
tags: ["dossiers", "ver", "pantalla", "data"]
estado_revision: "generado"
---

# Dossiers Ver Pantalla Data

Cuerpo de dossiers_ver: datos de cabecera + lista o ficha.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dossiers/dossiers_ver_pantalla_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/dossiers_ver_pantalla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | application | No | application |
| `id_activ` | `integer` | application | No | application |
| `id_dossier` | `string` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `mod` | `string` | application | No | application |
| `modo_curso` | `integer` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `pau` | `string` | application | No | application |
| `permiso` | `string` | application | No | application |
| `que` | `string` | application | No | application |
| `queSel` | `string` | application | No | application |
| `refresh` | `integer` | application | No | application |
| `restored_id_sel` | `mixed` | application | No | application |
| `restored_scroll_id` | `integer` | application | No | application |
| `scroll_id` | `integer` | application | No | application |
| `sel` | `mixed` | application | No | application |
| `stack` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Cuerpo de dossiers_ver: datos de cabecera + lista o ficha.

## Casos De Uso

- `src\dossiers\application\DossiersVerPantallaData`

## Frontend Relacionado

- `frontend/dossiers/controller/dossiers_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.