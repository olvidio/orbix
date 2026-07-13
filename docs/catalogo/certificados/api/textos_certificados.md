---
id: "certificados.textos_certificados"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/textos_certificados"
metodos: ["GET", "POST"]
operacion: "include"
controller: "src/certificados/infrastructure/ui/http/controllers/textos_certificados.php"
entrada: []
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["certificados", "textos"]
estado_revision: "revisado"
---

# Textos Certificados

Plantilla PHP de textos legales en latín para certificados STGR.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Fichero incluido (`include`) por `certificado_emitido_imprimir_mpdf_datos.php` que define variables
(`txt_superavit_*`, `titulo_*`, `infra`, `sello`, etc.) usando `$region_latin`, `$nom`,
`$lugar_nacimiento`, `$f_nacimiento` del contexto. Existe ruta HTTP registrada pero el uso real es
como plantilla, no como API JSON. Traducciones por idioma en `$dir_languages/<idioma>/textos_certificados.php`.

## Endpoint

- URL: `/src/certificados/textos_certificados`
- Metodos registrados: `GET, POST`
- Operacion: `include`
- Controller: `src/certificados/infrastructure/ui/http/controllers/textos_certificados.php`

## Entrada

Variables de contexto inyectadas por el controlador que hace `include` (no POST).

## Salida

- Sin respuesta HTTP útil en llamada directa (solo define variables PHP).
- Consumido indirectamente vía payload de `certificado_emitido_imprimir_mpdf_datos`.

## Errores conocidos

- Ninguno en la plantilla latín; traducciones inexistentes se detectan en `imprimir_mpdf_datos`.

## Permisos

- No aplica (include server-side).

## Casos De Uso

- Plantilla incluida; no caso de uso application.

## Frontend Relacionado

- No invocado desde frontend; solo backend al generar PDF.
