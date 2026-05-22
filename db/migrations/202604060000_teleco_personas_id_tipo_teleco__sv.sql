-- Personas (publicv/sv): tabla auxiliar con tipos de teleco de comun y migración d_teleco_personas.
-- Misma convención que teleco_ctr en sv-e (202604050000).
CREATE TABLE publicv.xd_tipo_teleco_tmp (
    tipo_teleco varchar(10),
    nombre_teleco varchar(20),
    ubi bool,
    persona bool,
    id int
);

INSERT INTO publicv.xd_tipo_teleco_tmp (tipo_teleco, nombre_teleco, ubi, persona, id) VALUES
    ('telf', 'teléfono fijo', 't', 't', 1),
    ('móvil', 'teléfono móvil', 't', 't', 2),
    ('e-mail', 'correo electrónico', 't', 't', 3),
    ('fax', 'fax', 't', 't', 4),
    ('web', 'página web', 't', 't', 5);

UPDATE publicv.d_teleco_personas d
SET tipo_teleco = t.id
FROM publicv.xd_tipo_teleco_tmp t
WHERE d.tipo_teleco = t.tipo_teleco;

ALTER TABLE publicv.d_teleco_personas RENAME COLUMN tipo_teleco TO id_tipo_teleco;
ALTER TABLE publicv.d_teleco_personas ALTER COLUMN id_tipo_teleco TYPE int USING id_tipo_teleco::integer;
ALTER TABLE publicv.d_teleco_personas ALTER COLUMN id_tipo_teleco SET NOT NULL;
