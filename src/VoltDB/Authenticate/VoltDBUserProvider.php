<?php
namespace Ytake\LaravelVoltDB\Authenticate;

use Illuminate\Config\Repository;
use Illuminate\Auth\UserInterface;
use Illuminate\Hashing\HasherInterface;
use Ytake\LaravelVoltDB\ClientConnection;
use Illuminate\Auth\UserProviderInterface;

/**
 * Class VoltDBUserProvider
 * @package Ytake\LaravelVoltDB\Authenticate
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class VoltDBUserProvider implements UserProviderInterface
{

    /** @var string default procedure */
    protected $findUserProcedure = "Auth_findUser";

    /** @var string default procedure */
    protected $rememberTokenProcedure = "Auth_rememberToken";

    /** @var string default procedure */
    protected $updateTokenProcedure = "Auth_updateToken";

    /** @var ClientConnection */
    protected $connection;

    /** @var \Illuminate\Hashing\HasherInterface  The hasher implementation.  */
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
     * @param HasherInterface $hasher
     * @param Repository $config
     */
    public function __construct(ClientConnection $connection, HasherInterface $hasher, Repository $config)
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
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        // stored procedure name
        $findUserProcedure = $this->config->get(
            'laravel-voltdb::default.auth.procedure.findUser', $this->findUserProcedure);
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
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveByToken($identifier, $token)
    {
        //
        $rememberTokenProcedure = $this->config->get(
            'laravel-voltdb::default.auth.procedure.remember_token', $this->rememberTokenProcedure);
        $user = $this->connection->procedure($rememberTokenProcedure, [$identifier, $token]);

        if(!is_null($user)) {
            return new VoltDBUser((array) $user, $this->config);
        }
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Auth\UserInterface $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(UserInterface $user, $token)
    {
        //
        $updateTokenProcedure = $this->config->get(
            'laravel-voltdb::default.auth.procedure.update_token', $this->updateTokenProcedure);
        $this->connection->procedure($updateTokenProcedure, [$token, $user->getAuthIdentifier()]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Auth\UserInterface|null
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
     * @param  \Illuminate\Auth\UserInterface $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
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