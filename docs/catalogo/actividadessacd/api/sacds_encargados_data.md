---
id: "actividadessacd.sacds_encargados_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacds_encargados_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacds_encargados_data.php"
entrada: ["post.dl_org:string", "post.id_activ:integer", "post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdsEncargadosData"]
tags: ["actividadessacd", "sacds", "encargados", "data"]
estado_revision: "generado"
---

# Sacds Encargados Data

Endpoint backend: devuelve los sacd encargados actuales de una actividad en un array serializable, junto con los flags de permiso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/sacds_encargados_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacds_encargados_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_org` | `string` | controller+application | No | controller+application |
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadessacd\application\SacdsEncargadosData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.