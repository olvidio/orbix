---
id: "actividades.actividad_permiso_crear.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Permiso crear actividad"
capacidad: "actividades.actividad_permiso_crear.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_permiso_crear_datos"]
estado_revision: "revisado"
---

# Flujo - Permiso crear actividad

Comprueba si el usuario puede crear actividades del tipo elegido (dl propia y dl externa
si la oficina responsable no es la del usuario), con módulo `procesos` instalado.

## Objetivo De Usuario

Al crear ficha nueva, el sistema bloquea o permite el formulario según permisos de proceso.

## Punto De Entrada

`actividad_ver` modo *nuevo* con `procesos` → helper `PrefillPermActividadesFases` /
consulta `actividad_permiso_crear_datos`.

## Endpoints Del Flujo

- `/src/actividades/actividad_permiso_crear_datos`

## Ruta de menú

- **Legacy / Pills2:** entradas *nueva activ* (ver `actividad_ver`).
