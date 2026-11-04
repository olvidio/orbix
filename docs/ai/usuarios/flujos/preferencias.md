---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Cambiar las preferencias personales"
flujo: "usuarios.preferencias.gestionar.flujo"
preguntas: ["Como cambiar las preferencias personales en Cambiar las preferencias personales?", "Como gestionar los avisos de cambios en actividades en Cambiar las preferencias personales?"]
pantallas_principales: ["usuarios.pantalla.preferencias"]
fragmentos: []
endpoints: ["/src/usuarios/preferencias_guardar"]
source: "docs/catalogo/usuarios/flujos/preferencias.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar las preferencias personales

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar las preferencias personales`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar las preferencias personales en Cambiar las preferencias personales?
- Como gestionar los avisos de cambios en actividades en Cambiar las preferencias personales?

## Donde Entrar

- Preferencias (`usuarios.pantalla.preferencias`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar las preferencias personales

1. Abrir **preferencias** en el menú de usuario.
2. En **Preferencias personales** elegir: Layout (Legacy, Burger, Pills, Pills 2), Página central de inicio (Home, Oficina, Avisos cambios actividades), Menú principal de la oficina, Estilo, Disposición menú, Presentación tablas (SlickGrid o Html), Presentación nombre/apellidos, Idioma y Zona horaria.
3. Pulsar guardar. Algunos cambios (layout, idioma) exigen volver a entrar: la pantalla avisa «deberá volver a entrar».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/preferencias_guardar`

## Gestionar los avisos de cambios en actividades

1. En la parte superior de preferencias, usar **ver lista**, **ver mails sin enviar** o **gestionar avisos en cambios actividades**.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/preferencias_guardar`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.preferencias`

## Objetivo

Cambiar las preferencias personales: diseño del menú, página de inicio, estilo, presentación de tablas, nombres e idioma.

## Errores Documentados

- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
