---
id: "cartaspresentacion.carta_presentacion_eliminar"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/carta_presentacion_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_eliminar.php"
entrada: ["post.id_direccion:integer", "post.id_ubi:integer"]
entrada_obligatoria: ["id_ubi", "id_direccion"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan id_ubi o id_direccion", "Carta de presentacion no encontrada", "Hay un error, no se ha borrado."]
frontend_referencias: ["frontend/cartaspresentacion/view/cartas_presentacion.phtml"]
casos_uso: ["src\\cartaspresentacion\\application\\CartaPresentacionEliminar"]
tags: ["cartaspresentacion", "carta", "presentacion", "eliminar"]
estado_revision: "revisado"
---

# Carta Presentacion Eliminar

Elimina una `CartaPresentacion`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la carta identificada por `id_ubi` + `id_direccion`. Sucesor de la rama `que_mod=eliminar` del
dispatcher legacy `cartas_presentacion_ajax.php`.

## Endpoint

- URL: `/src/cartaspresentacion/carta_presentacion_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller+application | Sí | PK del centro |
| `id_direccion` | `integer` | controller+application | Sí | PK de la dirección |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: ""` (string vacío).
- Error de negocio: `success: false`, `mensaje` con el texto traducido, `data: ""`.

## Efectos colaterales

- Eliminación física de la `CartaPresentacion` vía `CartaPresentacionRepository::Eliminar`.

## Errores conocidos

- `Faltan id_ubi o id_direccion`
- `Carta de presentacion no encontrada`
- `Hay un error, no se ha borrado.`

## Permisos

- Sin control de permisos propio en el caso de uso; la acción solo se ofrece en el listado para
  centros que ya tienen carta (`fnjs_eliminar_cp` en la shell). Autorización en frontend +
  `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cartaspresentacion\application\CartaPresentacionEliminar`

## Frontend Relacionado

- Invocado desde `fnjs_eliminar_cp` en `cartas_presentacion.phtml` tras confirmación del usuario.
  URL firmada como `URL_ELIMINAR` desde la shell.
