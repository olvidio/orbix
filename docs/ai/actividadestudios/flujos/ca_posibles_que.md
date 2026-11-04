---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ver el cuadro de posibles ca"
flujo: "actividadestudios.ca_posibles_que.gestionar.flujo"
preguntas: ["Como ver el cuadro de posibles ca en Ver el cuadro de posibles ca?"]
pantallas_principales: ["actividadestudios.pantalla.ca_posibles_que"]
fragmentos: ["actividadestudios.pantalla.ca_posibles_que"]
endpoints: ["/src/actividadestudios/ca_posibles_que_data"]
source: "docs/catalogo/actividadestudios/flujos/ca_posibles_que.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el cuadro de posibles ca

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el cuadro de posibles ca`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el cuadro de posibles ca en Ver el cuadro de posibles ca?

## Donde Entrar

- Ca Posibles Que (`actividadestudios.pantalla.ca_posibles_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el cuadro de posibles ca

1. Entrar en «Ver cuadro de posibles ca». Nota de la pantalla: para que salgan los ca en el cuadro deben tener introducidas las asignaturas y el campo de nivel de stgr.
2. Marcar qué ca se quieren: **ca de estudios**, **ca de repaso** o **todos los ca**.
3. Elegir los centros de los numerarios y de los agregados (o «todos los ctr»), el periodo (verano, curso, todo el año u otro) y el año.
4. Escoger el grupo de estudios y, si hace falta, un texto de referencia y el texto de cabecera: **horizontal (excel)** o **vertical (imprimir)**.
5. Pulsar **ver cuadro**.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/ca_posibles_que_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.ca_posibles_que`
- `actividadestudios.pantalla.ca_posibles_que`

## Objetivo

Preparar el cuadro de posibles ca: qué personas podrían ir a cada ca según las asignaturas que les faltan.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
