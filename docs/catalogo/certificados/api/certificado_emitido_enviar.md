---
id: "certificados.certificado_emitido_enviar"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_enviar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_enviar.php"
entrada: ["post.id_item:integer", "post.sel:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_lista.php"]
casos_uso: ["src\\certificados\\domain\\CertificadoEmitidoEnviar"]
tags: ["certificados", "certificado", "emitido", "enviar"]
estado_revision: "revisado"
---

# Certificado Emitido Enviar

Envía un certificado emitido a la delegación destino del alumno (copia + anuncio).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Marca `f_enviado`, copia el certificado a la DL destino vía `Trasladar::copiar_certificados_a_dl`
y crea un anuncio en el tablón `vest|Estudios` de la región STGR destino. Rechaza personas de
paso (`id_nom < 0`) y delegaciones fuera de Orbix.

## Endpoint

- URL: `/src/certificados/certificado_emitido_enviar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_enviar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `string` | controller | No | Lista; usa `id_item` antes del `#` |
| `id_item` | `integer` | controller | No | Alternativa a `sel` |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: "ok"` (puede haber avisos no bloqueantes en `mensaje`)

## Errores conocidos

- `No se encuentra el certificado`
- `Es una persona de paso. No se puede enviar. Hay que imprimir.`
- `No se puede determinar la delegación destino`
- `Hay que enviar manualmente el certificado. Esta persona no está en aquinate`
- Mensajes de error al resolver región STGR destino o al trasladar

## Permisos

- Botón expuesto solo si `soy_region_stgr()` en el listado; sin check adicional aquí.

## Casos De Uso

- `src\certificados\domain\CertificadoEmitidoEnviar`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_lista.php`: acción `fnjs_enviar_certificado`.
