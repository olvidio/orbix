---
id: "cartaspresentacion.carta_presentacion_update"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/carta_presentacion_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_update.php"
entrada: ["post.id_direccion:integer", "post.id_ubi:integer", "post.observ:string", "post.pres_mail:string", "post.pres_nom:string", "post.pres_telf:string", "post.zona:string"]
entrada_obligatoria: ["id_ubi", "id_direccion"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan id_ubi o id_direccion", "No puede modificar datos de otra dl", "Hay un error, no se ha guardado."]
frontend_referencias: ["frontend/cartaspresentacion/view/cartas_presentacion.phtml"]
casos_uso: ["src\\cartaspresentacion\\application\\CartaPresentacionUpdate"]
tags: ["cartaspresentacion", "carta", "presentacion", "update"]
estado_revision: "revisado"
---

# Carta Presentacion Update

Crea o actualiza una `CartaPresentacion`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Persiste los datos de presentación (`pres_nom`, `pres_telf`, `pres_mail`, `zona`, `observ`) para el par
`id_ubi` + `id_direccion`. Si la carta no existe, la crea en el repositorio adecuado según el centro
(dl propia → `CartaPresentacionDlRepository`; `cr` extranjero → `CartaPresentacionExRepository`).
Tras guardar ejecuta `sanear()` para eliminar cartas de la dl cuya dirección ya no pertenece al centro.

## Endpoint

- URL: `/src/cartaspresentacion/carta_presentacion_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller+application | Sí | PK del centro |
| `id_direccion` | `integer` | controller+application | Sí | PK de la dirección |
| `pres_nom` | `string` | controller+application | No | Nombre del director |
| `pres_telf` | `string` | controller+application | No | Teléfono |
| `pres_mail` | `string` | controller+application | No | E-mail |
| `zona` | `string` | controller+application | No | Zona geográfica |
| `observ` | `string` | controller+application | No | Observaciones |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: ""` (string vacío).
- Error de negocio: `success: false`, `mensaje` con el texto traducido, `data: ""`.

## Efectos colaterales

- Alta o actualización de `CartaPresentacion` en el repositorio correspondiente.
- `sanear()` elimina cartas de la dl cuya `id_direccion` ya no figura entre las direcciones del centro.

## Errores conocidos

- `Faltan id_ubi o id_direccion`
- `No puede modificar datos de otra dl` (alta en centro ajeno que no es `cr`)
- `Hay un error, no se ha guardado.`

## Permisos

- Al crear: solo centros de la propia dl o `tipo_ctr=cr` (`resolveWriteRepo`). Al actualizar una carta
  existente usa el repositorio general sin revalidar dl (la carta ya existía).

## Casos De Uso

- `src\cartaspresentacion\application\CartaPresentacionUpdate`

## Frontend Relacionado

- Invocado desde `fnjs_guardar_cp` en `cartas_presentacion.phtml` (submit del modal
  `cartas_presentacion_form.phtml`). URL expuesta como `URL_UPDATE` desde la shell.
