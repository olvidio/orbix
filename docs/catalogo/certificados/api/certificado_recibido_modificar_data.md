---
id: "certificados.certificado_recibido_modificar_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_modificar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_modificar_data.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_modificar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoRecibidoModificarFormData"]
tags: ["certificados", "certificado", "recibido", "modificar", "data"]
estado_revision: "revisado"
---

# Certificado Recibido Modificar Data

Datos del formulario para editar un certificado recibido existente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga el certificado recibido por `id_item` con todos los campos editables y el desplegable de
idiomas (`a_locales`).

## Endpoint

- URL: `/src/certificados/certificado_recibido_modificar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_modificar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | Sí | Certificado recibido |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload: `id_nom`, `nom`, `idioma`, `destino`, `certificado`, `f_certificado`, `f_recibido`,
  `firmado`, `chk_firmado`, `a_locales`

## Errores conocidos

- `No se encuentra el certificado`

## Permisos

- Sin control de permisos propio; acceso desde listado dossier o modificar.

## Casos De Uso

- `src\certificados\application\CertificadoRecibidoModificarFormData`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_recibido_modificar.php`
