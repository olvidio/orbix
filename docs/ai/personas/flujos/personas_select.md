---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Buscar y listar personas"
flujo: "personas.personas_select.gestionar.flujo"
preguntas: ["Como buscar y listar personas en Buscar y listar personas?"]
pantallas_principales: ["personas.pantalla.personas_que", "personas.pantalla.personas_select"]
fragmentos: []
endpoints: ["/src/personas/personas_select_data"]
source: "docs/catalogo/personas/flujos/personas_select.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar y listar personas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar y listar personas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar y listar personas en Buscar y listar personas?

## Donde Entrar

- Buscar personas (`personas.pantalla.personas_que`)
- Resultado búsqueda personas (`personas.pantalla.personas_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar y listar personas

1. Abrir «Búsqueda de personas» (por ejemplo «numerarios»). Elegir **realizar una búsqueda exacta** (no / sí) y escribir **Nombre**, **Primer apellido**, **Segundo apellido** o **centro**.
2. Pulsar **Buscar**: sale «N personas encontradas» con una tabla.
3. Pulsar el nombre de una persona abre su ficha. Para actuar sobre una o varias, marcarlas y pulsar un botón: **cambio de ctr**, **ver dossiers**, **ficha**, **ver actividades**, **ver tessera**, **modificar stgr**, **imprimir tessera**, **ver notas**, **posibles ca**, **petición ca**, **petición crt**, **plan estudios**, **ficha profesor stgr**, **copiar tessera**, **publicar** o **atención actividades**.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/personas_select_data`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.personas_que`
- `personas.pantalla.personas_select`

## Objetivo

Buscar personas y trabajar con las que salgan: abrir su ficha, ver sus actividades, notas, tessera, trasladarlas, etc.

## Errores Documentados

- `No se encuentra ningún centro con esta condición`
- `Avisos suaves región/persona no válida (listado vacío con mensaje)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
