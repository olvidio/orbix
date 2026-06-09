---
id: "ubiscamas.update_solo_vip"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/update_solo_vip"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/update_solo_vip.php"
entrada: ["post.ctx:string", "post.id_activ:integer", "post.solo_vip:string"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["ubiscamas", "update", "solo", "vip"]
estado_revision: "generado"
---

# Update Solo Vip

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/update_solo_vip`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/update_solo_vip.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | controller | No | controller |
| `id_activ` | `integer` | controller | No | controller |
| `solo_vip` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.