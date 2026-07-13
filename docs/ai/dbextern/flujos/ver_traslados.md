---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Revisar traslados punto 2"
flujo: "dbextern.ver_traslados.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_traslados"]
endpoints: ["/src/dbextern/ver_traslados_datos"]
source: "docs/catalogo/dbextern/flujos/ver_traslados.md"
estado_revision: "generado"
---

# Ayuda IA - Revisar traslados punto 2

Usa este documento para responder preguntas de usuario sobre como trabajar con `Revisar traslados punto 2`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_traslados`

## Objetivo

Identificar quién debe trasladarse a esta DL desde otra delegación.

## Errores Documentados

- `No existe la clase de la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
