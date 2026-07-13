---
id: "usuarios.borrar_pwd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Borrar Pwd"
capacidad: "usuarios.borrar_pwd.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/borrar_pwd"]
estado_revision: "revisado"
---

# Flujo - Borrar Pwd

## Objetivo De Usuario

Herramienta de pruebas: resetea contraseñas al login en todos los esquemas (excepto superadmin id_role=1). Solo WEBDIR=pruebas o Docker.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
