---
id: "notas.acta_modificar"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_modificar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_modificar.php"
entrada: ["post.acta:string", "post.examinadores:array", "post.f_acta:string", "post.id_activ:integer", "post.id_asignatura:integer", "post.libro:integer", "post.linea:integer", "post.lugar:string", "post.observ:string", "post.pagina:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el acta"]
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaModificar"]
tags: ["notas", "acta", "modificar"]
estado_revision: "generado"
---

# Acta Modificar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_modificar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_modificar.php`

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
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `No se encuentra el acta`

## Casos De Uso

- `src\notas\application\ActaModificar`

## Frontend Relacionado

- `frontend/notas/controller/acta_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.