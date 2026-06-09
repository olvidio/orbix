---
id: "notas.acta_imprimir_presentacion_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_imprimir_presentacion_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_imprimir_presentacion_data.php"
entrada: ["post.acta:string", "post.mode:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/acta_imprimir.php", "frontend/notas/controller/acta_imprimir_mpdf.php"]
casos_uso: ["src\\notas\\application\\ActaImprimirPresentacionData"]
tags: ["notas", "acta", "imprimir", "presentacion", "data"]
estado_revision: "generado"
---

# Acta Imprimir Presentacion Data

Datos compartidos por `acta_imprimir` y el HTML de `acta_imprimir_mpdf`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_imprimir_presentacion_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
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
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\notas\application\ActaImprimirPresentacionData`

## Frontend Relacionado

- `frontend/notas/controller/acta_imprimir.php`
- `frontend/notas/controller/acta_imprimir_mpdf.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.