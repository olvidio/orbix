---
id: "usuarios.perm_activ_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/perm_activ_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/perm_activ_guardar.php"
entrada: ["post.afecta_a:array", "post.dl_propia:string", "post.fase_ref:array", "post.iactividad_val:string", "post.iasistentes_val:string", "post.id_item:integer", "post.id_tipo_activ:integer", "post.id_usuario:integer", "post.inom_tipo_val:string", "post.isfsv_val:string", "post.perm_off:array", "post.perm_on:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: []
tags: ["usuarios", "perm", "activ", "guardar"]
estado_revision: "generado"
---

# Perm Activ Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/perm_activ_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `afecta_a` | `array` | controller | No | controller |
| `dl_propia` | `string` | controller | No | controller |
| `fase_ref` | `array` | controller | No | controller |
| `iactividad_val` | `string` | controller | No | controller |
| `iasistentes_val` | `string` | controller | No | controller |
| `id_item` | `integer` | controller | No | controller |
| `id_tipo_activ` | `integer` | controller | No | controller |
| `id_usuario` | `integer` | controller | No | controller |
| `inom_tipo_val` | `string` | controller | No | controller |
| `isfsv_val` | `string` | controller | No | controller |
| `perm_off` | `array` | controller | No | controller |
| `perm_on` | `array` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/procesos/controller/usuario_perm_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.