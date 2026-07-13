---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadescentro"
titulo: "Activ Ctr"
pantalla: "actividadescentro.pantalla.activ_ctr"
preguntas: ["Que se puede hacer en Activ Ctr?", "Que campos tiene Activ Ctr?", "Que acciones hay en Activ Ctr?"]
capacidades: ["actividadescentro.activ_ctr_shell.gestionar"]
endpoints: ["/src/actividadescentro/activ_ctr_shell_data", "/src/actividadescentro/lista_actividades_ctr_data", "/src/actividadescentro/centros_encargados_data", "/src/actividadescentro/centros_disponibles_data", "/src/actividadescentro/centro_encargado_asignar", "/src/actividadescentro/centro_encargado_reordenar", "/src/actividadescentro/centro_encargado_eliminar"]
source: "docs/catalogo/actividadescentro/pantallas/activ_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Activ Ctr

## Resumen

Pantalla principal del módulo `actividadescentro`: lista las actividades de un colectivo (`tipo`) en un periodo y permite gestionar, por actividad, los **centros encargados** (asignar, reordenar, eliminar).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.periodo`
- `form.tipo`
- `form.year`
- `post.periodo`
- `post.tipo`
- `post.year`

## Acciones Detectadas

- `fnjs_actualizar_activ`
- `fnjs_asignar_ctr`
- `fnjs_cambiar_ctr`
- `fnjs_cerrar`
- `fnjs_construir_celda_ctrs`
- `fnjs_construir_tabla_disponibles`
- `fnjs_construir_tabla_lista`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_esc`
- `fnjs_left_side_hide`
- `fnjs_nuevo_ctr`
- `fnjs_parse_rta`
- `fnjs_reordenar`
- `fnjs_ver`

## Capacidades Relacionadas

- `actividadescentro.activ_ctr_shell.gestionar`

## Endpoints Relacionados

- `/src/actividadescentro/activ_ctr_shell_data`
- `/src/actividadescentro/lista_actividades_ctr_data`
- `/src/actividadescentro/centros_encargados_data`
- `/src/actividadescentro/centros_disponibles_data`
- `/src/actividadescentro/centro_encargado_asignar`
- `/src/actividadescentro/centro_encargado_reordenar`
- `/src/actividadescentro/centro_encargado_eliminar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
