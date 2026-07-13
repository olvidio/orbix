---
id: "certificados.certificado_recibido_delete"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_delete"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_delete.php"
entrada: ["post.id_item:integer", "post.sel:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/view/select_certificados_de_una_persona.phtml"]
casos_uso: ["src\\certificados\\domain\\CertificadoRecibidoDelete"]
tags: ["certificados", "certificado", "recibido", "delete"]
estado_revision: "revisado"
---

# Certificado Recibido Delete

Elimina un certificado recibido de la región STGR local.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra el registro por `id_item` (directo o desde `sel[0]` antes del `#`).

## Endpoint

- URL: `/src/certificados/certificado_recibido_delete`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_delete.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `string` | controller | No | Token `id_item` |
| `id_item` | `integer` | controller | No | Alternativa |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: "ok"`

## Errores conocidos

- `No se encuentra el certificado`
- Errores de BD del repositorio

## Permisos

- Listado en dossier persona (`Select_certificados_de_una_persona`); permiso dossier en frontend.

## Casos De Uso

- `src\certificados\domain\CertificadoRecibidoDelete`

## Frontend Relacionado

- Segmento `select_certificados_de_una_persona` (dossier 1010): `fnjs_eliminar_certificado`
