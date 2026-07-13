---
id: "certificados.certificado_emitido_ver_datos"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_ver_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_ver_datos.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_ver.php"]
casos_uso: []
tags: ["certificados", "certificado", "emitido", "ver", "datos"]
estado_revision: "revisado"
---

# Certificado Emitido Ver Datos

Datos de un certificado emitido para la vista de consulta/edición (`certificado_emitido_ver`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga un `CertificadoEmitido` por `id_item` y devuelve campos del formulario/vista, incluido el
PDF embebido en base64 si existe documento adjunto.

## Endpoint

- URL: `/src/certificados/certificado_emitido_ver_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_ver_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | Sí | PK del certificado emitido |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `idioma`, `destino`, `certificado`, `f_certificado`, `f_enviado`, `firmado` (`bool`)
  - `content` (`string`): PDF en base64 o vacío
  - `nom`, `apellidos_nombre`, `id_nom`

## Errores conocidos

- `No encuentro certificado emitido con id_item: %d`

## Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']` al abrir desde el listado.

## Casos De Uso

- Lógica inline en el controller (repositorio `CertificadoEmitidoRepositoryInterface`).

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_ver.php`: modal/vista desde el listado de emitidos.
