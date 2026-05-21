---
id: "pasarela.contribucion_no_duerme_excepcion_eliminar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_no_duerme_excepcion_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_eliminar.php"
entrada: ["post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta id_tipo_activ"]
frontend_referencias: ["frontend/pasarela/controller/contribucion_no_duerme_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionNoDuermeExcepcionEliminar"]
tags: ["pasarela", "contribucion", "no", "duerme", "excepcion", "eliminar"]
estado_revision: "generado"
---

# Contribucion No Duerme Excepcion Eliminar

Elimina una excepción del parámetro `contribucion_no_duerme` para un `id_tipo_activ` concreto.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina una excepción del parámetro `contribucion_no_duerme` para un `id_tipo_activ` concreto.

## Errores conocidos

- `Falta id_tipo_activ`

## Casos De Uso

- `src\pasarela\application\ContribucionNoDuermeExcepcionEliminar`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.