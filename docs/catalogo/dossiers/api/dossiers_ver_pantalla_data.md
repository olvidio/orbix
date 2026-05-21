---
id: "dossiers.dossiers_ver_pantalla_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/dossiers_ver_pantalla_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/dossiers_ver_pantalla_data.php"
entrada: ["post.clase_info:string", "post.id_activ:integer", "post.id_dossier:string", "post.id_pau:integer", "post.mod:string", "post.modo_curso:integer", "post.obj_pau:string", "post.pau:string", "post.permiso:string", "post.que:string", "post.queSel:string", "post.refresh:integer", "post.restored_id_sel:mixed", "post.restored_scroll_id:integer", "post.scroll_id:integer", "post.sel:mixed", "post.stack:mixed", "post.stack_actual:integer", "post.todos:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dossiers_DossiersVerPantallaDataData"
respuesta_data: ["top_data:array", "modo:'lista'|'ficha'"]
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/dossiers_ver.php"]
casos_uso: ["src\\dossiers\\application\\DossiersVerPantallaData"]
tags: ["dossiers", "ver", "pantalla", "data"]
estado_revision: "generado"
---

# Dossiers Ver Pantalla Data

Cuerpo de dossiers_ver: datos de cabecera + lista o ficha. El backend NO firma URLs: devuelve `*_link_spec` ({path, query}) que firma el frontend. En modo ficha, `ficha_segmentos` mezcla: - Segmentos `html` ya generados por los `Select_*` (TODO: refactorizar para que tampoco lleven HTML/HashFront desde `src/`). - Segmentos `datos_tabla` con datos puros (`action_tabla_link_spec`, `ins_traslado_link_spec`, `script_ctx`, `hash`, `tabla`, `permiso`) que el frontend compone con HashFront, Lista y el script JS de `DatosTablaRepo`.

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
| `stack` | `mixed` | application | No | application |
| `stack_actual` | `integer` | application | No | application |
| `todos` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `dossiers_DossiersVerPantallaDataData`):
  - `top_data` (`array`)
  - `modo` (`'lista'|'ficha'`)

## Efectos colaterales

- Cuerpo de dossiers_ver: datos de cabecera + lista o ficha.
- @return array{ error?: string, top_data: array{web_icons: string, alt_dossiers: string, txt_dossiers: string, nom_cabecera: string, go_dossiers_link_spec: array{path: string, query: array<string, mixed>}, go_home_link_spec?: array{path: string, query: array<string, mixed>}}, modo: 'lista'|'ficha', lista_a_filas?: list<array<string, mixed>>, ficha_segmentos?: list<array<string, mixed>>, aviso?: string }

## Casos De Uso

- `src\dossiers\application\DossiersVerPantallaData`

## Frontend Relacionado

- `frontend/dossiers/controller/dossiers_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.