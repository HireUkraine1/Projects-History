<?php

namespace App\Support\RabbitMQ;

abstract class RabbitAbstract
{
    /**
     * @var
     */
    protected $settings;

    /**
     * Set settings from .env
     * AMQP_HOST - host
     * AMQP_PORT - port
     * AMQP_USER - user name
     * AMQP_PASSWORD - password
     * AMQP_VHOST - vhost
     * AMQP_QUEUE_NAME - The queue name can contain up to 255 bytes of UTF-8 characters
     * AMQP_PASSIVE - Can be used to check whether the exchange is initiated, without changing the state of the server
     * AMQP_DURABLE - Make sure that RabbitMQ never loses the queue in the fall - the queue will survive the reboot of the broker
     * AMQP_EXCLUSIVE - Only one connection is used, and the queue will be deleted when the connection is closed
     * AMQP_AUTO_DELETE - The queue is deleted when the last subscriber is unsubscribed
     */
    protected function getSettings()
    {
        $this->settings = [
            'host' => env('AMQP_HOST'),
            'port' => env('AMQP_PORT'),
            'user' => env('AMQP_USER'),
            'password' => env('AMQP_PASSWORD'),
            'vhost' => env('AMQP_VHOST'),
            'queue_name' => env('AMQP_QUEUE_NAME'),
            'passive' => env('AMQP_PASSIVE'),
            'durable' => env('AMQP_DURABLE'),
            'exclusive' => env('AMQP_EXCLUSIVE'),
            'auto_delete' => env('AMQP_AUTO_DELETE'),
        ];
    }

    /**
     * @return mixed
     */
    abstract public function connect();

    /**
     * @param $message
     * @return mixed
     */
    abstract public function put($message);

    /**
     * @return mixed
     */
    abstract public function close();
}
