---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Sacd Ausencias Jefe Zona"
flujo: "encargossacd.sacd_ausencias_jefe_zona.gestionar.flujo"
preguntas: ["Como obtener datos en Sacd Ausencias Jefe Zona?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ausencias_jefe_zona"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
source: "docs/catalogo/encargossacd/flujos/sacd_ausencias_jefe_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Ausencias Jefe Zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Ausencias Jefe Zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Sacd Ausencias Jefe Zona?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ausencias_jefe_zona`

## Objetivo

Gestiona SacdAusenciasJefeZona. Datos para el listado de SACDs susceptibles de gestionar ausencias desde la ficha de jefe de zona (frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php). Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde (Oficial_dl o jefe de calendario), la totalidad de SACDs activos. El array se devuelve ordenado por iniciales para alimentar el desplegable.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
