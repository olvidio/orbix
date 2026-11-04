---
id: "personas.personas_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Buscar y listar personas"
capacidad: "personas.personas_select.gestionar"
pantallas_principales: ["personas.pantalla.personas_que", "personas.pantalla.personas_select"]
fragmentos: []
acciones: ["buscar", "seleccionar", "acciones_listado"]
endpoints: ["/src/personas/personas_select_data"]
estado_revision: "revisado"
---

# Flujo - Buscar y listar personas

Búsqueda por criterios y visualización del listado con acciones sobre la selección.

## Objetivo De Usuario

Buscar personas y trabajar con las que salgan: abrir su ficha, ver sus actividades, notas, tessera, trasladarlas, etc.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**, **n de paso**, **agd r/dl**; también en vsm, vsg y scdl).

## Escenarios Inferidos

### Buscar y listar personas

Pasos propuestos:
1. Abrir «Búsqueda de personas» (por ejemplo «numerarios»). Elegir **realizar una búsqueda exacta** (no / sí) y escribir **Nombre**, **Primer apellido**, **Segundo apellido** o **centro**.
2. Pulsar **Buscar**: sale «N personas encontradas» con una tabla.
3. Pulsar el nombre de una persona abre su ficha. Para actuar sobre una o varias, marcarlas y pulsar un botón: **cambio de ctr**, **ver dossiers**, **ficha**, **ver actividades**, **ver tessera**, **modificar stgr**, **imprimir tessera**, **ver notas**, **posibles ca**, **petición ca**, **petición crt**, **plan estudios**, **ficha profesor stgr**, **copiar tessera**, **publicar** o **atención actividades**.

Endpoints asociados:
- `/src/personas/personas_select_data`

## Endpoints Del Flujo

- `/src/personas/personas_select_data`
- (derivado publicar) `/src/personas/persona_publicar_form_data`, `/src/personas/persona_publicar`

## Errores Conocidos

- `No se encuentra ningún centro con esta condición`
- Avisos suaves región/persona no válida (listado vacío con mensaje)

## Ruta de menú

Herencia de `personas_que` — ver variantes vsm/PERSONAS en `_referencia_menus.md`
(p. ej. `vsm > buscar n > n r/dl` / `PERSONAS > Numerarios > Buscar n de la r/dl`).
