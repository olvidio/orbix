---
id: "notas.acta_nueva"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_nueva"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_nueva.php"
entrada: ["post.acta:string", "post.examinadores:array", "post.f_acta:string", "post.id_activ:integer", "post.id_asignatura:integer", "post.libro:integer", "post.linea:integer", "post.lugar:string", "post.observ:string", "post.pagina:integer", "post.sel:array"]
entrada_obligatoria: ["acta"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaNueva"]
tags: ["notas", "acta", "nueva"]
estado_revision: "revisado"
---

# Acta Nueva

Da de alta un acta con cabecera (asignatura, actividad, fechas, libro/página/línea, lugar, observaciones) y sincroniza el tribunal de examinadores.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_nueva`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_nueva.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | application | No | application |
| `examinadores` | `array` | application | No | application |
| `f_acta` | `string` | application | No | application |
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |
| `libro` | `integer` | application | No | application |
| `linea` | `integer` | application | No | application |
| `lugar` | `string` | application | No | application |
| `observ` | `string` | application | No | application |
| `pagina` | `integer` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.

## Objetivo funcional

Alta de acta desde `acta_ver` en modo nuevo (`mod=nueva` o `notas=nuevo`). Propone número de acta siguiente, guarda en repositorio DL o regional según ámbito y persiste examinadores vía `ActaTribunalSync`.

## Permisos

- Frontend `acta_select`/`acta_ver`: `have_perm_oficina('est')` en ámbito DL; en `rstgr` solo lectura.

## Errores conocidos

- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\notas\application\ActaNueva`

## Frontend Relacionado

- Invocado desde `fnjs_guardar_acta` en `frontend/notas/controller/acta_ver.php` (`url_acta_nueva` en payload de `acta_ver_form_data`).