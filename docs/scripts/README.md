# Scripts De Documentacion

Esta carpeta contiene scripts auxiliares para generar documentacion tecnica a partir del codigo.

## Generar Toda La Documentacion De Un Modulo

```bash
docs/scripts/generar_documentacion_modulo.sh actividadtarifas
```

Ejecuta la cadena completa:

```text
api -> capacidades -> pantallas -> relaciones -> flujos -> manual -> ayuda IA -> OpenAPI -> validacion OpenAPI
```

Antes de generar, comprueba sintaxis de todos los scripts PHP y Bash.

Opciones utiles:

```bash
docs/scripts/generar_documentacion_modulo.sh actividadtarifas --dry-run
docs/scripts/generar_documentacion_modulo.sh actividadtarifas --force
docs/scripts/generar_documentacion_modulo.sh actividadtarifas --skip-openapi-validation
docs/scripts/generar_documentacion_modulo.sh actividadtarifas --only-syntax
```

Para un modulo nuevo normalmente basta con el comando sin opciones. Para regenerar un modulo ya documentado, usa `--force`.

## Generar Fichas API De Un Modulo

```bash
php docs/scripts/generar_api_modulo_md.php actividadtarifas
```

Por defecto escribe en:

```text
docs/catalogo/<modulo>/api/
```

Opciones utiles:

```bash
php docs/scripts/generar_api_modulo_md.php actividadtarifas --dry-run
php docs/scripts/generar_api_modulo_md.php actividadtarifas --force
php docs/scripts/generar_api_modulo_md.php actividadtarifas --output=docs/catalogo
```

El contenido generado es una primera version: extrae rutas, parametros, respuesta y referencias frontend, pero requiere revision manual para completar objetivo funcional, permisos, efectos y ejemplos reales.

## Generar Capacidades Desde El Catalogo API

```bash
php docs/scripts/generar_capacidades_modulo.php actividadtarifas
```

Lee:

```text
docs/catalogo/<modulo>/api/*.md
```

Y escribe:

```text
docs/catalogo/<modulo>/capacidades/*.md
```

Agrupa endpoints por prefijo comun y sufijos conocidos (`*_lista_data`, `*_form_data`, `*_update`, `*_eliminar`, etc.). La salida es una propuesta para revision manual.

Opciones utiles:

```bash
php docs/scripts/generar_capacidades_modulo.php actividadtarifas --dry-run
php docs/scripts/generar_capacidades_modulo.php actividadtarifas --force
php docs/scripts/generar_capacidades_modulo.php actividadtarifas --output=docs/catalogo
```

## Generar Pantallas Frontend

```bash
php docs/scripts/generar_pantallas_modulo.php actividadtarifas
```

Lee:

```text
frontend/<modulo>/controller/*.php
frontend/<modulo>/view/*.phtml
docs/catalogo/<modulo>/capacidades/*.md
```

Y escribe:

```text
docs/catalogo/<modulo>/pantallas/*.md
```

Detecta vistas renderizadas, fragmentos frontend relacionados, endpoints `/src/...`, capacidades relacionadas, campos de formulario y acciones JavaScript. La salida es una base para redactar manual de usuario y ayuda local.

Opciones utiles:

```bash
php docs/scripts/generar_pantallas_modulo.php actividadtarifas --dry-run
php docs/scripts/generar_pantallas_modulo.php actividadtarifas --force
php docs/scripts/generar_pantallas_modulo.php actividadtarifas --output=docs/catalogo
```

## Generar Relaciones Pantallas API

```bash
php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas
```

Lee:

```text
docs/catalogo/<modulo>/pantallas/*.md
docs/catalogo/<modulo>/api/*.md
docs/catalogo/<modulo>/capacidades/*.md
```

Y escribe:

```text
docs/catalogo/<modulo>/relaciones/pantallas_api.md
```

