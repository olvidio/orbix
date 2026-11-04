---
id: "usuarios.preferencias.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Cambiar las preferencias personales"
capacidad: "usuarios.preferencias.gestionar"
pantallas_principales: ["usuarios.pantalla.preferencias"]
fragmentos: []
acciones: ["guardar"]
endpoints: ["/src/usuarios/preferencias_guardar"]
estado_revision: "revisado"
---

# Flujo - Preferencias

## Objetivo De Usuario

Cambiar las preferencias personales: diseño del menú, página de inicio, estilo, presentación de tablas, nombres e idioma.

## Punto De Entrada

Menú de usuario > **preferencias**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Cambiar las preferencias personales

Pasos propuestos:
1. Abrir **preferencias** en el menú de usuario.
2. En **Preferencias personales** elegir: Layout (Legacy, Burger, Pills, Pills 2), Página central de inicio (Home, Oficina, Avisos cambios actividades), Menú principal de la oficina, Estilo, Disposición menú, Presentación tablas (SlickGrid o Html), Presentación nombre/apellidos, Idioma y Zona horaria.
3. Pulsar guardar. Algunos cambios (layout, idioma) exigen volver a entrar: la pantalla avisa «deberá volver a entrar».

Endpoints asociados:
- `/src/usuarios/preferencias_guardar`

### Gestionar los avisos de cambios en actividades

Pasos propuestos:
1. En la parte superior de preferencias, usar **ver lista**, **ver mails sin enviar** o **gestionar avisos en cambios actividades**.

Endpoints asociados:
- `/src/usuarios/preferencias_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/preferencias_guardar`

## Errores Conocidos

- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias
