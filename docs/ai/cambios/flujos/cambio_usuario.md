---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Eliminar cambio anotado"
flujo: "cambios.cambio_usuario.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
endpoints: ["/src/cambios/cambio_usuario_eliminar"]
source: "docs/catalogo/cambios/flujos/cambio_usuario.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar cambio anotado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar cambio anotado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.avisos_generar`

## Objetivo

Quitar de la cola de avisos un `CambioUsuario` concreto seleccionado en la lista de cambios.

## Errores Documentados

- `Hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
