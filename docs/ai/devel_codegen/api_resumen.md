---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "devel_codegen"
endpoints: 1
estado_revision: "revisado"
---

# Resumen API Para Ayuda IA - devel_codegen

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/devel_codegen/tabla_estructura_datos`

- Id: `devel_codegen.tabla_estructura_datos`
- Controller: `src/devel_codegen/infrastructure/ui/http/controllers/tabla_estructura_datos.php`
- Entrada: `post.db:string` (`tramity`, `davical`, `comun`, `sv`, `sf`, `sv-e`), `post.tabla:string` (`tabla` o `esquema.tabla`)
- Respuesta: `standard_envelope_string_data` (`primary_key: list<string>`, `columnas: list<{attnum, field, type, length, lengthvar, notnull, default}>`, `hoy`). Solo lectura. Error si falta un parámetro, la base de datos no se conoce o la tabla no tiene clave primaria. Lo usa el generador `frontend/devel_codegen/controller/factory.php`.
