<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 9/18/2017
 * Time: 3:57 PM
 */

namespace App\Helper;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
class NotificationManager implements MessageComponentInterface
{


    private $connections;
    private $users = [];
    private $uid = [];
    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function  __construct()
    {
        $this->connections = new \SplObjectStorage();
    }

    function onOpen(ConnectionInterface $conn)
    {
        // TODO: Implement onOpen() method.
        $this->connections->attach($conn);
        Log::info("connection open....".$conn->resourceId." total connection ".$this->connections->count()." connected use id...".count($this->users));
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
        $this->connections->detach($conn);
        if(isset($this->users[$conn->resourceId])){
            Log::info("unset user....".$this->users[$conn->resourceId]);
            unset($this->users[$conn->resourceId]);

        }
        Log::info("connection close....".$conn->resourceId);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
        Log::info($e->getMessage());
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        // TODO: Implement onMessage() method.
        Log::info($msg);
        $message = json_decode($msg,true);
        switch ($message['type']){
            case 'init':
                $this->users[$from->resourceId] = $message['data']['user_id'];
                break;
            case 'notification':
                $to = $message['data']['to'];
                $m = $message['data']['message'];
                Log::info($to);
                foreach ($to as $t){
                    if(!($id = array_search($t,$this->users))){
                        continue;
                    }
                    Log::info($id);
                    foreach ($this->connections as $connection){
                        if($connection->resourceId==$id){
                            $connection->send($m);
                        }
                    }
                }

        }
    }
}