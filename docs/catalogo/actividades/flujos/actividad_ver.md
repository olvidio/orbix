---
id: "actividades.actividad_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Cargar datos de ficha actividad"
capacidad: "actividades.actividad_ver.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_ver_datos"]
estado_revision: "revisado"
---

# Flujo - Cargar datos de ficha actividad

Bootstrap del formulario de actividad: entidad, desplegables y HTML auxiliar.

## Objetivo De Usuario

Al abrir ver/editar/nuevo/planning, el sistema carga en servidor los datos necesarios
para pintar la ficha sin acceder a `src/` desde el navegador.

## Punto De Entrada

Controllers `actividad_ver`, `planning_casa_nueva`, `planning_casa_modificar` al renderizar.

## Escenarios

### Obtener Datos

1. Controller recibe `id_activ` (0 si nueva), `dl_org`, `isfsv`, `id_ubi`, `id_tipo_activ`.
2. POST a `actividad_ver_datos`.
3. Respuesta incluye valores de formulario, opciones de desplegables y flags de permiso.

## Endpoints Del Flujo

- `/src/actividades/actividad_ver_datos`

## Errores Conocidos

Errores de permiso o actividad inexistente se gestionan en el controller frontend antes/después
del POST (ver ficha API).

## Ruta de menú

Misma que `actividad_ver` / planning (sin menú propio para el paso AJAX).
