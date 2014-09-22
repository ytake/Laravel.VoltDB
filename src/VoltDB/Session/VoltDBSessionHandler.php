<?php
namespace Ytake\LaravelVoltDB\Session;

use Illuminate\Config\Repository;
use Ytake\LaravelVoltDB\ClientConnection;
use Illuminate\Session\ExistenceAwareInterface;

/**
 * Class VoltDBSessionHandler
 * @package Ytake\LaravelVoltDB\Session
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class VoltDBSessionHandler implements \SessionHandlerInterface, ExistenceAwareInterface
{

    /** @var string default procedure */
    protected $sessionFindProcedure = "Session_find";

    /** @var string default procedure */
    protected $sessionAddProcedure = "Session_add";

    /** @var string default procedure */
    protected $sessionUpdateProcedure = "Session_update";

    /** @var string default procedure */
    protected $sessionForgetProcedure = "Session_forget";

    /** @var string default procedure */
    protected $sessionDeleteActivityProcedure = "Session_delete_activity";

    /** @var string ClientConnection */
    protected $connection;

    /**
     * The existence state of the session.
     * @var bool
     */
    protected $exists;

    /**
     * @param ClientConnection $connection
     * @param Repository $config
     */
    public function __construct(ClientConnection $connection, Repository $config)
    {
        $this->config = $config;
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @param $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        // stored procedure name
        $procedure = $this->config->get(
            'laravel-voltdb::default.session.procedure.find', $this->sessionFindProcedure
        );
        $session = $this->connection->procedure($procedure, [$sessionId]);
        if(!is_null($session)) {
            if (is_array($session)) {
                $session = (object)array_change_key_case($session[0]);
            }
            if (isset($session->payload)) {
                $this->exists = true;
                return base64_decode($session->payload);
            }
        }
    }

    /**
     * {@inheritDoc}
     * @return void
     */
    public function write($sessionId, $data)
    {
        if ($this->exists) {
            // stored procedure name
            $updateProcedure = $this->config->get(
                'laravel-voltdb::default.session.procedure.update', $this->sessionUpdateProcedure
            );
            $this->connection->procedure($updateProcedure, [
                    base64_encode($data),
                    time(),
                    $sessionId
                ]
            );
        } else {
            // stored procedure name
            $addProcedure = $this->config->get(
                'laravel-voltdb::default.session.procedure.add', $this->sessionAddProcedure
            );
            $this->connection->procedure($addProcedure, [
                    $sessionId,
                    base64_encode($data),
                    time(),
                ]
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId)
    {
        // stored procedure name
        $procedure = $this->config->get(
            'laravel-voltdb::default.session.procedure.forget', $this->sessionForgetProcedure
        );
        $this->connection->procedure($procedure, [$sessionId]);
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime)
    {
        // stored procedure name
        $procedure = $this->config->get(
            'laravel-voltdb::default.session.procedure.deleteActivity', $this->sessionDeleteActivityProcedure
        );
        $this->connection->procedure($procedure, [time() - $lifetime]);
    }

    /**
     * Set the existence state for the session.
     *
     * @param  bool  $value
     * @return $this
     */
    public function setExists($value)
    {
        $this->exists = $value;
        return $this;
    }
} 