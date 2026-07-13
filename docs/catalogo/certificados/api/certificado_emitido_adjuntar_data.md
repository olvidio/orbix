---
id: "certificados.certificado_emitido_adjuntar_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_adjuntar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_adjuntar_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_adjuntar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoAdjuntarFormData"]
tags: ["certificados", "certificado", "emitido", "adjuntar", "data"]
estado_revision: "revisado"
---

# Certificado Emitido Adjuntar Data

Datos iniciales para adjuntar un certificado emitido (PDF) a una persona.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Valida `id_nom`, obtiene nombre de la persona y fecha de envío por defecto (`hoy`). Si la persona
no es válida para la región STGR, devuelve un aviso suave en el payload en lugar de error duro.

## Endpoint

- URL: `/src/certificados/certificado_emitido_adjuntar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_adjuntar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Sí | Persona destino |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload normal: `nom`, `f_enviado`
- Aviso suave (`RegionStgrAviso`): `aviso`, `nom` vacío, `f_enviado` = hoy

## Errores conocidos

- `persona no encontrada`
- Mensaje de `RegionStgrAviso::mensajePersonaNoValida()` si `id_nom ≤ 0`

## Permisos

- Sin control de permisos propio; formulario abierto desde dossier o flujo de persona.

## Casos De Uso

- `src\certificados\application\CertificadoEmitidoAdjuntarFormData`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_adjuntar.php`
