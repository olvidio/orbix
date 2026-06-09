---
id: "inventario.equipajes_nuevo_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_nuevo_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_nuevo_guardar.php"
entrada: ["post.f_fin:string", "post.f_ini:string", "post.id_ubi_activ:integer", "post.ids_activ:string", "post.lugar:string", "post.nom_equipaje:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["inventario", "equipajes", "nuevo", "guardar"]
estado_revision: "generado"
---

# Equipajes Nuevo Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/equipajes_nuevo_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_nuevo_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_fin` | `string` | controller | No | controller |
| `f_ini` | `string` | controller | No | controller |
| `id_ubi_activ` | `integer` | controller | No | controller |
| `ids_activ` | `string` | controller | No | controller |
| `lugar` | `string` | controller | No | controller |
| `nom_equipaje` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.