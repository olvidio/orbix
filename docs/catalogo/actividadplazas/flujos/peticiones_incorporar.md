---
id: "actividadplazas.peticiones_incorporar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Peticiones Incorporar"
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

Ejecutar el proceso masivo que convierte las primeras peticiones de plaza (orden = 1) en asistencias
propias con plaza, para un tipo y colectivo, sin incorporar personas que ya tienen actividad propia en
el periodo.

## Punto De Entrada

Menú de plazas → **Incorporar 1ª petición** (ver "Ruta de menú"). Se abre
`actividadplazas.pantalla.incorporar_peticion`.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

1. Abrir **Incorporar peticiones de plazas** desde el menú (según tipo y colectivo).
2. Leer el texto explicativo y pulsar **Continuar** (`fnjs_incorporar_peticiones`).
3. El botón se deshabilita mientras se ejecuta; el sistema envía `sactividad` y `sasistentes` a
   `peticiones_incorporar`.
4. Muestra en `#resultado` cuántas peticiones se incorporaron (`incorporadas`) y el aviso de que no
   se incorporan personas con actividad propia ya existente (`mensaje_final`).

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
