---
tipo: "guia_revision"
alcance: "catalogo_api"
ultima_actualizacion: 2026-07-13
---

# Guía de revisión del catálogo (de `generado` a `revisado`)

El catálogo (`docs/catalogo/<modulo>/`) se **genera automáticamente desde el código** y luego se
deriva a `docs/manual/` y `docs/ai/`. Casi todo nace con `estado_revision: "generado"` (extracción
mecánica, con plantillas y algún error). "Revisar" = **contrastar cada ficha contra el código real**
(`src/<modulo>/` y `frontend/<modulo>/`), corregir y marcar `estado_revision: "revisado"`.

Módulo piloto de referencia (hecho a mano): **`actividadcargos`** — usar sus fichas API como
ejemplo del nivel de detalle esperado.

## Regla de oro de durabilidad

Las fichas del catálogo son la **fuente revisada**; los generadores las sobrescriben con `--force`.
Por tanto, en un módulo ya revisado:

- **NO** volver a ejecutar `generar_api_modulo_md.php <mod> --force` (borraría la revisión de `api/`).
- **NO** volver a ejecutar `generar_pantallas_modulo.php` / `generar_flujos_modulo.php` / `generar_capacidades_modulo.php` con `--force` si se han revisado esas capas.
- **SÍ** regenerar las capas *derivadas* (no editadas a mano): `openapi`, `docs/ai`, `docs/manual`.

Regeneración segura tras revisar la capa API:

```bash
php docs/scripts/generar_openapi_desde_catalogo.php <mod> --force
php docs/scripts/generar_ayuda_ia_modulo.php <mod> --force
php docs/scripts/generar_manual_usuario_modulo.php <mod> --force   # si se revisan flujos/pantallas
```

## Checklist por ficha API (`docs/catalogo/<mod>/api/*.md`)

Para cada endpoint, abrir su `controller` (front matter) y su(s) `casos_uso` en
`src/<mod>/application/`, y:

1. **`operacion`** (front matter y línea "Operacion" del cuerpo). Corregir la clasificación
   automática, que a menudo marca `mutacion` por defecto:
   - `*_lista_data` / builders de tabla → `lista_data`.
   - `*_form_data` y demás builders `*_data` que devuelven payload de formulario → `form_data`.
   - `*_update` / `*_nuevo` / `*_editar` / `*_eliminar` / `*_guardar` / `*_copiar` → `mutacion`.
2. **`entrada` / `entrada_obligatoria`**: verificar contra los `inputInt/inputString/inputStringList`
   del caso de uso. Anotar en "Notas" los alias reales (p. ej. `id_pau` que en realidad es `id_activ`
   o `id_nom`) y los tokens dentro de `sel` (`id_nom#id_item#...`).
3. **`errores`**: listar los mensajes `_( ... )` que devuelve el caso de uso. Reflejarlos también en
   la sección "Errores conocidos".
4. **Descripción / título**: arreglar textos truncados o con artefactos del extractor (p. ej.
   `... front ({`).
5. **`## Objetivo funcional`** (añadir): qué hace y sus ramas principales (alta/edición/selección…).
6. **`## Salida`**: sustituir el boilerplate `data: "ok"` cuando no aplique:
   - Mutaciones que devuelven string vacío en éxito → `data: "ok"` (correcto).
   - Builders `*_data` → describir las **claves reales** del payload y recordar el doble `JSON.parse`
     (`ContestarJson::enviar` serializa el array como string), salvo que usen `enviarDataAnidado`.
7. **`## Permisos`**: indicar los controles reales del caso de uso (p. ej. `perm_modificar()`) o, si
   no hay, que la autorización se resuelve en frontend + `$_SESSION['oPerm']` (no inventar permisos).
8. **`## Frontend Relacionado`**: si el extractor no encontró la URL, indicar desde qué form/listado
   se invoca (las URLs suelen emitirse en el payload como `url_*`).
9. Quitar el bloque "## Revision Manual" y poner `estado_revision: "revisado"`.

## Checklist por pantalla (`pantallas/*.md`) y flujo (`flujos/*.md`)

Segunda pasada, opcional pero recomendada:

- Corregir el **Resumen** cuando sea plantilla vacía o esté truncado.
- Verificar campos/acciones detectados contra la vista `.phtml`.
- **Ruta de menú**: si la pantalla/flujo tiene entrada en `docs/guias/_referencia_menus.md` (busca
  por el controller/URL en el índice URL→ruta o en el árbol), añade una sección con este formato
  exacto (usa la ruta real de la referencia; si no aparece, escribe "sin entrada de menú en el índice"):

  ```
  ## Ruta de menú

  - **Legacy:** grupo > nivel > entrada
  - **Pills2:** GRUPO > nivel > entrada
  ```
- `estado_revision: "revisado"`.

## Definición de "hecho" por módulo

- Todas las fichas `api/` con `estado_revision: revisado` y verificadas contra código.
- `openapi.yaml` regenerado sin errores (`generar_openapi_desde_catalogo.php <mod> --force`).
- `docs/ai/<mod>` regenerado (`generar_ayuda_ia_modulo.php <mod> --force`).
- Entrada añadida en `_PROGRESO_REVISION.md`.
