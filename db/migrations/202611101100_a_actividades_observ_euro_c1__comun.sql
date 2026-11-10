-- observ de actividades: U+0080 → «€».
-- El euro de Windows-1252 (byte 0x80) se importó en su día como Latin-1 y quedó
-- guardado como el carácter de control U+0080 (invisible). El VO ActividadObserv
-- lo rechaza: «ActividadObserv has invalid characters».
-- Sobre la tabla padre: corrige todas las hijas (esquemas dl, resto…). Idempotente.

UPDATE public.a_actividades_all
SET observ = replace(observ, chr(128), '€')
WHERE observ LIKE '%' || chr(128) || '%';

SELECT public.migracion_aviso(format(
    'a_actividades_all.observ: quedan %s filas con U+0080',
    (SELECT count(*) FROM public.a_actividades_all WHERE observ LIKE '%' || chr(128) || '%')
));
