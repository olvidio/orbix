---
id: "certificados.certificado_emitido_imprimir_mpdf_datos"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_imprimir_mpdf_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_mpdf_datos.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_imprimir_mpdf.php", "frontend/certificados/controller/certificado_emitido_2_mpdf.php"]
casos_uso: []
tags: ["certificados", "certificado", "emitido", "imprimir", "mpdf", "datos"]
estado_revision: "revisado"
---

# Certificado Emitido Imprimir Mpdf Datos

Payload completo para renderizar el certificado en PDF (mPDF) con notas y textos traducidos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga certificado emitido, persona, configuración STGR, textos legales (`include textos_certificados`
o traducción por idioma), asignaturas activas y notas aprobadas (`aAprobadas`). Usado por las
pantallas `certificado_emitido_imprimir_mpdf` y `certificado_emitido_2_mpdf`.

## Endpoint

- URL: `/src/certificados/certificado_emitido_imprimir_mpdf_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_mpdf_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | Sí | Certificado emitido |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload principal: `id_nom`, `nom`, `certificado`, `lugar_fecha`, `vstgr`, `dir_stgr`,
  `replace` (mapa entidades HTML), textos (`txt_superavit`, `curso_*`, `titulo_*`, `infra`,
  `sello`, `fidem`, `reg_num`), `f_certificado`, `chk_firmado`, `cAsignaturas` (JSON),
  `aAprobadas` (notas por nivel)

## Errores conocidos

- Certificado o persona no encontrados
- `Configuración de sesión no disponible`
- Parámetros STGR faltantes (`nombre región en latín`, `vstgr`, etc.)
- `No existe un fichero con las traducciones para %s...`
- `Debe definir la región del stgr a la que pertenece`
- `No se ha encontrado la asignatura con id: %s`

## Permisos

- Requiere `$_SESSION['oConfig']` y esquema región STGR en sesión para notas de otras regiones.

## Casos De Uso

- Lógica inline en el controller; incluye `textos_certificados.php`.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_imprimir_mpdf.php`
- `frontend/certificados/controller/certificado_emitido_2_mpdf.php`
