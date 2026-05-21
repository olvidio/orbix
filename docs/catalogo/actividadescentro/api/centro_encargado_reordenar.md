---
id: "actividadescentro.centro_encargado_reordenar"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centro_encargado_reordenar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_reordenar.php"
entrada: ["post.id_activ:integer", "post.id_ubi:integer", "post.num_orden:string"]
entrada_obligatoria: ["id_activ", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_ubi", "direccion de orden incorrecta (mas / menos)"]
frontend_referencias: []
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoReordenar"]
tags: ["actividadescentro", "centro", "encargado", "reordenar"]
estado_revision: "generado"
---

# Centro Encargado Reordenar

Endpoint backend: reordena un CentroEncargado (mas / menos prioridad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadescentro/centro_encargado_reordenar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_reordenar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | application |
| `id_ubi` | `integer` | application | Si | application |
| `num_orden` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan parametros id_activ / id_ubi`
- `direccion de orden incorrecta (mas / menos)`

## Casos De Uso

- `src\actividadescentro\application\CentroEncargadoReordenar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.