---
id: "pasarela.contribucion_no_duerme_excepcion_eliminar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_no_duerme_excepcion_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_eliminar.php"
entrada:
  - "post.id_tipo_activ:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Falta id_tipo_activ"
frontend_referencias:
  - "frontend\/pasarela\/controller\/contribucion_no_duerme_ajax.php"
casos_uso: ["src\pasarela\application\ContribucionNoDuermeExcepcionEliminar"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Contribucion No Duerme Excepcion Eliminar

Elimina excepción de contribución no duerme.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la fila de excepción.

## Endpoint

- URL: `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |


## Salida

- Éxito: `data: "ok"`.

## Errores conocidos

- `Falta id_tipo_activ`

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ContribucionNoDuermeExcepcionEliminar`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`