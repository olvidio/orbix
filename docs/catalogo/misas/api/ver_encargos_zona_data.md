---
id: "misas.ver_encargos_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_encargos_zona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/ver_encargos_zona_data.php"
entrada: ["post.id_zona:integer", "post.orden:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\VerEncargosZonaData"]
tags: ["misas", "ver", "encargos", "zona", "data"]
estado_revision: "generado"
---

# Ver Encargos Zona Data

Devuelve los datos necesarios para pintar el SlickGrid de encargos de una zona + los desplegables del modal de edicion. Replica la consulta de `apps/misas/controller/ver_encargos_zona.php`: encargos con `id_tipo_enc >= 8100` (grupo `8...`) de la zona indicada, ordenados por `$orden` (`orden`, `prioridad` o `desc_enc`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/ver_encargos_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_encargos_zona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | controller | No | controller |
| `orden` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\VerEncargosZonaData`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.