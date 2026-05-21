---
id: "usuarios.preferencias_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/preferencias_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php"
entrada: ["post.estilo_color:string", "post.idioma_nou:string", "post.inicio:string", "post.layout:string", "post.oficina:string", "post.ordenApellidos:string", "post.que:string", "post.sPrefs:string", "post.tabla:string", "post.tipo_menu:string", "post.tipo_tabla:string", "post.zona_horaria_nou:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/security/HashFront.php"]
casos_uso: []
tags: ["usuarios", "preferencias", "guardar"]
estado_revision: "generado"
---

# Preferencias Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/preferencias_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `estilo_color` | `string` | controller | No | controller |
| `idioma_nou` | `string` | controller | No | controller |
| `inicio` | `string` | controller | No | controller |
| `layout` | `string` | controller | No | controller |
| `oficina` | `string` | controller | No | controller |
| `ordenApellidos` | `string` | controller | No | controller |
| `que` | `string` | controller | No | controller |
| `sPrefs` | `string` | controller | No | controller |
| `tabla` | `string` | controller | No | controller |
| `tipo_menu` | `string` | controller | No | controller |
| `tipo_tabla` | `string` | controller | No | controller |
| `zona_horaria_nou` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/shared/security/HashFront.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.