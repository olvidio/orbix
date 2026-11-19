---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "shared"
endpoints: 11
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - shared

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/shared/ayuda_consulta_valorar`

- Id: `shared.ayuda_consulta_valorar`
- Controller: `src/shared/infrastructure/ui/http/controllers/ayuda_consulta_valorar.php`
- Entrada: `post.ctx_valorar:string` (cápsula `HashB` que devuelve `/src/shared/ayuda_preguntar` con cada respuesta), `post.util:string` (`1` sirvió, `0` no sirvió), `post.comentario:string` (opcional, qué se buscaba; máx. 1000)
- Respuesta: `standard_envelope_string_data` (`ok`); sin cápsula válida → «Operación no autorizada».

## `/src/shared/ayuda_consultas_estado_update`

- Id: `shared.ayuda_consultas_estado_update`
- Controller: `src/shared/infrastructure/ui/http/controllers/ayuda_consultas_estado_update.php`
- Entrada: `post.ctx_estado:string` (cápsula `HashB` de la fila, firmada por `/src/shared/ayuda_consultas_lista_data`: consultas del grupo y estado nuevo). Solo administradores (`admin_sv` / `admin_sf`).
- Respuesta: `standard_envelope_string_data` (`ok`)

## `/src/shared/ayuda_consultas_lista_data`

- Id: `shared.ayuda_consultas_lista_data`
- Controller: `src/shared/infrastructure/ui/http/controllers/ayuda_consultas_lista_data.php`
- Entrada: `post.estado:integer` (1 pendiente, 2 resuelta, 3 descartada; por defecto 1), `post.ver:string` (`problemas` por defecto | `todas`)
- Respuesta: `standard_envelope_string_data` (`estado`, `ver`, `opciones_estado` pares, `total_consultas`, `grupos[{pregunta, veces, ultima, no_sirvio, sirvio, comentarios[], esquemas[], pantallas[], n_resultados, puntuacion, fuente, titulo, ctx_resolver, ctx_descartar, ctx_reabrir}]`, `tsv`); quien no es administrador recibe solo `aviso_txt`.

## `/src/shared/ayuda_pantalla`

- Id: `shared.ayuda_pantalla`
- Controller: `src/shared/infrastructure/ui/http/controllers/ayuda_pantalla.php`
- Entrada: `post.controller:string` (ruta `frontend/<mod>/controller/<x>.php` o URL que la contenga), `post.limite:integer` (opcional, 6 por defecto, máx. 30)
- Respuesta: `standard_envelope_string_data` (`encontrada`, `controller`, `titulo`, `modulo`, `manual_modulo`, `total_tareas`, `tareas[{modulo, ancla, titulo, sirve}]`, `aviso_txt`); pantalla sin ayuda → `encontrada: false` con `aviso_txt`, no es error. Fuente: `docs/manual/_indice_pantallas.json` (`php docs/scripts/generar_indice_ayuda.php`).

## `/src/shared/ayuda_preguntar`

- Id: `shared.ayuda_preguntar`
- Controller: `src/shared/infrastructure/ui/http/controllers/ayuda_preguntar.php`
- Entrada: `post.pregunta:string`, `post.limite:integer` (opcional, 8 por defecto, máx. 20), `post.pantalla:string` (opcional, ruta `frontend/<mod>/controller/<x>.php` desde la que se abrió la ayuda)
- Respuesta: `standard_envelope_string_data` (`pregunta`, `respuesta`, `modo`, `resultados[{titulo, tipo, modulo, fuente, excerpt, score, preguntas, pasos}]`, `ctx_valorar`). Cada pregunta no vacía se anota en `ayuda_consultas` (BD comun) sin el usuario: delegación, pantalla y primer resultado.

## `/src/shared/locales_posibles`

- Id: `shared.locales_posibles`
- Controller: `src/shared/infrastructure/ui/http/controllers/locales_posibles.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_buscar_datos`

- Id: `shared.tablaDB_buscar_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos.php`
- Entrada: `post.aSerieBuscar:string`, `post.clase_info:string`, `post.id_pau:integer`, `post.k_buscar:string`, `post.obj_pau:string`, `post.pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_depende_datos`

- Id: `shared.tablaDB_depende_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_depende_datos.php`
- Entrada: `post.accion:string`, `post.clase_info:string`, `post.opcion_sel:string`, `post.pKeyRepository:string`, `post.valor_depende:string`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_formulario_datos`

- Id: `shared.tablaDB_formulario_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_formulario_datos.php`
- Entrada: `post.a_pkey:mixed`, `post.clase_info:string`, `post.mod:string`, `post.obj_pau:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_lista_datos`

- Id: `shared.tablaDB_lista_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_lista_datos.php`
- Entrada: `post.clase_info:string`, `post.id_pau:integer`, `post.k_buscar:string`, `post.obj_pau:integer`, `post.pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_update`

- Id: `shared.tablaDB_update`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_update.php`
- Entrada: `post.clase_info:string`, `post.go_to:string`, `post.id_pau:string`, `post.mod:string`, `post.obj_pau:string`, `post.s_pkey:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`
