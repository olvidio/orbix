---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Consultar las actividades de sg"
flujo: "actividades.lista_actividades_sg.gestionar.flujo"
preguntas: ["Como consultar las actividades de sg en Consultar las actividades de sg?"]
pantallas_principales: ["actividades.pantalla.lista_actividades_sg"]
fragmentos: ["actividades.pantalla.lista_actividades_sg"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
source: "docs/catalogo/actividades/flujos/lista_actividades_sg.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar las actividades de sg

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar las actividades de sg`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar las actividades de sg en Consultar las actividades de sg?

## Donde Entrar

- Listado actividades San Gabriel (crt/cv) (`actividades.pantalla.lista_actividades_sg`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar las actividades de sg

1. Entrar en **vsg > crt** (o **cv**) **> de la r/dl**.
2. Elegir el periodo y otros filtros y buscar.
3. Pulsar una actividad de la tabla para abrir su ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/lista_actividades_sg_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.lista_actividades_sg`
- `actividades.pantalla.lista_actividades_sg`

## Objetivo

Consultar las actividades de San Gabriel (crt o cv) de la r/dl o de un centro, filtrar y abrir las fichas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
