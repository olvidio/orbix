---
id: "misas.ver_iniciales_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Cambiar iniciales y color de un sacerdote"
capacidad: "misas.ver_iniciales_zona.gestionar"
pantallas_principales: ["misas.pantalla.ver_iniciales_zona"]
fragmentos: ["misas.pantalla.ver_iniciales_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_iniciales_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver iniciales zona

## Objetivo De Usuario

Ver y cambiar las iniciales y el color de cada sacerdote de la zona, que se usan en el plan.

## Punto De Entrada

Menú **dre > Misas > Iniciales sacd** (Pills2: ATENCIÓN SACD > Gestión de misas > Iniciales sacd).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_iniciales_zona`

## Escenarios Inferidos

### Cambiar iniciales y color de un sacerdote

Pasos propuestos:
1. En **Iniciales sacd**, elegir la zona.
2. Editar las **Iniciales** en la fila del sacerdote.
3. Elegir el **Color** en la paleta («Más colores», «Elegir otro color» o «Sin color»).

Endpoints asociados:
- `/src/misas/ver_iniciales_zona_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.color`
- `form.id_sacd`
- `form.iniciales`
- `post.id_zona`

Acciones JavaScript:
- `fnjs_generarNomEnc`

## Endpoints Del Flujo

- `/src/misas/ver_iniciales_zona_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd
