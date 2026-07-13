---
id: "usuarios.preferencias_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/preferencias_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php"
entrada: ["post.que:string", "post.tabla:string", "post.sPrefs:string", "post.layout:string", "post.oficina:string", "post.inicio:string", "post.tipo_tabla:string", "post.ordenApellidos:string", "post.idioma_nou:string", "post.zona_horaria_nou:string", "post.estilo_color:string", "post.tipo_menu:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/security/HashFront.php"]
casos_uso: []
tags: ["usuarios", "preferencias", "guardar"]
estado_revision: "revisado"
errores: ["hay un error, no se ha guardado"]
---

# Preferencias Guardar

Persiste preferencias personales: layout, inicio, slickGrid, tabla, idioma, zona horaria, estilo (según `que`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Persiste preferencias personales: layout, inicio, slickGrid, tabla, idioma, zona horaria, estilo (según `que`).

## Endpoint

- URL: `/src/usuarios/preferencias_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | application | No | |
| `tabla` | `string` | application | No | |
| `sPrefs` | `string` | application | No | |
| `layout` | `string` | application | No | |
| `oficina` | `string` | application | No | |
| `inicio` | `string` | application | No | |
| `tipo_tabla` | `string` | application | No | |
| `ordenApellidos` | `string` | application | No | |
| `idioma_nou` | `string` | application | No | |
| `zona_horaria_nou` | `string` | application | No | |
| `estilo_color` | `string` | application | No | |
| `tipo_menu` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `hay un error, no se ha guardado`

## Permisos

Usuario autenticado; escribe en `web_preferencias` del usuario actual.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/shared/security/HashFront.php"]`).
