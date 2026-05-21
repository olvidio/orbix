---
id: "cambios.cambio_usuario_objeto_pref_fases_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_fases_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_fases_data.php"
entrada: ["post.dl_propia:string", "post.id_tipo_activ:string", "post.objeto:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref_fases.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefFasesData"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "fases", "data"]
estado_revision: "generado"
---

# Cambio Usuario Objeto Pref Fases Data

Endpoint JSON: lista de fases para el tipo de actividad indicado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_fases_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_fases_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_propia` | `string` | controller+application | No | controller+application |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |
| `objeto` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefFasesData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref_fases.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.