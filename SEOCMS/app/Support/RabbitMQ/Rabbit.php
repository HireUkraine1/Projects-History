<?php

namespace App\Support\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Rabbit extends RabbitAbstract
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $channel;

    /**
     * Rabbit constructor.
     */
    public function __construct()
    {
        $this->getSettings();
        //define('AMQP_DEBUG', true);
    }

    /**
     *
     * @return $this
     */
    public function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this->settings['host'],
            $this->settings['port'],
            $this->settings['user'],
            $this->settings['password'],
            $this->settings['vhost']
        );

        $this->channel = $this->connection->channel();

        $this->channel->queue_declare(
            $this->settings['queue_name'],
            $this->settings['passive'],
            $this->settings['durable'],
            $this->settings['exclusive'],
            $this->settings['auto_delete']
        );

        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function put($message)
    {
        $message = $this->convertMessage($message);
        $exchange = '';
        $this->channel
            ->basic_publish(
                $message,
                $exchange,
                $this->settings['queue_name']
            )
        ;

        return $this;
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @param $message
     * @return AMQPMessage
     */
    private function convertMessage($message)
    {
        return new AMQPMessage(
            json_encode($message)
        );
    }

}