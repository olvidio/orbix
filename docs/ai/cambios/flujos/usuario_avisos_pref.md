---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Configurar preferencia de aviso"
flujo: "cambios.usuario_avisos_pref.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref", "cambios.pantalla.usuario_avisos_pref_fases", "cambios.pantalla.usuario_avisos_pref_propiedades", "cambios.pantalla.usuario_avisos_pref_condicion"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data", "/src/cambios/cambio_usuario_objeto_pref_guardar", "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas", "/src/cambios/cambio_usuario_propiedad_pref_preview"]
source: "docs/catalogo/cambios/flujos/usuario_avisos_pref.md"
estado_revision: "generado"
---

# Ayuda IA - Configurar preferencia de aviso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Configurar preferencia de aviso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref`
- `cambios.pantalla.usuario_avisos_pref_fases`
- `cambios.pantalla.usuario_avisos_pref_propiedades`
- `cambios.pantalla.usuario_avisos_pref_condicion`

## Objetivo

Definir qué cambios debe recibir un usuario o grupo: objeto, ámbito (tipo/fase/casas) y propiedades con condiciones opcionales.

## Errores Documentados

- `falta id_usuario, usuario/grupo no encontrado, preferencia no encontrada`
- `id_tipo_activ invalido, Hay un error, no se ha guardado`
- `faltan parametros (propiedades)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
