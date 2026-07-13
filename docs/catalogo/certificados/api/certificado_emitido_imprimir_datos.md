---
id: "certificados.certificado_emitido_imprimir_datos"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_imprimir_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_datos.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_imprimir.php"]
casos_uso: []
tags: ["certificados", "certificado", "emitido", "imprimir", "datos"]
estado_revision: "revisado"
---

# Certificado Emitido Imprimir Datos

Datos de persona y configuración STGR para iniciar la impresión de un certificado nuevo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Comprueba parámetros de configuración obligatorios (`regionLatin`, `vstgr`, `dirStgr`,
`lugarFirma`, `iniContadorCertificados`) y devuelve datos del alumno y valores por defecto para
el formulario de impresión (fecha certificado = hoy, contador, textos de cabecera).

## Endpoint

- URL: `/src/certificados/certificado_emitido_imprimir_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Sí | Persona (`FilterPostGet::post`) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload: `nombreApellidos`, `lugar_nacimiento`, `f_nacimiento`, `nivel_stgr`, `region_latin`,
  `vstgr`, `dir_stgr`, `lugar_firma`, `contador`, `f_certificado`, `any_2digit`

## Errores conocidos

- Mensaje de parámetros de configuración faltantes (`ConfigSnapshot::formatMissingParametersMessage`)
- Persona no encontrada (texto técnico con `id_nom`)

## Permisos

- Requiere `$_SESSION['oConfig']`; sin permisos de oficina adicionales en el controller.

## Casos De Uso

- Lógica inline en el controller.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_imprimir.php`: formulario previo a generar PDF.
