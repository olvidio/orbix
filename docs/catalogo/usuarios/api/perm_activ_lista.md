---
id: "usuarios.perm_activ_lista"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/perm_activ_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/perm_activ_lista.php"
entrada: ["post.id_usuario:string"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/perm_activ_lista.php"]
casos_uso: []
tags: ["usuarios", "perm", "activ", "lista"]
estado_revision: "revisado"
errores: []
---

# Perm Activ Lista

Tabla de permisos actividad-proceso del usuario con etiquetas de tipo, fase y bits.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Tabla de permisos actividad-proceso del usuario con etiquetas de tipo, fase y bits.

## Endpoint

- URL: `/src/usuarios/perm_activ_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_cabeceras`: columnas permiso
  - `a_botones`: modificar/eliminar
  - `a_valores`: filas con sel id_usuario#id_item

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Admin en pestaña permisos actividad de `usuario_form`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/perm_activ_lista.php"]`).
