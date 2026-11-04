---
id: "actividades.actividad_permiso_crear.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Crear una actividad con o sin permiso"
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

Al crear una actividad nueva, permitir o bloquear el formulario según los permisos de proceso del usuario.

## Punto De Entrada

Menú **actividades > Nueva actividad**.

## Escenarios Inferidos

### Crear una actividad con o sin permiso

Pasos propuestos:
1. Abrir una actividad nueva.
2. Si el usuario tiene permiso de crear para ese tipo, el formulario está activo.
3. Si no, el formulario se muestra bloqueado y hay que pedir el permiso.

Endpoints asociados:
- `/src/actividades/actividad_permiso_crear_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_permiso_crear_datos`

## Ruta de menú

- **Legacy / Pills2:** entradas *nueva activ* (ver `actividad_ver`).
