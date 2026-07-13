---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "tipos de actividad"
flujo: "actividades.tipo_activ.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
source: "docs/catalogo/actividades/flujos/tipo_activ.md"
estado_revision: "generado"
---

# Ayuda IA - tipos de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `tipos de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Gestión de tipos de actividad (`actividades.pantalla.tipo_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.tipo_activ`

## Objetivo

Listar tipos, crear uno nuevo, renombrar o eliminar desde la pantalla de administración.

## Errores Documentados

- `tipo de actividad no encontrado`
- `Id incorrecto (alta)`
- `hay un error, no se ha guardado / hay un error, no se ha eliminado`
- `Aviso: IMPORTANTE: Debe añadir un proceso… (con procesos instalado)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
