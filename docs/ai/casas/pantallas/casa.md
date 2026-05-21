---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Casa"
pantalla: "casas.pantalla.casa"
preguntas: ["Que se puede hacer en Casa?", "Que campos tiene Casa?", "Que acciones hay en Casa?"]
capacidades: ["casas.casa_ingreso.gestionar"]
endpoints: ["/src/casas/casa_ingreso_eliminar", "/src/casas/casa_ingreso_update"]
source: "docs/catalogo/casas/pantallas/casa.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Casa

## Resumen

Pantalla principal del módulo `casas` — filtro casa + periodo y delegación a distintas vistas AJAX según `tipo_lista`: - default → listado económico (`casa_ingresos_lista.php`) - `lista_activ` → listado de actividades (`casa_actividades_lista.php`) - `ctrsEncargados` → `frontend/actividades/controller/calendario_listas.php` - `datosEcGastos` → `frontend/casas/controller/casa_ec_gastos_lista.php` Migrada desde `apps/casas/controller/casa_que.php` + `casa_ajax.php` siguiendo `refactor.md`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_activ`
- `form.id_tarifa`
- `form.ingresos`
- `form.num_asistentes`
- `form.observ`
- `form.precio`
- `html.buscar`
- `post.id_ubi`
- `post.periodo`
- `post.tipo_lista`
- `post.ver_ctr`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_mas_casas`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- `casas.casa_ingreso.gestionar`

## Endpoints Relacionados

- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
