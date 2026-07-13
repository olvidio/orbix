---
id: "tablonanuncios.anuncio_delete"
tipo: "endpoint"
modulo: "tablonanuncios"
url: "/src/tablonanuncios/anuncio_delete"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/tablonanuncios/infrastructure/ui/http/controllers/anuncio_delete.php"
entrada: ["post.sel:array", "post.uuid_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el anuncio", "error al borrar el anuncio"]
frontend_referencias: ["public/portada.php"]
casos_uso: ["src\\tablonanuncios\\application\\AnuncioDelete"]
tags: ["tablonanuncios", "anuncio", "delete"]
estado_revision: "revisado"
---

# Anuncio Delete

Borrado lógico de un anuncio (marca `t_eliminado`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Marca un anuncio como eliminado sin borrarlo físicamente: carga el `Anuncio` por su UUID, le fija
`t_eliminado` con la fecha/hora actual (`DateTimeLocal`) y lo persiste. El controller resuelve el
identificador de forma flexible: si llega `sel` (lista de seleccionados del tablón), toma el primer
token de `sel[0]` antes del `#` como `uuid_item`; en caso contrario usa el campo `uuid_item` directo.
Si el UUID llega vacío o el anuncio no existe, no borra nada y devuelve el error correspondiente.

## Endpoint

- URL: `/src/tablonanuncios/anuncio_delete`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/tablonanuncios/infrastructure/ui/http/controllers/anuncio_delete.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller | No | Lista de seleccionados; se usa `strtok(sel[0], '#')` → el primer token es el `uuid_item` (formato `uuid_item#...`) |
| `uuid_item` | `string` | controller | No | UUID del anuncio; se usa solo si `sel` viene vacío |

Se requiere que al menos uno de los dos aporte un `uuid_item` no vacío; el caso de uso
(`AnuncioDelete::execute(string $uuid_item)`) recibe únicamente el UUID resuelto por el controller.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"` (el caso de uso devuelve string vacío en éxito).
- En error de negocio: `success: false`, `mensaje` con el texto traducido, `data: "none"`.

## Efectos colaterales

- Borrado lógico de un anuncio (marca `t_eliminado` vía `AnuncioRepository::Guardar`). No hay borrado
  físico ni efectos en cascada.

## Errores conocidos

- `No se encuentra el anuncio` — el `uuid_item` llega vacío o no existe ningún anuncio con ese UUID.
- `error al borrar el anuncio` — falló la persistencia (`Guardar` devolvió `false`).

## Permisos

- El caso de uso no aplica un control de permisos propio. La autorización se resuelve en el frontend
  (`public/portada.php`), que monta el listado con `HashFront` y opera bajo la sesión del usuario
  (`$_SESSION['oPerm']`). No inferir permisos concretos aquí.

## Casos De Uso

- `src\tablonanuncios\application\AnuncioDelete`

## Frontend Relacionado

- `public/portada.php`: la función JS `fnjs_borrar(formulario)` fija `mod=eliminar`, serializa el
  form `#seleccionados` (que incluye `sel`) y hace `POST` a `src/tablonanuncios/anuncio_delete`;
  tras `success` refresca el listado.
