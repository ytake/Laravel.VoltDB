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

CREATE PROCEDURE Auth_findUser AS SELECT * FROM users WHERE user_id = ?;
PARTITION PROCEDURE Auth_findUser ON TABLE users COLUMN user_id;

CREATE PROCEDURE Auth_rememberToken AS SELECT * FROM users WHERE user_id = ? AND remember_token = ?;
PARTITION PROCEDURE Auth_rememberToken ON TABLE users COLUMN user_id;

CREATE PROCEDURE Auth_updateToken AS UPDATE users SET remember_token = ? WHERE user_id = ?;
PARTITION PROCEDURE Auth_updateToken ON TABLE users COLUMN user_id PARAMETER 1;

CREATE TABLE caches (
  key VARCHAR(255) UNIQUE NOT NULL,
  value VARCHAR(262144),
  expiration INTEGER DEFAULT 0 NOT NULL,
  CONSTRAINT PK_cache PRIMARY KEY (key)
);
CREATE INDEX IX_cache_expires ON caches (expiration);
PARTITION TABLE caches ON COLUMN key;

CREATE PROCEDURE Cache_forget AS DELETE FROM caches WHERE key = ?;
PARTITION PROCEDURE Cache_forget ON TABLE caches COLUMN key;

CREATE PROCEDURE Cache_find AS SELECT * FROM caches WHERE key = ?;
PARTITION PROCEDURE Cache_find ON TABLE caches COLUMN key;

CREATE PROCEDURE Cache_update AS UPDATE caches SET value = ?, expiration = ? WHERE key = ?;
PARTITION PROCEDURE Cache_update ON TABLE caches COLUMN key PARAMETER 2;

CREATE PROCEDURE Cache_flushAll AS DELETE FROM caches;

CREATE PROCEDURE Cache_add AS INSERT INTO caches (key, value, expiration) VALUES (?, ?, ?);

CREATE TABLE sessions (
  id VARCHAR(255) UNIQUE NOT NULL,
  payload VARCHAR(65535),
  last_activity INTEGER DEFAULT 0 NOT NULL
);
CREATE INDEX IX_session_id ON sessions (id);
CREATE INDEX IX_activity ON sessions (last_activity);
PARTITION TABLE sessions ON COLUMN id;

CREATE PROCEDURE Session_find AS SELECT * FROM sessions WHERE id = ?;
PARTITION PROCEDURE Session_find ON TABLE sessions COLUMN id;

CREATE PROCEDURE Session_forget AS DELETE FROM sessions WHERE id = ?;
PARTITION PROCEDURE Session_forget ON TABLE sessions COLUMN id;

CREATE PROCEDURE Session_update AS UPDATE sessions SET payload = ?, last_activity = ? WHERE id = ?;
PARTITION PROCEDURE Session_update ON TABLE sessions COLUMN id PARAMETER 2;

CREATE PROCEDURE Session_delete_activity AS DELETE FROM sessions WHERE last_activity <= ?;

CREATE PROCEDURE Session_add AS INSERT INTO sessions (id, payload, last_activity) VALUES (?, ?, ?);