---
id: "dbextern.ver_desaparecidos_de_listas_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_desaparecidos_de_listas_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_listas_datos.php"
entrada: ["post.ids_desaparecidos_de_listas:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_listas.php"]
casos_uso: ["src\\dbextern\\application\\VerDesaparecidosDeListasData"]
tags: ["dbextern", "ver", "desaparecidos", "de", "listas", "datos"]
estado_revision: "generado"
---

# Ver Desaparecidos De Listas Datos

Obtiene datos de personas de Orbix desaparecidas de la BDU.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/ver_desaparecidos_de_listas_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_listas_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ids_desaparecidos_de_listas` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\dbextern\application\VerDesaparecidosDeListasData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.