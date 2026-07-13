---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Texto Comunicacion"
flujo: "actividadessacd.texto_comunicacion.gestionar.flujo"
preguntas: ["Como obtener datos en Texto Comunicacion?", "Como guardar en Texto Comunicacion?"]
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
- Como obtener datos en Texto Comunicacion?
- Como guardar en Texto Comunicacion?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Elegir **clave** e **idioma** en los desplegables.
2. El sistema recarga el textarea con el texto guardado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/texto_comunicacion_data`

## Guardar

1. Editar el texto en el textarea.
2. Pulsar **guardar** (o **cancelar** para volver sin guardar).
3. El sistema hace upsert o elimina si el texto queda vacío.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/texto_comunicacion_guardar`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.com_sacd_txt`

## Objetivo

El usuario edita los textos de la carta de comunicación: elige clave (comunicación general o títulos de columna) e idioma, carga el texto guardado, lo modifica y guarda. Guardar con el textarea vacío elimina el texto de ese `{clave, idioma}`.

## Errores Documentados

- `faltan parametros clave / idioma`
- `hay un error, no se ha eliminado el texto`
- `hay un error, no se ha guardado el texto`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
