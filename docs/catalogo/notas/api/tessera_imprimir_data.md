---
id: "notas.tessera_imprimir_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/tessera_imprimir_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/tessera_imprimir_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/tessera_imprimir.php", "frontend/notas/controller/tessera_imprimir_mpdf.php"]
casos_uso: ["src\\notas\\application\\TesseraImprimirData"]
tags: ["notas", "tessera", "imprimir", "data"]
estado_revision: "generado"
---

# Tessera Imprimir Data

Datos imprimibles de tessera ya serializados (sin objetos dominio → JSON estable).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/tessera_imprimir_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_imprimir_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\notas\application\TesseraImprimirData`

## Frontend Relacionado

- `frontend/notas/controller/tessera_imprimir.php`
- `frontend/notas/controller/tessera_imprimir_mpdf.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.