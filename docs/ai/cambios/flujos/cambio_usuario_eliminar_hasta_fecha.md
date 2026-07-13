---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Purgar cambios hasta fecha"
flujo: "cambios.cambio_usuario_eliminar_hasta_fecha.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
endpoints: ["/src/cambios/cambio_usuario_eliminar_hasta_fecha"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_eliminar_hasta_fecha.md"
estado_revision: "generado"
---

# Ayuda IA - Purgar cambios hasta fecha

Usa este documento para responder preguntas de usuario sobre como trabajar con `Purgar cambios hasta fecha`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.avisos_generar`

## Objetivo

Eliminar en bloque todos los cambios anotados con fecha anterior o igual a la indicada.

## Errores Documentados

- `debe indicar la fecha`
- `Hay un error al eliminar los cambios hasta la fecha indicada`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
