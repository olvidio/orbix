---
id: "actividadplazas.peticiones_incorporar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Incorporar las primeras peticiones"
capacidad: "actividadplazas.peticiones_incorporar.gestionar"
pantallas_principales: ["actividadplazas.pantalla.incorporar_peticion"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Peticiones Incorporar

Incorpora en bloque la primera petición de plaza de cada persona como asistencia con plaza asignada o
pedida, según organice la actividad mi dl u otra.

## Objetivo De Usuario

Convertir de golpe las primeras peticiones de plaza de cada persona en asistencias propias con plaza, para un tipo de actividad y un colectivo. No se incorpora a quien ya tiene una actividad propia en ese periodo.

## Punto De Entrada

Menú de plazas > **Incorporar 1ª petición**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Incorporar las primeras peticiones

Pasos propuestos:
1. Abrir **Incorporar peticiones de plazas** desde el menú (según el tipo de actividad y el colectivo).
2. Leer el texto explicativo y pulsar **Continuar**. El botón se desactiva mientras se ejecuta.
3. Al terminar se muestra cuántas peticiones se han incorporado, con el recordatorio de que no se incorpora a quien ya tiene actividad propia.

Endpoints asociados:
- `/src/actividadplazas/peticiones_incorporar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sactividad`
- `form.sasistentes`
- `post.sactividad`
- `post.sasistentes`

Acciones JavaScript:
- `fnjs_incorporar_peticiones`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/actividadplazas/peticiones_incorporar`

## Errores Conocidos

- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** vsm > ca > Incorporar 1ª petición (y variantes por perfil/tipo: dagd, crt…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Incorporar 1ª petición > ca n (y variantes por tipo/colectivo)
