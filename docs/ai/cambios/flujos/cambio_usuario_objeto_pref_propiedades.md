---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Cargar propiedades vigilables"
flujo: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref_propiedades"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref_propiedades.md"
estado_revision: "generado"
---

# Ayuda IA - Cargar propiedades vigilables

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cargar propiedades vigilables`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref_propiedades`

## Objetivo

Mostrar la tabla de campos del objeto que pueden vigilarse, con el estado guardado preseleccionado.

## Errores Documentados

- `Usuario no encontrado, Usuario sin rol asignado, objeto %s no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
