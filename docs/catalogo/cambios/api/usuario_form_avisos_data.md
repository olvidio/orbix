---
id: "cambios.usuario_form_avisos_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/usuario_form_avisos_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/cambios/infrastructure/ui/http/controllers/usuario_form_avisos_data.php"
entrada: ["post.id_usuario:integer", "post.quien:string"]
entrada_obligatoria: ["id_usuario", "quien"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_form_avisos.php"]
casos_uso: ["src\\cambios\\application\\UsuarioFormAvisosData"]
tags: ["cambios", "usuario", "form", "avisos", "data"]
estado_revision: "revisado"
---

# Usuario Form Avisos Data

Listado de `CambioUsuarioObjetoPref` de un usuario para la tabla `usuario_form_avisos`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Solo opera si el módulo `cambios` está instalado, `id_usuario > 0` y `quien=usuario`. Construye filas
con objeto, delegación, tipo de actividad, fase/estado, flags de aviso, tipo de aviso y resumen de
propiedades vigiladas.

## Endpoint

- URL: `/src/cambios/usuario_form_avisos_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/usuario_form_avisos_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | controller+application | Sí | Usuario destino |
| `quien` | `string` | controller+application | Sí | Debe ser `usuario` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `error` (`string`)
  - `nombre_usuario` (`string`)
  - `fases_usa_procesos` (`bool`)
  - `a_valores` (`array`): filas con `sel` = `id_usuario#id_item_usuario_objeto`, columnas `1`–`10`
    (objeto, delegación, tipo, fase, flags aviso, tipo aviso, propiedades, condiciones)

## Errores conocidos

- `No tiene permiso` (módulo no instalado, `id_usuario=0`, `quien≠usuario` o usuario inexistente)

## Permisos

- Gate en el caso de uso: requiere app `cambios` y `quien=usuario`; no comprueba permisos de oficina
  adicionales.

## Casos De Uso

- `src\cambios\application\UsuarioFormAvisosData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_form_avisos.php`: tabla embebida en ficha de usuario; acciones
  nuevo/modificar/eliminar redirigen a `usuario_avisos_pref`.
