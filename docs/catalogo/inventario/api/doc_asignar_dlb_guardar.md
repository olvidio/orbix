---
id: "inventario.doc_asignar_dlb_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/doc_asignar_dlb_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/doc_asignar_dlb_guardar.php"
entrada: ["post.f_asignado:string", "post.f_recibido:string", "post.id_tipo_doc:string", "post.numerado:string", "post.str_selected_id:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/inventario/controller/doc_asignar_dlb.php"]
casos_uso: []
tags: ["inventario", "doc", "asignar", "dlb", "guardar"]
estado_revision: "generado"
---

# Doc Asignar Dlb Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/doc_asignar_dlb_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/doc_asignar_dlb_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_asignado` | `string` | controller | No | controller |
| `f_recibido` | `string` | controller | No | controller |
| `id_tipo_doc` | `string` | controller | No | controller |
| `numerado` | `string` | controller | No | controller |
| `str_selected_id` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/inventario/controller/doc_asignar_dlb.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.