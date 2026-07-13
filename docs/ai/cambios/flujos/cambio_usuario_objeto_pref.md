---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Guardar objeto de aviso"
flujo: "cambios.cambio_usuario_objeto_pref.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_guardar"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar objeto de aviso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar objeto de aviso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref`

## Objetivo

Persistir la parte «objeto + tipo de actividad + fase + flags de aviso» de una preferencia.

## Errores Documentados

- `falta id_usuario, id_tipo_activ invalido, Hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
