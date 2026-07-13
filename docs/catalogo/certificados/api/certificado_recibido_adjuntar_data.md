---
id: "certificados.certificado_recibido_adjuntar_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_adjuntar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_adjuntar_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_adjuntar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoRecibidoAdjuntarFormData"]
tags: ["certificados", "certificado", "recibido", "adjuntar", "data"]
estado_revision: "revisado"
---

# Certificado Recibido Adjuntar Data

Datos iniciales para adjuntar un certificado recibido (PDF) a una persona.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Valida `id_nom` y devuelve nombre y fecha de recepción por defecto. Avisos suaves de región STGR
van en el payload, no como error HTTP.

## Endpoint

- URL: `/src/certificados/certificado_recibido_adjuntar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_adjuntar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Sí | Persona |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Normal: `nom`, `f_recibido`
- Aviso suave: `aviso`, `nom` vacío, `f_recibido` = hoy

## Errores conocidos

- `persona no encontrada`
- `RegionStgrAviso::mensajePersonaNoValida()` si `id_nom ≤ 0`

## Permisos

- Sin control de permisos propio; dossier persona o flujo adjuntar.

## Casos De Uso

- `src\certificados\application\CertificadoRecibidoAdjuntarFormData`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_recibido_adjuntar.php`
