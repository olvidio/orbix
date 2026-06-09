---
id: "certificados.certificado_recibido_guardar"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_guardar.php"
entrada: ["post.certificado:string", "post.certificado_old:string", "post.destino:string", "post.f_certificado:string", "post.f_recibido:string", "post.firmado:string", "post.id_item:integer", "post.id_nom:integer", "post.idioma:string", "post.nom:string", "post.nuevo:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_adjuntar.php", "frontend/certificados/controller/certificado_recibido_modificar.php"]
casos_uso: []
tags: ["certificados", "certificado", "recibido", "guardar"]
estado_revision: "generado"
---

# Certificado Recibido Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_recibido_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `certificado` | `string` | controller | No | controller |
| `certificado_old` | `string` | controller | No | controller |
| `destino` | `string` | controller | No | controller |
| `f_certificado` | `string` | controller | No | controller |
| `f_recibido` | `string` | controller | No | controller |
| `firmado` | `string` | controller | No | controller |
| `id_item` | `integer` | controller | No | controller |
| `id_nom` | `integer` | controller | No | controller |
| `idioma` | `string` | controller | No | controller |
| `nom` | `string` | controller | No | controller |
| `nuevo` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_recibido_adjuntar.php`
- `frontend/certificados/controller/certificado_recibido_modificar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.