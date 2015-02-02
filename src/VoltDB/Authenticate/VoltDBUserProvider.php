<?php
namespace Ytake\LaravelVoltDB\Authenticate;

use Ytake\LaravelVoltDB\ClientConnection;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HashContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticateUserContract;

/**
 * Class VoltDBUserProvider
 * @package Ytake\LaravelVoltDB\Authenticate
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class VoltDBUserProvider implements UserProvider
{

    /** @var string default procedure */
    protected $findUserProcedure = "Auth_findUser";

    /** @var string default procedure */
    protected $rememberTokenProcedure = "Auth_rememberToken";

    /** @var string default procedure */
    protected $updateTokenProcedure = "Auth_updateToken";

    /** @var ClientConnection */
    protected $connection;

    /** @var HashContract  The hasher implementation.  */
    protected $hasher;

    /** @var \Illuminate\Config\Repository  */
    protected $config;

    /**
     * The table containing the users.
     * @var string
     */
    protected $table;


    /**
     * @param ClientConnection $connection
     * @param HashContract $hasher
     * @param ConfigContract $config
     */
    public function __construct(ClientConnection $connection, HashContract $hasher, ConfigContract $config)
    {
        $this->connection = $connection;
        $this->config = $config;
        $this->hasher = $hasher;
        $this->table = $config['auth.table'];
    }

    /**
     * Retrieve a user by their unique identifier.
     * use stored procedure
     *
     * @param  mixed $identifier
     * @return AuthenticateUserContract|null
     */
    public function retrieveById($identifier)
    {
        // stored procedure name
        $findUserProcedure = $this->config->get(
            'ytake-laravel-voltdb.default.auth.procedure.findUser', $this->findUserProcedure);
        $user = $this->connection->procedure($findUserProcedure, [$identifier]);
		if (!is_null($user)) {
            return new VoltDBUser((array) $user, $this->config);
        }
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return AuthenticateUserContract|null
     */
    public function retrieveByToken($identifier, $token)
    {
        //
        $rememberTokenProcedure = $this->config->get(
            'ytake-laravel-voltdb.default.auth.procedure.remember_token', $this->rememberTokenProcedure);
        $user = $this->connection->procedure($rememberTokenProcedure, [$identifier, $token]);

        if(!is_null($user)) {
            return new VoltDBUser((array) $user, $this->config);
        }
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  AuthenticateUserContract $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(AuthenticateUserContract $user, $token)
    {
        //
        $updateTokenProcedure = $this->config->get(
            'ytake-laravel-voltdb.default.auth.procedure.update_token', $this->updateTokenProcedure);
        $this->connection->procedure($updateTokenProcedure, [$token, $user->getAuthIdentifier()]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return AuthenticateUserContract|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        $query = $this->buildQuery($credentials);
        $user = $this->connection->selectOne($sql . implode(" AND ", $query));
        if (!is_null($user)) {
            return new VoltDBUser((array) $user, $this->config);
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  AuthenticateUserContract $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(AuthenticateUserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
        return $this->hasher->check($plain, $user->getAuthPassword());
    }


    /**
     * @todo
     * @param array $credentials
     * @return array
     */
    protected function buildQuery(array $credentials)
    {
        $query = [];
        $columns = $this->connection->procedure("@SystemCatalog", ["COLUMNS"]);
        if(count($columns)) {
            foreach ($columns as $row) {
                // not supported prepared statement
                foreach ($credentials as $key => $value) {
                    if (!str_contains($key, 'password')) {
                        if ($row['TABLE_NAME'] == strtoupper($this->table)
                            && $row['COLUMN_NAME'] == strtoupper($key)
                        ) {
                            $value = $this->connection->convertType($row['TYPE_NAME'], $value);
                            $query[] = "{$key} = " . $value;
                        }
                    }
                }
            }
        }
        return $query;
    }

}
