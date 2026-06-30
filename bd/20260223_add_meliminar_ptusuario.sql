-- Agrega privilegio de eliminación/deshabilitación para control de usuario
ALTER TABLE usuario_ptusuario
    ADD COLUMN MELIMINAR TINYINT(4) DEFAULT 0 AFTER MVER;

-- Normaliza registros históricos: si tenía permiso de editar, hereda eliminar inicialmente
UPDATE usuario_ptusuario
SET MELIMINAR = 1
WHERE MEDITAR = 1;
