---
id: "inventario.equipajes_lista_activ_periodo"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_lista_activ_periodo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_periodo.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.fin:string", "post.id_cdc:integer", "post.inicio:string", "post.periodo:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/inventario/controller/equipajes_lista_activ_periodo.php"]
casos_uso: []
tags: ["inventario", "equipajes", "lista", "activ", "periodo"]
estado_revision: "generado"
---

# Equipajes Lista Activ Periodo

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/equipajes_lista_activ_periodo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_periodo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `fin` | `string` | controller | No | controller |
| `id_cdc` | `integer` | controller | No | controller |
| `inicio` | `string` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |
| `year` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_lista_activ_periodo.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.