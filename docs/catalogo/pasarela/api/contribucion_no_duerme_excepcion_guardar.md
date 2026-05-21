---
id: "pasarela.contribucion_no_duerme_excepcion_guardar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_no_duerme_excepcion_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_guardar.php"
entrada: ["post.id_tipo_activ:string", "post.valor:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta id_tipo_activ", "Falta valor de contribución", "Debe ser un numero entero del 1 al 100"]
frontend_referencias: ["frontend/pasarela/controller/contribucion_no_duerme_ajax.php", "frontend/pasarela/controller/contribucion_no_duerme_lista.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionNoDuermeExcepcionGuardar"]
tags: ["pasarela", "contribucion", "no", "duerme", "excepcion", "guardar"]
estado_revision: "generado"
---

# Contribucion No Duerme Excepcion Guardar

Inserta o actualiza una excepción del parámetro `contribucion_no_duerme` para un `id_tipo_activ` concreto.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | controller |
| `valor` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

## Casos De Uso

- `src\pasarela\application\ContribucionNoDuermeExcepcionGuardar`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`
- `frontend/pasarela/controller/contribucion_no_duerme_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.