---
id: "usuarios.usuario_guardar_pwd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Cambiar la contraseña"
capacidad: "usuarios.usuario_guardar_pwd.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_form_pwd"]
fragmentos: ["usuarios.pantalla.usuario_form_pwd"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_guardar_pwd"]
estado_revision: "revisado"
---

# Flujo - Usuario Guardar Pwd

## Objetivo De Usuario

Cambiar la contraseña de un usuario cumpliendo los requisitos de seguridad.

## Punto De Entrada

Pantalla de cambio de contraseña (obligatoria si el usuario tiene marcado **Cambio password**) o ficha de usuario.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_pwd`

## Escenarios Inferidos

### Cambiar la contraseña

Pasos propuestos:
1. Escribir la nueva contraseña en **password** y repetirla en **confirma password**.
2. La contraseña debe tener más de 8 caracteres e incluir al menos una mayúscula, un número y un carácter especial (no se admiten comillas dobles).
3. Pulsar **guardar**. Si no coinciden o no es válida aparecen «error: passwords no coincidentes» o «error: password no válido».
4. Al guardar bien se muestra «paswword guardado» y se quita la obligación de cambiarla.

Endpoints asociados:
- `/src/usuarios/usuario_guardar_pwd`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_usuario`
- `form.password`
- `form.password1`
- `html.password`
- `html.password1`

Acciones JavaScript:
- `fnjs_chk_passwd`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Endpoints Del Flujo

- `/src/usuarios/usuario_guardar_pwd`

## Errores Conocidos

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
