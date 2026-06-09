---
id: "certificados.certificado_recibido_modificar_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_modificar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_modificar_data.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_modificar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoRecibidoModificarFormData"]
tags: ["certificados", "certificado", "recibido", "modificar", "data"]
estado_revision: "generado"
---

# Certificado Recibido Modificar Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_recibido_modificar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_modificar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\certificados\application\CertificadoRecibidoModificarFormData`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_recibido_modificar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.