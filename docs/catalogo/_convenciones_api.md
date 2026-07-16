---
tipo: "convenciones"
alcance: "api_orbix"
estado_revision: "revisado"
---

# Convenciones API Orbix

Documento transversal para endpoints bajo `/src/<modulo>/...`. Las fichas concretas viven en `docs/catalogo/<modulo>/api/`.

**Clientes nativos (Android/iOS):** ver [`_clientes_nativos.md`](_clientes_nativos.md) e índice [`_endpoints_cliente_movil.md`](_endpoints_cliente_movil.md).

## Transporte y sesión

| Aspecto | Convención |
|---------|------------|
| URL base | `/src/<modulo>/<endpoint>` |
| Método HTTP | Las rutas suelen registrar `GET` y `POST`; el frontend llama casi siempre con **POST** |
| Content-Type | `application/x-www-form-urlencoded` |
| Autenticación | Cookie de sesión PHP `PHPSESSID` (usuario ya autenticado en Orbix) |
| CSRF / contexto | Ver sección HashB más abajo |

## Envelope de respuesta

La mayoría de endpoints responden JSON mediante `ContestarJson::enviar`:

```json
{
  "success": true,
  "data": "ok"
}
```

En error de negocio (`success: false`):

```json
{
  "success": false,
  "mensaje": "texto traducido del error",
  "data": "none"
}
```

Notas importantes:

- Por compatibilidad legacy, cuando `data` es un **array u objeto**, `ContestarJson::enviar` lo serializa como **string JSON escapado** dentro del campo `data`. El cliente frontend hace un segundo `JSON.parse` sobre `data` (ver `frontend/shared/PostRequest.php`).
- Mutaciones exitosas suelen devolver `data: "ok"` (string literal).
- Endpoints `*_form_data` y `*_lista_data` devuelven un payload estructurado en `data`.
- `ContestarJson::enviarDataAnidado` deja `data` como objeto/array JSON nativo (una sola codificación). Usar solo cuando el cliente no espera doble parse.

Códigos HTTP: por defecto **200** incluso en error de negocio (`success: false`). Algunos endpoints pueden usar otro código si se pasa `$httpStatusOnError`.

## Tipos de operación (sufijos)

| Sufijo | `operacion` | Rol |
|--------|-------------|-----|
| `*_lista_data` | `lista_data` | Datos tabulares (cabeceras + filas + flags de permiso) |
| `*_form_data` | `form_data` | Datos para pintar un formulario (opciones, tokens HashB, valores actuales) |
| `*_update`, `*_nuevo`, `*_editar` | `mutacion` | Crear o modificar entidades |
| `*_eliminar`, `*_copiar` | `mutacion` | Otras mutaciones |

## Autorización con HashB

En endpoints migrados al patrón `HashB`, el navegador **no envía** identificadores sensibles como campos editables de confianza. En su lugar transporta una **cápsula opaca** firmada por el backend:

| Campo POST | Acción HashB | Uso |
|------------|--------------|-----|
| `ctx_update` | `tarifa_ubi_update` | Autoriza crear/editar `TarifaUbi` |
| `ctx_eliminar` | `tarifa_ubi_eliminar` | Autoriza eliminar `TarifaUbi` |
| `ctx_copiar` | `tarifa_ubi_copiar` | Autoriza copiar tarifas |

Flujo típico:

1. El endpoint `*_form_data` emite `token_update` / `token_eliminar` (cápsulas firmadas).
2. El formulario incluye la cápsula en un hidden (`ctx_update`, etc.).
3. El endpoint de mutación abre la cápsula con `HashB::open($capsule, $expectedAction)` y toma el contexto firmado (`id_item`, `id_ubi`, `year`, …).
4. Campos homónimos en el body POST pueden existir por compatibilidad transitoria pero **se ignoran**; la verdad está en la cápsula.

Si la cápsula es inválida o caducada: `success: false`, `mensaje: "Operación no autorizada"`, `data: "none"`.

Referencia arquitectónica: `docs/dev/hash_arquitectura.md`.

## Entrada desde controllers legacy

Algunos controllers pasan `$_POST` completo al caso de uso (`ActividadCargoEditar::execute($_POST)`). La ficha API lista los campos inferidos del controller **y** del caso de uso en `src/.../application/`. Revisar ambos.

Campos especiales:

- `asis_presente`: debe valer `'1'` cuando el form incluye el checkbox `asis` (sustituye `isset($_POST['asis'])` del legacy).
- `sel`: array legacy con tokens `id_nom#id_item#...` usado en listados/selección.

## Permisos

Los endpoints no implementan un middleware de permisos uniforme. Las comprobaciones suelen estar en:

- El caso de uso (validación de parámetros).
- Los builders `*_lista_data` / `*_form_data` (flags `puede_editar`, `puede_anadir`, etc.).
- Permisos de oficina vía `$_SESSION['oPerm']`.

No inferir permisos concretos desde la ficha API salvo que estén documentados explícitamente.

## OpenAPI generado

`docs/catalogo/<modulo>/openapi.yaml` se genera desde las fichas API. Los campos en `entrada_obligatoria` del front matter se marcan `required: true` en el schema. Los payloads de `respuesta_data` generan schemas nombrados bajo `components/schemas/`.

Regenerar tras cambiar fichas:

```bash
php docs/scripts/generar_openapi_desde_catalogo.php <modulo> --force
docs/scripts/validar_openapi.sh <modulo>
```

## Estados de revisión

| Valor | Significado |
|-------|-------------|
| `generado` | Extraído automáticamente; pendiente revisión humana |
| `revisado` | Validado contra código y/o pruebas manuales |
