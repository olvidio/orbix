---
id: "usuarios.borrar_pwd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Borrar las contraseñas en pruebas"
capacidad: "usuarios.borrar_pwd.gestionar"
pantallas_principales: ["usuarios.pantalla.borrar_todos_pwd"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/borrar_pwd"]
estado_revision: "revisado"
---

# Flujo - Borrar Pwd

## Objetivo De Usuario

Herramienta de pruebas: borra las contraseñas de todos los usuarios (salvo el superadministrador) para poder entrar sin ellas.

## Punto De Entrada

Menú **sistema > usuarios web > borrar todos los passwords** (solo en la base de pruebas).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Borrar las contraseñas en pruebas

Pasos propuestos:
1. Entrar en la opción del menú **sistema**.
2. Confirmar la acción: afecta a todos los esquemas.
3. Solo funciona en la base de pruebas; en producción aparece «Sólo se puede borrar en la base de datos de pruebas».

Endpoints asociados:
- `/src/usuarios/borrar_pwd`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/borrar_pwd`

## Errores Conocidos

- `No se pudieron obtener esquemas`
- `Sólo se puede borrar en la base de datos de pruebas`
- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
