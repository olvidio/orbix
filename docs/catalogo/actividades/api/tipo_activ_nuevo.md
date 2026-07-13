---
id: "actividades.tipo_activ_nuevo"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_nuevo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_nuevo.php"
entrada: ["post.isfsv_val:string", "post.iasistentes_val:string", "post.iactividad_val:string", "post.id_nom_tipo_activ:string", "post.nom_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Id incorrecto", "hay un error, no se ha guardado", "IMPORTANTE: Debe añadir un proceso para el nuevo tipo de actividad"]
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivNuevo"]
tags: ["actividades", "tipo", "activ", "nuevo"]
estado_revision: "revisado"
---

# Tipo Activ Nuevo

Crea un nuevo tipo de actividad. Portado del case `nuevo` del dispatcher legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Compone el `id_tipo_activ` concatenando `isfsv_val` + `iasistentes_val` + `iactividad_val` +
`id_nom_tipo_activ` (debe resultar en 6 caracteres) y guarda el `TipoDeActividad` con el nombre
`nom_tipo_activ`. Al guardar, invalida la caché de sesión `TipoActivMetadataLoader::forget()`. Si el
módulo `procesos` está instalado, añade un aviso recordando dar de alta el proceso del nuevo tipo.

## Endpoint

- URL: `/src/actividades/tipo_activ_nuevo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_nuevo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `isfsv_val` | `string` | application | No | 1er dígito del id (sf/sv); del selector `ActividadTipo` |
| `iasistentes_val` | `string` | application | No | 2º dígito del id (tipo asistentes) |
| `iactividad_val` | `string` | application | No | dígitos de actividad |
| `id_nom_tipo_activ` | `string` | application | No | dígito final del id |
| `nom_tipo_activ` | `string` | application | No | Nombre del tipo |

El id compuesto debe medir exactamente 6 caracteres; si no, devuelve `Id incorrecto`.

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con la clave `mensaje`: cadena **vacía** si todo fue bien, o el texto de
  error/aviso acumulado. Los errores de negocio **no** viajan como `success: false`, sino dentro de
  `data.mensaje`.

## Errores conocidos

- `Id incorrecto` (el id compuesto no tiene 6 caracteres)
- `hay un error, no se ha guardado`
- `IMPORTANTE: Debe añadir un proceso para el nuevo tipo de actividad` (aviso informativo cuando el
  módulo `procesos` está instalado; se añade incluso en alta correcta)

## Permisos

- Sin control de permisos propio. La autorización se resuelve en el frontend (`tipo_activ.php`, firma
  `HashFront`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\TipoActivNuevo`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php` (emite la URL como `url_nuevo`).
