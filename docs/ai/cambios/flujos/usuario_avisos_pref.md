---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Configurar un aviso de cambios en actividades"
flujo: "cambios.usuario_avisos_pref.gestionar.flujo"
preguntas: ["Como configurar un aviso de cambios en actividades en Configurar un aviso de cambios en actividades?"]
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref", "cambios.pantalla.usuario_avisos_pref_fases", "cambios.pantalla.usuario_avisos_pref_propiedades", "cambios.pantalla.usuario_avisos_pref_condicion"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data", "/src/cambios/cambio_usuario_objeto_pref_guardar", "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas", "/src/cambios/cambio_usuario_propiedad_pref_preview"]
source: "docs/catalogo/cambios/flujos/usuario_avisos_pref.md"
estado_revision: "generado"
---

# Ayuda IA - Configurar un aviso de cambios en actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Configurar un aviso de cambios en actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como configurar un aviso de cambios en actividades en Configurar un aviso de cambios en actividades?

## Donde Entrar

- Configurar aviso (`cambios.pantalla.usuario_avisos_pref`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Configurar un aviso de cambios en actividades

1. En «Avisos para …», elegir **cambios en:** (el objeto a vigilar).
2. Elegir si es para actividades que organiza **la propia dl** u **otras dl**, y el tipo (**sf/sv**, **asistentes**, **actividad**, **tipo de actividad**).
3. En **fase de referencia** elegir la fase (salen todas las posibles, aunque no tengas permiso; el permiso se comprueba al generar el aviso).
4. Marcar cuándo avisar: **avisar antes de que esté marcada (off)**, **avisar si está marcada (on)** (viene marcada) y **avisar aunque la actividad haya finalizado**.
5. En «opciones» elegir **ver cambios sólo de las casas** (opcional) y **avisar por:** lista o email.
6. En «campos o propiedades» marcar los campos a vigilar (enlaces **[todos]**, **[ninguno]**, **[invertir]**) y, si hace falta, una **condición**.
7. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/cambios/usuario_avisos_pref_form_data`
- `/src/cambios/cambio_usuario_objeto_pref_guardar`
- `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`
- `/src/cambios/cambio_usuario_propiedad_pref_preview`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref`
- `cambios.pantalla.usuario_avisos_pref`
- `cambios.pantalla.usuario_avisos_pref_fases`
- `cambios.pantalla.usuario_avisos_pref_propiedades`
- `cambios.pantalla.usuario_avisos_pref_condicion`

## Objetivo

Configurar un aviso de cambios: qué se vigila, en qué actividades y cómo se avisa.

## Errores Documentados

- `falta id_usuario, usuario/grupo no encontrado, preferencia no encontrada`
- `id_tipo_activ invalido, Hay un error, no se ha guardado`
- `faltan parametros (propiedades)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
