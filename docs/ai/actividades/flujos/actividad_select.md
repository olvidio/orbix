---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ver y trabajar con la lista de actividades encontradas"
flujo: "actividades.actividad_select.gestionar.flujo"
preguntas: ["Como ver y trabajar con la lista de actividades encontradas en Ver y trabajar con la lista de actividades encontradas?"]
pantallas_principales: ["actividades.pantalla.actividad_select"]
fragmentos: ["actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_select_datos"]
source: "docs/catalogo/actividades/flujos/actividad_select.md"
estado_revision: "generado"
---

# Ayuda IA - Ver y trabajar con la lista de actividades encontradas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver y trabajar con la lista de actividades encontradas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver y trabajar con la lista de actividades encontradas en Ver y trabajar con la lista de actividades encontradas?

## Donde Entrar

- Resultados buscar actividad (`actividades.pantalla.actividad_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver y trabajar con la lista de actividades encontradas

1. Tras **buscar** aparece el título «N actividades encontradas (M sin permiso) entre fecha y fecha» y una tabla con inicio, fin, actividad, horas, sf/sv, tarifa, Sacd, dl org, centro, observaciones e idioma.
2. Si hay muchas filas, antes de la tabla aparece un aviso y hay que confirmar que se quiere continuar.
3. Marcar la casilla de la actividad (o de varias, según el botón) y pulsar una de las acciones: **datos**, **duplicar**, **borrar**, **cambiar tipo**, **cargos**, **lista cl**, **asistentes**, **otras peticiones**, **lista**, **plazas**, **proceso**, **asignaturas**, **plan estudios**, **listas de clase** o **posibles asignaturas**.
4. Pulsar el nombre de una actividad abre directamente su ficha.
5. Debajo de la tabla, **nueva actividad del mismo tipo** y **nueva actividad sv n** (el tipo que se buscó) crean una actividad nueva.
6. **realizar otra búsqueda** (arriba) vuelve al formulario de búsqueda.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_select_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_select`
- `actividades.pantalla.actividad_select`

## Objetivo

Ver las actividades que cumplen la búsqueda y trabajar con las que se marquen: abrir su ficha, duplicarlas, borrarlas, cambiar su tipo o ir a sus asistentes, cargos, plazas o proceso.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
