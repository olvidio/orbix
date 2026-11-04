---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Abrir la ficha de una actividad"
flujo: "actividades.actividad_ver.gestionar.flujo"
preguntas: ["Como abrir la ficha de una actividad en Abrir la ficha de una actividad?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
endpoints: ["/src/actividades/actividad_ver_datos"]
source: "docs/catalogo/actividades/flujos/actividad_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir la ficha de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir la ficha de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir la ficha de una actividad en Abrir la ficha de una actividad?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir la ficha de una actividad

1. Buscar la actividad y marcar su casilla en la lista.
2. Pulsar **datos** (o pulsar el nombre de la actividad).
3. Se abre «DATOS ACTIVIDAD» con el título de la actividad, el enlace **(dossiers)** y el tipo (sf/sv, asistentes, actividad y tipo de actividad, solo lectura).
4. Los campos son: **Estado**, **Nombre actividad** (con el enlace **generar**), **Fecha inicio**, **Hora inicio**, **Fecha fin**, **Hora fin**, **dl/r que organiza**, **Plazas totales**, **Lugar**, **Tarifa**, **Precio**, **Observaciones**, **Repetición anual por**, **Nivel de stgr**, **publicado** (sí/no) e **Idioma**.
5. Según los permisos, la ficha se puede modificar o es solo de consulta.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_ver_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Objetivo

Abrir la ficha de una actividad (o el formulario de una nueva) con todos sus datos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
