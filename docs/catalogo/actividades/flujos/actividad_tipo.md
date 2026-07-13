---
id: "actividades.actividad_tipo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Cascada tipo actividad (AJAX)"
capacidad: "actividades.actividad_tipo.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_ver"]
acciones: ["obtener"]
endpoints: ["/src/actividades/actividad_tipo_get"]
estado_revision: "revisado"
---

# Flujo - Cascada tipo actividad (AJAX)

Desplegables dinámicos de la cascada sf/sv → asistentes → actividad → tipo (y variantes
`salida=lugar`, `id_tarifa`, etc.).

## Objetivo De Usuario

Al cambiar un nivel de la cascada, actualizar los desplegables dependientes sin recargar
toda la página.

## Punto De Entrada

- `actividad_que` / ficha (`fnjs_asistentes`, `fnjs_actividad`, `fnjs_nom_tipo`, `fnjs_id_activ`).
- `actividad_select_ubi` (`fnjs_lugar`).

## Escenarios

### Obtener

1. onchange envía `entrada` (código parcial) y `salida` deseada.
2. `actividad_tipo_get` devuelve HTML `<option>` o datos según salida.
3. JS sustituye el desplegable correspondiente.

## Endpoints Del Flujo

- `/src/actividades/actividad_tipo_get`

## Ruta de menú

sin entrada propia (AJAX dentro de buscar actividad, ficha o popup lugar).
