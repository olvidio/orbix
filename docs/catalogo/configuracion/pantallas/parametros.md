---
id: "configuracion.pantalla.parametros"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "configuracion"
nombre: "Configuración del esquema"
controller: "frontend/configuracion/controller/parametros.php"
vistas: ["frontend/configuracion/view/parametros.html.twig"]
fragmentos_frontend: []
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
capacidades: ["configuracion.parametros.gestionar"]
campos: ["form.ini_dia", "form.ini_mes", "form.fin_dia", "form.fin_mes", "form.valor", "form.parametro"]
acciones: ["fnjs_guardar"]
estado_revision: "revisado"
---

# Configuración del esquema

Pantalla de parámetros globales del esquema Orbix: periodos de curso STGR/CRT, jefe de
calendario, datos de estudios/certificados, notas, idioma, ámbito (dl/región/rstgr) y
gestión de calendario. Cada bloque es un formulario independiente con HashFront.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/configuracion/controller/parametros.php`

## Vistas Relacionadas

- `frontend/configuracion/view/parametros.html.twig`

## Endpoints Usados

- `/src/configuracion/parametros_lista` — carga valores actuales (builder `form_data`)
- `/src/configuracion/parametros_update` — guarda un parámetro (`parametro` + `valor` o fechas)

## Parámetros editables

| Código | Contenido |
|--------|-----------|
| `curso_stgr` / `curso_crt` | Periodo curso (día/mes inicio y fin) |
| `jefe_calendario` | Logins del jefe de calendario (coma) |
| `ce_lugar` | Nombre(s) centro de estudios |
| `region_latin` | Nombre región en latín (HTML) |
| `vstgr`, `lugar_firma`, `dir_stgr` | Datos certificados STGR |
| `nota_corte`, `nota_max`, `caduca_cursada` | Notas y caducidad asignaturas |
| `ini_contador_certificados` | Contador inicial certificados |
| `idioma_default` | Desplegable de locales |
| `ambito` | `dl` / `r` / `rstgr` |
| `gesCalendario` | `central` / `oficinas` |

## Manual De Usuario

1. Abrir desde menú Configuración > config esquema (o ADMIN LOCAL > Esquema en Pills2).
2. Localizar el bloque del parámetro a cambiar.
3. Editar y pulsar «Guardar» del formulario correspondiente (cada bloque guarda por separado).
4. Confirmar el aviso «se ha guardado correctamente».

## Ruta de menú

- **Legacy:** sistema > Configuración > config esquema
- **Pills2:** ADMIN LOCAL > Esquema; sistema > Configuración > config esquema
