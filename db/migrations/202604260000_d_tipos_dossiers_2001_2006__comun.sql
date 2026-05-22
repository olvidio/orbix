-- d_tipos_dossiers: ajustes tipos 2001 y 2006 (comun, datos).
UPDATE public.d_tipos_dossiers SET db = 2 WHERE id_tipo_dossier = 2001;

UPDATE public.d_tipos_dossiers SET class = 'TelecoUbi' WHERE id_tipo_dossier = 2001;

UPDATE public.d_tipos_dossiers
SET tabla_to = 'du_habitaciones_dl',
    app = 'ubiscamas',
    class = 'Habitacion',
    db = 5
WHERE id_tipo_dossier = 2006;
