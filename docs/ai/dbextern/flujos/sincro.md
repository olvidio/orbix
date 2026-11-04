---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Crear una persona desde la BDU"
flujo: "dbextern.sincro.gestionar.flujo"
preguntas: ["Como crear una persona desde la bdu en Crear una persona desde la BDU?"]
pantallas_principales: ["dbextern.pantalla.ver_listas"]
fragmentos: ["dbextern.pantalla.ver_listas"]
endpoints: ["/src/dbextern/sincro_crear"]
source: "docs/catalogo/dbextern/flujos/sincro.md"
estado_revision: "generado"
---

# Ayuda IA - Crear una persona desde la BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear una persona desde la BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear una persona desde la bdu en Crear una persona desde la BDU?

## Donde Entrar

- Personas BDU no unidas (`dbextern.pantalla.ver_listas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear una persona desde la bdu

1. En «Revisar BDU no unidas», con la persona delante, pulsar **crear nuevo**.
2. Se crea la ficha y queda vinculada.
3. Pasa automáticamente a la siguiente persona de la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_crear`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_listas`
- `dbextern.pantalla.ver_listas`

## Objetivo

Cuando una persona de la BDU no tiene coincidencia en Aquinate, crear una ficha nueva y vincularla automáticamente.

## Errores Documentados

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
