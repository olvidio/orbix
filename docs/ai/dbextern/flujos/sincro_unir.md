---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Unir una persona de la BDU con una de Aquinate"
flujo: "dbextern.sincro_unir.gestionar.flujo"
preguntas: ["Como unir una persona de la bdu con una de aquinate en Unir una persona de la BDU con una de Aquinate?"]
pantallas_principales: ["dbextern.pantalla.ver_listas", "dbextern.pantalla.ver_orbix"]
fragmentos: ["dbextern.pantalla.ver_listas", "dbextern.pantalla.ver_orbix"]
endpoints: ["/src/dbextern/sincro_unir"]
source: "docs/catalogo/dbextern/flujos/sincro_unir.md"
estado_revision: "generado"
---

# Ayuda IA - Unir una persona de la BDU con una de Aquinate

Usa este documento para responder preguntas de usuario sobre como trabajar con `Unir una persona de la BDU con una de Aquinate`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como unir una persona de la bdu con una de aquinate en Unir una persona de la BDU con una de Aquinate?

## Donde Entrar

- Personas BDU no unidas (`dbextern.pantalla.ver_listas`)
- Personas Aquinate sin BDU (`dbextern.pantalla.ver_orbix`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Unir una persona de la bdu con una de aquinate

1. En la pantalla de revisión, comprobar que el candidato propuesto es la misma persona.
2. Pulsar **unir**.
3. La persona sale de la cola y pasa a la siguiente.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_unir`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_listas`
- `dbextern.pantalla.ver_orbix`
- `dbextern.pantalla.ver_listas`
- `dbextern.pantalla.ver_orbix`

## Objetivo

Confirmar la correspondencia entre una persona de la BDU y una de Aquinate cuando el sistema propone candidatos (puntos 4 y 9).

## Errores Documentados

- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
