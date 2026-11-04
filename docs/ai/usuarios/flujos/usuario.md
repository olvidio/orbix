---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Buscar un usuario"
flujo: "usuarios.usuario.gestionar.flujo"
preguntas: ["Como buscar un usuario en Buscar un usuario?", "Como crear un usuario en Buscar un usuario?", "Como modificar un usuario en Buscar un usuario?", "Como asignar grupos de permisos en Buscar un usuario?", "Como eliminar un usuario en Buscar un usuario?"]
pantallas_principales: ["usuarios.pantalla.usuario_lista"]
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_lista"]
endpoints: ["/src/usuarios/usuario_eliminar", "/src/usuarios/usuario_form", "/src/usuarios/usuario_guardar", "/src/usuarios/usuario_lista"]
source: "docs/catalogo/usuarios/flujos/usuario.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar un usuario

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar un usuario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar un usuario en Buscar un usuario?
- Como crear un usuario en Buscar un usuario?
- Como modificar un usuario en Buscar un usuario?
- Como asignar grupos de permisos en Buscar un usuario?
- Como eliminar un usuario en Buscar un usuario?

## Donde Entrar

- Usuario Lista (`usuarios.pantalla.usuario_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar un usuario

1. Entrar en **sistema > usuarios web > lista usuarios**.
2. Escribir parte del nombre en **Nombre** y pulsar **Buscar**, o usar **Buscar en todas las columnas** sobre la tabla.
3. Pulsar el login del usuario para abrir su ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Crear un usuario

1. En la lista, pulsar **NUEVO USUARIO**.
2. Rellenar **Login** y elegir el **Role** (admin, casa, centro sv, director, oficial_dl, p-sacd, supervisor).
3. Escribir un **Password** (debe cumplir los requisitos de seguridad; si se deja vacío en un usuario existente no se cambia).
4. Marcar **Cambio password** si se quiere obligar a cambiarlo al primer acceso, y **¿tiene doble factor activado?** si procede.
5. Escribir el **Email** y pulsar **guardar datos usuario**.
6. Al guardar aparece la ficha completa con grupos y permisos.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Modificar un usuario

1. Abrir la ficha desde la lista.
2. Cambiar login, nombre a mostrar, role, email o las casillas, y pulsar **guardar datos usuario**.
3. Para cambiar la contraseña, escribir la nueva en **Password** (se comprueba su fortaleza antes de guardar).

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Asignar grupos de permisos

1. En la ficha, apartado **grupos**, pulsar **añadir un grupo de permisos** y, en la tabla, **añadir** en la fila del grupo (se asigna al instante).
2. Para retirarlo, pulsar **quitar de un grupo de permisos** y **quitar** en la fila del grupo.
3. Aviso de la pantalla: si el usuario tiene varios grupos, sus permisos deberían ser independientes porque no hay preferencia entre ellos.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Eliminar un usuario

1. En la lista, marcar el usuario y pulsar el botón de eliminar.
2. Confirmar el mensaje «¿Está seguro que desea borrar este usuario?».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_lista`
- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_lista`

## Objetivo

Dar de alta, consultar, modificar y eliminar usuarios web de la delegación, y asignarles grupos de permisos y permisos propios.

## Errores Documentados

- `Usuario no encontrado`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
