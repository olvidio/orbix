---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "profesores"
endpoints: 6
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - profesores

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/profesores/congresos`

- Id: `profesores.congresos`
- Controller: `src/profesores/infrastructure/ui/http/controllers/congresos.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/profesores/docencia`

- Id: `profesores.docencia`
- Controller: `src/profesores/infrastructure/ui/http/controllers/docencia.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/profesores/ficha_profesor_stgr`

- Id: `profesores.ficha_profesor_stgr`
- Controller: `src/profesores/infrastructure/ui/http/controllers/ficha_profesor_stgr.php`
- Entrada: `post.depende:string`, `post.id_nom:integer`, `post.id_tabla:string`, `post.obj_pau:string`, `post.permiso:string`, `post.print:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/profesores/lista_por_departamentos`

- Id: `profesores.lista_por_departamentos`
- Controller: `src/profesores/infrastructure/ui/http/controllers/lista_por_departamentos.php`
- Entrada: `post.dl:array`, `post.filtro:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/profesores/profesor_asignatura_ajax`

- Id: `profesores.profesor_asignatura_ajax`
- Controller: `src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_ajax.php`
- Entrada: `post.id_asignatura:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/profesores/profesor_asignatura_que`

- Id: `profesores.profesor_asignatura_que`
- Controller: `src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_que.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`
