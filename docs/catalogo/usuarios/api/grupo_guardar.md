---
id: "usuarios.grupo_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/grupo_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/grupo_guardar.php"
entrada: ["post.usuario:string", "post.id_usuario:integer"]
entrada_obligatoria: ["usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "grupo", "guardar"]
estado_revision: "revisado"
errores: ["debe poner un nombre", "Grupo no encontrado", "hay un error, no se ha guardado"]
---

# Grupo Guardar

Crea o actualiza un grupo de permisos (nombre en campo `usuario`, id en `id_usuario`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o actualiza un grupo de permisos (nombre en campo `usuario`, id en `id_usuario`).

## Endpoint

- URL: `/src/usuarios/grupo_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `usuario` | `string` | application | Si | |
| `id_usuario` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `debe poner un nombre`
- `Grupo no encontrado`
- `hay un error, no se ha guardado`

## Permisos

Admin id_role≤3 en frontend `grupo_form`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
