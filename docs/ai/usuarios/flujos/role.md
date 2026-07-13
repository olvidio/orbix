---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Role"
flujo: "usuarios.role.gestionar.flujo"
preguntas: ["Como eliminar en Role?", "Como guardar en Role?", "Como consultar el listado en Role?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_form", "usuarios.pantalla.role_lista"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_guardar", "/src/usuarios/role_lista"]
source: "docs/catalogo/usuarios/flujos/role.md"
estado_revision: "generado"
---

# Ayuda IA - Role

Usa este documento para responder preguntas de usuario sobre como trabajar con `Role`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Role?
- Como guardar en Role?
- Como consultar el listado en Role?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_form`
- `usuarios.pantalla.role_lista`

## Objetivo

Administración de roles: listar, crear/editar flags sf/sv/pau/dmz y asignar grupmenus.

## Errores Documentados

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
