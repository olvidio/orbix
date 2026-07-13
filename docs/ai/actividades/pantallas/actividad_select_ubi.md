---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Seleccionar lugar (popup)"
pantalla: "actividades.pantalla.actividad_select_ubi"
preguntas: ["Que se puede hacer en Seleccionar lugar (popup)?", "Que campos tiene Seleccionar lugar (popup)?", "Que acciones hay en Seleccionar lugar (popup)?"]
capacidades: ["actividades.actividad_select_ubi_desplegable.gestionar", "actividades.actividad_tipo.gestionar"]
endpoints: ["/src/actividades/actividad_select_ubi_desplegable", "/src/actividades/actividad_tipo_get"]
source: "docs/catalogo/actividades/pantallas/actividad_select_ubi.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Seleccionar lugar (popup)

## Resumen

Ventana auxiliar para **elegir ubicación** de una actividad. Cinco modos: historial reciente, región, búsqueda por nombre (enlace a `ubis_lista.php`), lugar especial o *por determinar*. Los desplegables dinámicos cargan vía `actividad_select_ubi_desplegable`; la cascada país/dl usa `actividad_tipo_get` (`salida=lugar`). Al aceptar, devuelve `id_ubi` y texto al `window.opener` (ficha `actividad_ver`, planning, etc.).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl_org`
- `form.entrada`
- `form.extendida`
- `form.filtro_lugar`
- `form.frm_4_nombre_ubi`
- `form.id_ubi_1`
- `form.isfsv`
- `form.lst_lugar`
- `form.modo`
- `form.nombre_ubi`
- `form.salida`
- `form.tipo`
- `html.b_buscar`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_cargar_desplegable`
- `fnjs_construir_desplegable`
- `fnjs_enviar_form`
- `fnjs_lugar`

## Capacidades Relacionadas

- `actividades.actividad_select_ubi_desplegable.gestionar`
- `actividades.actividad_tipo.gestionar`

## Endpoints Relacionados

- `/src/actividades/actividad_select_ubi_desplegable`
- `/src/actividades/actividad_tipo_get`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
