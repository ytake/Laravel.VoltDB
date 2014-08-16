CREATE TABLE users (
   user_id INTEGER UNIQUE NOT NULL,
   username VARCHAR(40) NOT NULL,
   password VARCHAR(64) NOT NULL,
   remember_token VARCHAR(128) DEFAULT NULL,
   created_at TIMESTAMP NOT NULL,
   PRIMARY KEY(user_id)
);
CREATE INDEX UsersIndex ON users (username, password, remember_token);
PARTITION TABLE users ON COLUMN user_id;

CREATE PROCEDURE Auth_findUser AS
  SELECT * FROM users WHERE user_id = ?;
PARTITION PROCEDURE Auth_findUser ON TABLE users COLUMN user_id;

CREATE PROCEDURE Auth_rememberToken AS
  SELECT * FROM users WHERE user_id = ? AND remember_token = ?;
PARTITION PROCEDURE Auth_rememberToken ON TABLE users COLUMN user_id;

CREATE PROCEDURE Auth_updateToken AS
  UPDATE users SET remember_token = ? WHERE user_id = ?;
PARTITION PROCEDURE Auth_updateToken ON TABLE users COLUMN user_id PARAMETER 1;

CREATE TABLE cache (
  key VARCHAR(255) UNIQUE NOT NULL,
  value VARCHAR(262144),
  expiration INTEGER DEFAULT 0 NOT NULL,
  CONSTRAINT PK_cache PRIMARY KEY (key)
);
CREATE INDEX IX_cache_expires ON cache (expiration);
PARTITION TABLE cache ON COLUMN key;

CREATE PROCEDURE Cache_flushAll AS DELETE FROM cache;

CREATE PROCEDURE Cache_forget AS
  DELETE FROM cache WHERE key = ?;
PARTITION PROCEDURE Cache_forget ON TABLE cache COLUMN key;

CREATE PROCEDURE Cache_find AS
  SELECT * FROM cache WHERE key = ?;
PARTITION PROCEDURE Cache_find ON TABLE cache COLUMN key;

CREATE PROCEDURE Cache_add AS INSERT INTO cache (key, value, expiration) VALUES (?, ?, ?);

CREATE PROCEDURE Cache_update AS
  UPDATE cache SET value = ?, expiration = ? WHERE key = ?;
PARTITION PROCEDURE Cache_update ON TABLE cache COLUMN key PARAMETER 2;