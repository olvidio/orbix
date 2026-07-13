---
id: "menus.grupmenu_guardar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_guardar.php"
entrada: ["post.grupmenu:string", "post.id_grupmenu:integer", "post.orden:integer"]
entrada_obligatoria: ["grupmenu"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro el grupmenu", "hay un error, no se ha guardado", "debe poner un nombre"]
frontend_referencias: ["frontend/menus/controller/grupmenu_form.php"]
casos_uso: []
tags: ["menus", "grupmenu", "guardar"]
estado_revision: "revisado"
---

# Guardar grupmenu

Alta o edición de un grupo de menú (nombre + orden).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- **Alta** (`id_grupmenu` vacío): crea registro con nuevo id.
- **Edición** (`id_grupmenu` > 0): actualiza nombre (`grupmenu`) y `orden`.

## Endpoint

- URL: `/src/menus/grupmenu_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `grupmenu` | `string` | POST | Si | Nombre del grupo |
| `id_grupmenu` | `integer` | POST | No | Vacío → alta |
| `orden` | `integer` | POST | No | Orden de visualización |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `data: "ok"`, `mensaje` vacío.

## Errores conocidos

- `debe poner un nombre`
- `No encuentro el grupmenu`
- `hay un error, no se ha guardado`

## Permisos

- Menú administración usuarios web / grupmenu.

## Casos De Uso

- Lógica inline en controller.

## Frontend Relacionado

- `frontend/menus/controller/grupmenu_form.php`
