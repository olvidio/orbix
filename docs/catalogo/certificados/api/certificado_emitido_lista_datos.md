---
id: "certificados.certificado_emitido_lista_datos"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_lista_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_lista_datos.php"
entrada: ["post.certificado:string", "post.fincurs_ca_iso:string", "post.inicurs_ca_iso:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_lista.php"]
casos_uso: ["src\\certificados\\domain\\CertificadoEmitidoSelect"]
tags: ["certificados", "certificado", "emitido", "lista", "datos"]
estado_revision: "revisado"
---

# Certificado Emitido Lista Datos

Builder de tabla para el listado de certificados emitidos (no enviados o filtrados por número).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye cabeceras, filas y botones de la tabla de certificados emitidos de la región STGR
(`esquema_emisor = mi_region_dl()`). Si `certificado` viene informado, filtra por número (con
autocompletado de región/año); si está vacío, filtra por rango de fechas `inicurs_ca_iso`–
`fincurs_ca_iso` y solo certificados con `f_enviado IS NULL`.

## Endpoint

- URL: `/src/certificados/certificado_emitido_lista_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_lista_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `certificado` | `string` | controller | No | Filtro por número; vacío → rango de curso |
| `inicurs_ca_iso` | `string` | controller | No | Inicio curso (ISO) cuando no hay filtro número |
| `fincurs_ca_iso` | `string` | controller | No | Fin curso (ISO) cuando no hay filtro número |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `a_cabeceras` (`array`): columnas certificado, fecha, alumno, firmado, adjunto, idioma, destino, enviado
  - `a_valores` (`array`): filas con `sel` = `id_item` y columnas `1`–`8`
  - `a_botones` (`array`): acciones JS; eliminar/modificar/subir/enviar solo si la delegación es región STGR

## Errores conocidos

- `No se encuentra la tabla. ¿Seguro que es una región del stgr?` (tabla `e_certificados_rstgr` inexistente)
- Mensaje de error de BD devuelto por el repositorio (en `mensaje`)

## Permisos

- Botones de mutación condicionados a `DelegacionRepository::soy_region_stgr()` en el dominio.
- La pantalla frontend restringe acceso a ámbito `rstgr` o `r` (`OrbixRuntime::miAmbito()`).

## Casos De Uso

- `src\certificados\domain\CertificadoEmitidoSelect`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_lista.php`: pantalla principal del menú Certificados.
