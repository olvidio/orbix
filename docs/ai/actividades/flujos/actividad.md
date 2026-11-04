---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Crear una actividad"
flujo: "actividades.actividad.gestionar.flujo"
preguntas: ["Como crear una actividad en Crear una actividad?", "Como borrar una actividad en Crear una actividad?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_eliminar", "/src/actividades/actividad_nuevo"]
source: "docs/catalogo/actividades/flujos/actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Crear una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear una actividad en Crear una actividad?
- Como borrar una actividad en Crear una actividad?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear una actividad

1. Pulsar **nueva actividad…** debajo de la lista (o la opción de nueva actividad del menú). Se abre «Nueva actividad» con el estado «proyecto».
2. Elegir el tipo: **sf/sv**, **asistentes**, **actividad** y **tipo de actividad** (es obligatorio concretar los dos últimos; si no, avisa «debe concretar la actividad en el tipo de actividad» o «debe concretar el tipo de actividad»).
3. Rellenar el nombre (o pulsar **generar** para que lo proponga), las fechas y los demás datos: **Estado**, **Nombre actividad** (con el enlace **generar**), **Fecha inicio**, **Hora inicio**, **Fecha fin**, **Hora fin**, **dl/r que organiza**, **Plazas totales**, **Lugar**, **Tarifa**, **Precio**, **Observaciones**, **Repetición anual por**, **Nivel de stgr**, **publicado** (sí/no) e **Idioma**.
4. Pulsar **Crear ficha**.
5. La actividad queda creada; el formulario se vacía y no muestra mensaje. Para verla, buscarla con **Buscar actividad** (estado «proyecto» o «cualquiera»).

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_eliminar`
- `/src/actividades/actividad_nuevo`

## Borrar una actividad

1. En la lista, marcar **una** actividad y pulsar **borrar**.
2. Confirmar «¿Está seguro que desea borrar esta actividad?».
3. La actividad desaparece al volver a buscar.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_eliminar`
- `/src/actividades/actividad_nuevo`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.actividad_select`

## Objetivo

Dar de alta una actividad nueva o borrar una existente.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
