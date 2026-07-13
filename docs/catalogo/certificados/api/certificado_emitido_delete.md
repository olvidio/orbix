---
id: "certificados.certificado_emitido_delete"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_delete"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_delete.php"
entrada: ["post.id_item:integer", "post.sel:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_lista.php"]
casos_uso: ["src\\certificados\\domain\\CertificadoEmitidoDelete"]
tags: ["certificados", "certificado", "emitido", "delete"]
estado_revision: "revisado"
---

# Certificado Emitido Delete

Elimina un certificado emitido y su referencia en notas de otras regiones.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra el registro en `e_certificados_rstgr` y, si tiene número, llama a
`PersonaNotaOtraRegionStgrRepository::deleteCertificado`. Acepta `id_item` directo o extraído de
`sel[0]` (token antes del `#`).

## Endpoint

- URL: `/src/certificados/certificado_emitido_delete`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_delete.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `string` | controller | No | Lista; usa `id_item` antes del `#` |
| `id_item` | `integer` | controller | No | Alternativa a `sel` |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: "ok"`

## Errores conocidos

- `No se encuentra el certificado` (`id_item` ≤ 0 o registro inexistente)
- Errores de BD del repositorio en `mensaje`

## Permisos

- Botón expuesto solo si `soy_region_stgr()` en el builder de lista; sin check adicional aquí.

## Casos De Uso

- `src\certificados\domain\CertificadoEmitidoDelete`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_lista.php`: acción `fnjs_eliminar`.
