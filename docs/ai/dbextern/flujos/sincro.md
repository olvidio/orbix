---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Sincro"
flujo: "dbextern.sincro.gestionar.flujo"
preguntas: ["Como crear en Sincro?"]
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas"]
endpoints: ["/src/dbextern/sincro_crear"]
source: "docs/catalogo/dbextern/flujos/sincro.md"
estado_revision: "generado"
---

# Ayuda IA - Sincro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sincro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Sincro?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_listas`

## Objetivo

Gestiona CrearPersonaDesdeListasUseCase. Crea una persona en Orbix desde la BDU y la vincula.

## Errores Documentados

- `hay un error, no se ha guardado`
- `no se encontró la persona en la BDU`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
