---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Texto Comunicacion"
flujo: "actividadessacd.texto_comunicacion.gestionar.flujo"
preguntas: ["Como guardar en Texto Comunicacion?", "Como obtener datos en Texto Comunicacion?"]
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_txt"]
endpoints: ["/src/actividadessacd/texto_comunicacion_data", "/src/actividadessacd/texto_comunicacion_guardar"]
source: "docs/catalogo/actividadessacd/flujos/texto_comunicacion.md"
estado_revision: "generado"
---

# Ayuda IA - Texto Comunicacion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Texto Comunicacion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Texto Comunicacion?
- Como obtener datos en Texto Comunicacion?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.com_sacd_txt`

## Objetivo

Gestiona TextoComunicacion. Devuelve el texto de comunicacion (clave, idioma). Upsert/delete del texto de comunicacion (clave, idioma, texto).

## Errores Documentados

- `faltan parametros clave / idioma`
- `hay un error, no se ha eliminado el texto`
- `hay un error, no se ha guardado el texto`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