El informe cruza pantallas, endpoints directos y endpoints aportados por capacidades. Tambien lista endpoints sin pantalla directa para revision.

Opciones utiles:

```bash
php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas --dry-run
php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas --force
php docs/scripts/generar_relaciones_pantallas_api.php actividadtarifas --output=docs/catalogo
```

## Generar Flujos Frontend

```bash
php docs/scripts/generar_flujos_modulo.php actividadtarifas
```

Lee:

```text
docs/catalogo/<modulo>/capacidades/*.md
docs/catalogo/<modulo>/pantallas/*.md
```

Y escribe:

```text
docs/catalogo/<modulo>/flujos/*.md
```

Genera un flujo por capacidad, con punto de entrada, fragmentos auxiliares, escenarios inferidos por accion, campos detectados, acciones JavaScript y endpoints implicados. La salida es una base para redactar manual de usuario.

Opciones utiles:

```bash
php docs/scripts/generar_flujos_modulo.php actividadtarifas --dry-run
php docs/scripts/generar_flujos_modulo.php actividadtarifas --force
php docs/scripts/generar_flujos_modulo.php actividadtarifas --output=docs/catalogo
```

## Generar Manual De Usuario

```bash
php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas
```

Lee:

```text
docs/catalogo/<modulo>/flujos/*.md
docs/catalogo/<modulo>/pantallas/*.md
```

Y escribe:

```text
docs/manual/<modulo>.md
```

Genera un borrador orientado a usuario final: para que sirve cada flujo, donde entrar, tareas habituales, errores frecuentes pendientes y referencias internas al catalogo. Requiere revision manual para completar rutas de menu, permisos y nombres visibles.

Opciones utiles:

```bash
php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas --dry-run
php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas --force
php docs/scripts/generar_manual_usuario_modulo.php actividadtarifas --output=docs/manual
```

## Generar Ayuda Para IA Local

```bash
php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas
```

Lee:

```text
docs/catalogo/<modulo>/flujos/*.md
docs/catalogo/<modulo>/pantallas/*.md
docs/catalogo/<modulo>/api/*.md
```

Y escribe documentos pequenos para RAG en:

```text
docs/ai/<modulo>/
```

Genera un indice, ayuda por flujo, ayuda por pantalla y resumen tecnico de API. Las fichas incluyen preguntas probables, pasos de respuesta, limites para no inventar permisos/rutas y referencias internas para verificacion.

Opciones utiles:

```bash
php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas --dry-run
php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas --force
php docs/scripts/generar_ayuda_ia_modulo.php actividadtarifas --output=docs/ai
```

## Generar OpenAPI Desde El Catalogo

```bash
php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas
```

Lee:

```text
docs/catalogo/<modulo>/api/*.md
```

Y escribe:

```text
docs/catalogo/<modulo>/openapi.yaml
```

Opciones utiles:

```bash
php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas --dry-run
php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas --force
php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas --output=docs/catalogo
php docs/scripts/generar_openapi_desde_catalogo.php actividadtarifas --output=docs/catalogo/actividadtarifas/openapi.yaml
```

El OpenAPI generado documenta `POST` cuando detecta entradas `post.*`, `GET` cuando detecta entradas `get.*`, y usa esquemas comunes para las respuestas de `ContestarJson`.

## Validar OpenAPI

```bash
docs/scripts/validar_openapi.sh actividadtarifas
```

Por defecto valida:

```text
docs/catalogo/<modulo>/openapi.yaml
```

El script ejecuta primero Redocly y, si pasa, despues OpenAPI Generator:

```bash
npx --yes @redocly/cli lint docs/catalogo/actividadtarifas/openapi.yaml
npx --yes @openapitools/openapi-generator-cli validate -i docs/catalogo/actividadtarifas/openapi.yaml
```

Tambien se puede pasar una ruta concreta:

```bash
docs/scripts/validar_openapi.sh actividadtarifas docs/catalogo/actividadtarifas/openapi.yaml
```

