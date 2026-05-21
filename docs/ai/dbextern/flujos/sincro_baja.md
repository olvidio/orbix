---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Sincro Baja"
flujo: "dbextern.sincro_baja.gestionar.flujo"
preguntas: ["Como ejecutar en Sincro Baja?"]
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
endpoints: ["/src/dbextern/sincro_baja"]
source: "docs/catalogo/dbextern/flujos/sincro_baja.md"
estado_revision: "generado"
---

# Ayuda IA - Sincro Baja

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sincro Baja`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Sincro Baja?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_desaparecidos_de_listas`

## Objetivo

Gestiona BajaPersonaUseCase. Da de baja a una persona (fallecido o traslado a otra región).

## Errores Documentados

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
