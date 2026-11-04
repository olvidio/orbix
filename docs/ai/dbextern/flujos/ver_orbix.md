---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Revisar las personas de Aquinate sin BDU"
flujo: "dbextern.ver_orbix.gestionar.flujo"
preguntas: ["Como revisar las personas de aquinate sin bdu en Revisar las personas de Aquinate sin BDU?"]
pantallas_principales: ["dbextern.pantalla.ver_orbix"]
fragmentos: ["dbextern.pantalla.ver_orbix"]
endpoints: ["/src/dbextern/ver_orbix_datos"]
source: "docs/catalogo/dbextern/flujos/ver_orbix.md"
estado_revision: "generado"
---

# Ayuda IA - Revisar las personas de Aquinate sin BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Revisar las personas de Aquinate sin BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como revisar las personas de aquinate sin bdu en Revisar las personas de Aquinate sin BDU?

## Donde Entrar

- Personas Aquinate sin BDU (`dbextern.pantalla.ver_orbix`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Revisar las personas de aquinate sin bdu

1. Pulsar **ver** en el punto 9.
2. Aparece la primera persona con sus posibles candidatos de la BDU.
3. Pulsar **unir** si hay coincidencia.
4. Usar anterior y siguiente para recorrer la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/ver_orbix_datos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_orbix`
- `dbextern.pantalla.ver_orbix`

## Objetivo

Revisar las personas activas de Aquinate sin correspondencia en la BDU y unirlas si hay candidato.

## Errores Documentados

- `No existe la clase de la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
