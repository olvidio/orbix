---
id: "cambios.cambio_usuario_eliminar"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/avisos_generar.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioEliminar"]
tags: ["cambios", "cambio", "usuario", "eliminar"]
estado_revision: "revisado"
---

# Cambio Usuario Eliminar

Elimina uno o varios `CambioUsuario` por la clave compuesta recibida en `sel[]`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recorre cada token de `sel[]` con formato `id_item_cambio#id_usuario#sfsv#aviso_tipo`, localiza el
registro y lo elimina. Con `sel` vacío devuelve éxito sin hacer nada.

## Endpoint

- URL: `/src/cambios/cambio_usuario_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller+application | No | Tokens `id_item_cambio#id_usuario#sfsv#aviso_tipo` |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: "ok"` (string vacío serializado).
- Error: mensaje en el envelope (`Hay un error, no se ha eliminado` + detalle repositorio).

## Errores conocidos

- `Hay un error, no se ha eliminado` (concatenado con errores del repositorio)

## Permisos

- Sin control propio; la autorización se resuelve en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioEliminar`

## Frontend Relacionado

- `frontend/cambios/controller/avisos_generar.php`: `fnjs_borrar` envía `sel[]` firmado vía
  `hash_eliminar` del payload de `avisos_generar_lista_data`.
