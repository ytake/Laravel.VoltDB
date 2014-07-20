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
