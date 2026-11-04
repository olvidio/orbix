---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Elegir qué campos vigilar"
flujo: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar.flujo"
preguntas: ["Como elegir qué campos vigilar en Elegir qué campos vigilar?"]
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref_propiedades"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref_propiedades"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref_propiedades.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir qué campos vigilar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir qué campos vigilar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir qué campos vigilar en Elegir qué campos vigilar?

## Donde Entrar

- Tabla de propiedades (`cambios.pantalla.usuario_avisos_pref_propiedades`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir qué campos vigilar

1. Elegir el objeto.
2. Aparece la tabla de campos; marcar los que se quieren vigilar.

Referencias tecnicas para verificar la respuesta:
- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref_propiedades`
- `cambios.pantalla.usuario_avisos_pref_propiedades`

## Objetivo

Mostrar los campos del objeto que se pueden vigilar, con el estado guardado.

## Errores Documentados

- `Usuario no encontrado, Usuario sin rol asignado, objeto %s no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
