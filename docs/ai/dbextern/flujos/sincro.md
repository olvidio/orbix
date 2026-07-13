---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Crear persona desde BDU"
flujo: "dbextern.sincro.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas"]
endpoints: ["/src/dbextern/sincro_crear"]
source: "docs/catalogo/dbextern/flujos/sincro.md"
estado_revision: "generado"
---

# Ayuda IA - Crear persona desde BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear persona desde BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_listas`

## Objetivo

Cuando no hay coincidencia Orbix, crear una ficha nueva y vincularla automáticamente.

## Errores Documentados

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
