---
id: "encargossacd.ctr_get_ficha_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/ctr_get_ficha_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/ctr_get_ficha_data.php"
entrada: ["post.id_ubi:mixed", "post.seleccion_sacd:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_CtrGetFichaDataData"
respuesta_data: ["id_enc:integer", "id_tipo_enc:integer", "mod_horario:integer", "desc_enc:string", "observ:string", "cl_checked:string", "actual_id_sacd_titular:integer", "actual_id_sacd_suplente:integer", "dedic_ctr_m:string", "dedic_ctr_t:string", "dedic_ctr_v:string", "dedic_m:array", "dedic_t:array", "dedic_v:array", "dedic_sacd:array", "colaboradores:list<array<string, mixed>>", "sacd_num:integer"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/ctr_get_ficha.php"]
casos_uso: ["src\\encargossacd\\application\\CtrGetFichaData"]
tags: ["encargossacd", "ctr", "get", "ficha", "data"]
estado_revision: "revisado"
---
# Ctr Get Ficha Data

Lectura de la ficha de atencion sacerdotal de un centro. Puerto del antiguo `frontend/encargossacd/controller/ctr_get_ficha.php`. Devuelve arrays planos/estructurados para que el controlador frontend arme `frontend\shared\web\Desplegable` y la HTML sin instanciar nada de `src\`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lectura de la ficha de atención sacerdotal de un centro: encargos (tipos 1/2/3), horarios, sacds titular/suplente, opciones de desplegables y flags (`perm_des`, `mod`). Sucesor de `frontend/encargossacd/controller/ctr_get_ficha.php`.

## Endpoint

- URL: `/src/encargossacd/ctr_get_ficha_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_get_ficha_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `mixed` | controller | No | controller |
| `seleccion_sacd` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`.
- Payload grande: `encargos[]`, `aOpcionesSacd`, `perm_des`, `mod`, etc. (doble `JSON.parse`).


## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

## Casos De Uso

- `src\encargossacd\application\CtrGetFichaData`

## Frontend Relacionado

- `frontend/encargossacd/controller/ctr_get_ficha.php`

