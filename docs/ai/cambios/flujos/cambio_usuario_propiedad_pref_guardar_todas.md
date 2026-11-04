---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Guardar los avisos"
flujo: "cambios.cambio_usuario_propiedad_pref_guardar_todas.gestionar.flujo"
preguntas: ["Como guardar los avisos en Guardar los avisos?"]
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_guardar_todas"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_guardar_todas.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar los avisos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar los avisos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar los avisos en Guardar los avisos?

## Donde Entrar

- Configurar aviso (`cambios.pantalla.usuario_avisos_pref`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar los avisos

1. Marcar y condicionar los campos.
2. Pulsar grabar todo: se guardan el aviso y todos sus campos vigilados.

Referencias tecnicas para verificar la respuesta:
- `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref`
- `cambios.pantalla.usuario_avisos_pref`

## Objetivo

Crear, actualizar o quitar los campos vigilados según las casillas y condiciones marcadas.

## Errores Documentados

- `faltan parametros, Hay un error, no se ha guardado, Hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
