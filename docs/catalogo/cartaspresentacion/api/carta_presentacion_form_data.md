---
id: "cartaspresentacion.carta_presentacion_form_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/carta_presentacion_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_form_data.php"
entrada: ["post.id_direccion:integer", "post.id_ubi:integer"]
entrada_obligatoria: ["id_ubi", "id_direccion"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartaPresentacionFormDataData"
respuesta_data: ["ok:boolean", "mensaje:string", "id_ubi:integer", "id_direccion:integer", "nombre_ubi:string", "pres_nom:string", "pres_telf:string", "pres_mail:string", "zona:string", "observ:string", "paths:array", "hash_update:array"]
requiere_hashb: false
errores: ["Faltan id_ubi o id_direccion", "Centro no encontrado", "No puede modificar datos de otra dl"]
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_form.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartaPresentacionFormData"]
tags: ["cartaspresentacion", "carta", "presentacion", "form", "data"]
estado_revision: "revisado"
---

# Carta Presentacion Form Data

Datos del formulario modal de modificación de una `CartaPresentacion`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga o prepara el alta de una carta para el par `id_ubi` + `id_direccion`. Valida que el centro
exista y que pertenezca a la delegación del usuario o sea un `cr` extranjero. Si `ok=true`, devuelve
los campos editables y la especificación `hash_update` para el submit; si la carta no existe aún, los
campos vienen vacíos (alta implícita al guardar).

## Endpoint

- URL: `/src/cartaspresentacion/carta_presentacion_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller+application | Sí | PK del centro |
| `id_direccion` | `integer` | controller+application | Sí | PK de la dirección |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el cliente).
- Forma: `standard_envelope_string_data`.
- Payload en `data`:
  - `ok` (`boolean`): `false` si falla validación; `true` si puede editarse.
  - `mensaje` (`string`): texto de error cuando `ok=false`.
  - `id_ubi`, `id_direccion` (`integer`): eco de la entrada.
  - `nombre_ubi` (`string`): nombre del centro (+ sede entre paréntesis si aplica).
  - `pres_nom`, `pres_telf`, `pres_mail`, `zona`, `observ` (`string`): valores actuales o vacíos.
  - `paths` (`array`, solo si `ok=true`): `update` → `src/cartaspresentacion/carta_presentacion_update`.
  - `hash_update` (`array`, solo si `ok=true`): `campos_hidden` con ids, `campos_form` =
    `pres_nom!pres_telf!pres_mail!zona!observ`.

## Errores conocidos

- `Faltan id_ubi o id_direccion`
- `Centro no encontrado`
- `No puede modificar datos de otra dl`

## Permisos

- Validación en el caso de uso: solo centros de la propia delegación (`ConfigGlobal::mi_delef()`) o
  con `tipo_ctr=cr`. No usa `perm_modificar()` ni flags de sesión adicionales.

## Casos De Uso

- `src\cartaspresentacion\application\CartaPresentacionFormData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_form.php`: invocado desde
  `fnjs_modificar` de `cartas_presentacion.phtml`; `CartaPresentacionFormRender` firma `hash_update`
  y renderiza `cartas_presentacion_form.phtml`.
