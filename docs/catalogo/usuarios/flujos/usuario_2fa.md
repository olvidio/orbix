---
id: "usuarios.usuario_2fa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario 2fa"
capacidad: "usuarios.usuario_2fa.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_reset_2fa"]
acciones: ["crear_actualizar"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
estado_revision: "revisado"
---

# Flujo - Usuario 2fa

## Objetivo De Usuario

Configuración autenticación dos factores del usuario.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_reset_2fa`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/usuarios/usuario_2fa_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.enable_2fa`
- `form.secret_2fa`
- `form.verification_code`
- `html.btn_ok`
- `html.enable_2fa`
- `html.id_usuario`
- `html.verification_code`
- `post.id_usuario`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Endpoints Del Flujo

- `/src/usuarios/usuario_2fa_update`

## Errores Conocidos

- `Usuario no encontrado`
- `Se requiere un código de verificación para activar 2FA`
- `Código de verificación inválido`
- `Hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
