---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Ver la relación de dossiers"
flujo: "dossiers.dossiers_lista_fichas.gestionar.flujo"
preguntas: ["Como ver la relación de dossiers en Ver la relación de dossiers?"]
pantallas_principales: ["dossiers.pantalla.lista_dossiers"]
fragmentos: ["dossiers.pantalla.lista_dossiers"]
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
source: "docs/catalogo/dossiers/flujos/dossiers_lista_fichas.md"
estado_revision: "generado"
---

# Ayuda IA - Ver la relación de dossiers

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver la relación de dossiers`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver la relación de dossiers en Ver la relación de dossiers?

## Donde Entrar

- Lista Dossiers (`dossiers.pantalla.lista_dossiers`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver la relación de dossiers

1. Abrir la ficha de la persona, actividad o centro/casa y pulsar dossiers.
2. Aparece la **relación de dossiers** con un símbolo por carpeta: **dossier modificable**, **dossier de sólo lectura** o **dossier sin permisos**.
3. Pulsar una carpeta con permiso para abrirla.

Referencias tecnicas para verificar la respuesta:
- `/src/dossiers/dossiers_lista_fichas_data`

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.lista_dossiers`
- `dossiers.pantalla.lista_dossiers`

## Objetivo

Ver las carpetas (dossiers) disponibles de una persona, actividad o centro/casa y qué puede hacer el usuario en cada una.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
