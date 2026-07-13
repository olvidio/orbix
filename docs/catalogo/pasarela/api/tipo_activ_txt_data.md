---
id: "pasarela.tipo_activ_txt_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/tipo_activ_txt_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/tipo_activ_txt_data.php"
entrada:
  - "post.id_tipo_activ:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/activacion_ajax.php"
  - "frontend\/pasarela\/controller\/contribucion_no_duerme_ajax.php"
  - "frontend\/pasarela\/controller\/contribucion_reserva_ajax.php"
  - "frontend\/pasarela\/controller\/nombre_ajax.php"
casos_uso: ["src\pasarela\application\TipoActivTxtData"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Tipo Activ Txt Data

Texto descriptivo del tipo de actividad (sf/sv + asistentes + actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Usado en `form_modificar` de activación, contribuciones y nombre para mostrar la fila editada.

## Endpoint

- URL: `/src/pasarela/tipo_activ_txt_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/tipo_activ_txt_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |


## Salida

- Payload: `{tipo_txt: string}`.

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Sin control en el caso de uso.

## Casos De Uso

- `src\pasarela\application\TipoActivTxtData`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`
- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`
- `frontend/pasarela/controller/contribucion_reserva_ajax.php`
- `frontend/pasarela/controller/nombre_ajax.php`