---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Desunir vínculo BDU"
flujo: "dbextern.sincro_desunir.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
endpoints: ["/src/dbextern/sincro_desunir"]
source: "docs/catalogo/dbextern/flujos/sincro_desunir.md"
estado_revision: "generado"
---

# Ayuda IA - Desunir vínculo BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Desunir vínculo BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_desaparecidos_de_orbix`

## Objetivo

Romper el vínculo incorrecto para poder re-unir o crear la ficha después.

## Errores Documentados

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
