---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Ver los grupos disponibles"
flujo: "usuarios.role_grupmenu_info.gestionar.flujo"
preguntas: ["Como ver los grupos disponibles en Ver los grupos disponibles?"]
pantallas_principales: ["usuarios.pantalla.role_grupmenu"]
fragmentos: ["usuarios.pantalla.role_grupmenu"]
endpoints: ["/src/usuarios/role_grupmenu_info"]
source: "docs/catalogo/usuarios/flujos/role_grupmenu_info.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los grupos disponibles

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los grupos disponibles`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los grupos disponibles en Ver los grupos disponibles?

## Donde Entrar

- Role Grupmenu (`usuarios.pantalla.role_grupmenu`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los grupos disponibles

1. En la ficha del rol, pulsar **añadir grup menu**.
2. Aparece la lista de grupos de menús que el rol aún no tiene.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_grupmenu_info`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_grupmenu`
- `usuarios.pantalla.role_grupmenu`

## Objetivo

Mostrar los grupos de menús que se pueden añadir a un rol.

## Errores Documentados

- `Rol no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
