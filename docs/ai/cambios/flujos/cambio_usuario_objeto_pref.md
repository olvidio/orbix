---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Crear un aviso de cambios"
flujo: "cambios.cambio_usuario_objeto_pref.gestionar.flujo"
preguntas: ["Como crear un aviso de cambios en Crear un aviso de cambios?"]
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_guardar"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref.md"
estado_revision: "generado"
---

# Ayuda IA - Crear un aviso de cambios

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear un aviso de cambios`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear un aviso de cambios en Crear un aviso de cambios?

## Donde Entrar

- Configurar aviso (`cambios.pantalla.usuario_avisos_pref`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear un aviso de cambios

1. Entrar en **gestionar avisos en cambios actividades**.
2. Elegir el objeto a vigilar, el tipo de actividad y la fase.
3. Marcar los campos y condiciones y guardar todo.

Referencias tecnicas para verificar la respuesta:
- `/src/cambios/cambio_usuario_objeto_pref_guardar`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref`
- `cambios.pantalla.usuario_avisos_pref`

## Objetivo

Guardar el objeto, tipo de actividad, fase y avisos de una preferencia de aviso de cambios.

## Errores Documentados

- `falta id_usuario, id_tipo_activ invalido, Hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
