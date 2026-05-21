---
id: "shared.tablaDB_depende_datos"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_depende_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_depende_datos.php"
entrada: ["post.clase_info:string", "post.opcion_sel:string", "post.pKeyRepository:string", "post.valor_depende:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/controller/tablaDB_formulario_ver.php"]
casos_uso: []
tags: ["shared", "tablaDB", "depende", "datos"]
estado_revision: "generado"
---

# TablaDB Depende Datos

************  datos  *********************************

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/shared/tablaDB_depende_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_depende_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | controller | No | controller |
| `opcion_sel` | `string` | controller | No | controller |
| `pKeyRepository` | `string` | controller | No | controller |
| `valor_depende` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/shared/controller/tablaDB_formulario_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.