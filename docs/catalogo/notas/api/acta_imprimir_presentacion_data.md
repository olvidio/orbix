---
id: "notas.acta_imprimir_presentacion_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_imprimir_presentacion_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/acta_imprimir_presentacion_data.php"
entrada: ["post.acta:string", "post.mode:string"]
entrada_obligatoria: ["acta"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta el acta", "No se encuentra el acta: %s", "El acta no tiene asignatura asociada", "No se ha encontrado la asignatura con id: %s", "La asignatura no tiene tipo", "No se ha encontrado el tipo de asignatura con id: %s"]
frontend_referencias: ["frontend/notas/controller/acta_imprimir.php", "frontend/notas/controller/acta_imprimir_mpdf.php"]
casos_uso: ["src\\notas\\application\\ActaImprimirPresentacionData"]
tags: ["notas", "acta", "imprimir", "presentacion", "data"]
estado_revision: "revisado"
---

# Acta Imprimir Presentacion Data

Datos de presentación para imprimir un acta (HTML/mpdf).

Datos compartidos por `acta_imprimir` y el HTML de `acta_imprimir_mpdf`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_imprimir_presentacion_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_imprimir_presentacion_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | controller | No | controller |
| `mode` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Payload de impresión en `data` (doble `JSON.parse`); avisos HTML en `mensaje` si hay notas inaccesibles.

## Objetivo funcional

Construye payload de impresión: cabecera acta, asignatura, alumnos con notas. Valida acceso a nombres de otras DL.

## Permisos

- Desde `acta_imprimir` / selección en `acta_select`.

## Errores conocidos

- `Falta el acta`
- `No se encuentra el acta: %s`
- `El acta no tiene asignatura asociada`
- `No se ha encontrado la asignatura con id: %s`
- `La asignatura no tiene tipo`
- `No se ha encontrado el tipo de asignatura con id: %s`

## Casos De Uso

- `src\notas\application\ActaImprimirPresentacionData`

## Frontend Relacionado

- `frontend/notas/controller/acta_imprimir.php`, `acta_imprimir_mpdf.php`.