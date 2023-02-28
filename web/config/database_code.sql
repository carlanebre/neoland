DROP SCHEMA IF EXISTS HITO CASCADE;
CREATE SCHEMA HITO

CREATE TABLE HITO.USERS
(
	ID_USER SERIAL NOT NULL PRIMARY KEY,
	USERNAME VARCHAR(20),
	EMAIL VARCHAR(150),
	USER_PASS VARCHAR(100),
	DETAIL VARCHAR(150),
	USER_IMAGE BYTEA
);

CREATE TABLE HITO.POSTS
(
    ID_POST SERIAL NOT NULL PRIMARY KEY,
	TITULO VARCHAR(100),
	USERNAME VARCHAR(20),
	CUERPO TEXT,
	IMAGEN BYTEA DEFAULT 'default_pfp.png',
	FECHA TIMESTAMP DEFAULT now(),
	ID_USER SERIAL,
	FOREIGN KEY (ID_USER) REFERENCES HITO.USERS(ID_USER)
);

ALTER TABLE hito.users ADD CONSTRAINT unique_email UNIQUE (email);

-- Procedimiento Almacenado: Insertar Post

CREATE OR REPLACE PROCEDURE hito.sp_add_post(
  IN titulo VARCHAR,
  IN username VARCHAR,
  IN cuerpo TEXT,
  IN imagen BYTEA,
  IN id_user INTEGER
)
BEGIN ATOMIC
	INSERT INTO HITO.POSTS (TITULO, USERNAME, CUERPO, IMAGEN, id_user)
  	VALUES (titulo, username, cuerpo, imagen, id_user);
END;

-- Procedimiento Almacenado: Borrar post

CREATE OR REPLACE PROCEDURE hito.sp_delete_post(_id integer)
BEGIN ATOMIC
	DELETE FROM HITO.POSTS WHERE ID_POST=_id;
END;

-- Procedimiento Almacenado: Insertar Usuario

CREATE OR REPLACE PROCEDURE hito.sp_add_user(
	IN username VARCHAR,
	IN email VARCHAR,
	IN _password VARCHAR
)
BEGIN ATOMIC
	INSERT INTO HITO.users (username, email, user_pass)
	VALUES (username, email, md5(_password));
END;

-- Procedimiento Almacenado: Actualizar Detalles Usuario

CREATE OR REPLACE PROCEDURE hito.sp_update_user(
    IN _detail VARCHAR(150),
    IN pfp BYTEA,
    IN id_user INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF pfp IS NOT NULL THEN
		UPDATE HITO.USERS SET DETAIL = _detail, user_image = pfp WHERE HITO.USERS.ID_USER = $3;
    ELSE
        UPDATE HITO.USERS SET DETAIL = _detail WHERE HITO.USERS.ID_USER = $3;
    END IF;
END;
$$;

-- Procedimiento Almacenado: Eliminar foto de usuario

CREATE OR REPLACE PROCEDURE hito.sp_delete_pfp(IN _id integer)
BEGIN ATOMIC
	UPDATE HITO.USERS SET USER_IMAGE = NULL WHERE ID_USER = _id;
END;


