---
id: "encargossacd.sacd_ausencias_jefe_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Sacd Ausencias Jefe Zona"
capacidad: "encargossacd.sacd_ausencias_jefe_zona.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ausencias_jefe_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Ausencias Jefe Zona

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ausencias_jefe_zona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SacdAusenciasJefeZona. Datos para el listado de SACDs susceptibles de gestionar ausencias desde la ficha de jefe de zona (frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php). Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde (Oficial_dl o jefe de calendario), la totalidad de SACDs activos. El array se devuelve ordenado por iniciales para alimentar el desplegable.

## Punto De Entrada

Menú: exterior > sacd > Misas > Ausencias.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ausencias_jefe_zona`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.filtro_sacd`
- `form.historial`
- `form.id_nom`

Acciones JavaScript:
- `fnjs_horario`
- `fnjs_lista_sacd`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ausencias_jefe_zona_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice

