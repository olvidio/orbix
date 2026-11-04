---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Consultar los roles"
flujo: "usuarios.role.gestionar.flujo"
preguntas: ["Como consultar los roles en Consultar los roles?", "Como asignar grupos de menús a un rol en Consultar los roles?", "Como quitar un grupo de menús a un rol en Consultar los roles?"]
pantallas_principales: ["usuarios.pantalla.role_lista"]
fragmentos: ["usuarios.pantalla.role_form", "usuarios.pantalla.role_lista"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_guardar", "/src/usuarios/role_lista"]
source: "docs/catalogo/usuarios/flujos/role.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los roles

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los roles`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los roles en Consultar los roles?
- Como asignar grupos de menús a un rol en Consultar los roles?
- Como quitar un grupo de menús a un rol en Consultar los roles?

## Donde Entrar

- Role Lista (`usuarios.pantalla.role_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los roles

1. Entrar en **lista de roles**.
2. Los roles son comunes a todas las delegaciones y solo los modifica el superusuario.
3. Pulsar un rol para ver su ficha y los grupos de menús que tiene.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_lista`

## Asignar grupos de menús a un rol

1. Abrir la ficha del rol.
2. Pulsar **añadir grup menu** y marcar los grupos de menús que se quieren dar.
3. Guardar. Los grupos de menús son propios de cada delegación (sv y sf distintos) y los puede modificar el administrador.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_lista`

## Quitar un grupo de menús a un rol

1. En la ficha del rol, en la tabla de grupos de menús, elegir el que se quiere quitar y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_lista`
- `usuarios.pantalla.role_form`
- `usuarios.pantalla.role_lista`

## Objetivo

Consultar los roles y asignar a cada uno los grupos de menús de la delegación.

## Errores Documentados

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
