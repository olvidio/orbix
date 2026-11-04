---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Elegir la fase de referencia"
flujo: "procesos.usuario_perm_activ_ajax.gestionar.flujo"
preguntas: ["Como elegir la fase de referencia en Elegir la fase de referencia?"]
pantallas_principales: ["procesos.pantalla.usuario_perm_activ"]
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
endpoints: ["/src/procesos/usuario_perm_activ_ajax"]
source: "docs/catalogo/procesos/flujos/usuario_perm_activ_ajax.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la fase de referencia

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la fase de referencia`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la fase de referencia en Elegir la fase de referencia?

## Donde Entrar

- Usuario Perm Activ (`procesos.pantalla.usuario_perm_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la fase de referencia

1. En el permiso, cambiar el tipo de actividad o la delegación.
2. El desplegable de fase de referencia se actualiza con las fases válidas.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/usuario_perm_activ_ajax`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.usuario_perm_activ`
- `procesos.pantalla.usuario_perm_activ`

## Objetivo

Actualizar las fases disponibles al cambiar el tipo de actividad o la delegación en el permiso de un usuario.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
