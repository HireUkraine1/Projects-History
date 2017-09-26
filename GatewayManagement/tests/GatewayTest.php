<?php

namespace App\Forward;

use Packages\Testing\App\Test\TestCase;

class GatewayTest extends TestCase
{
    /**
     * @var string CRUD gateway url
     */
    protected $url = 'http://localhost:8000/api/forward/gateway';

    protected $gateway;

    protected $permissions = [
        'read'  => 'network.forward.read',
        'write' => 'network.forward.write',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->authAdmin();
    }

    public function tearDown()
    {
        $this->authAdmin();
        $this->delete("{$this->url}/{$this->gateway->data->id}");
        $this->assertResponseStatus(200);
    }

    public function testCreate()
    {
        $this->post($this->url, $this->data());

        $this->assertResponseStatus(200);

        $this->gateway = $this->response->getData();
    }

    public function testShow()
    {
        $this->testCreate();

        $this->get("{$this->url}/{$this->gateway->data->id}");
        $this->see('"hostname":"hostname"');
        $this->see('"port_limit":2');

        $this->assertResponseStatus(200);
    }

    public function testUpdate()
    {
        $this->testCreate();

        $this->patch("{$this->url}/{$this->gateway->data->id}", $this->dataEdit());

        $this->see('"hostname":"hostname_2"');
        $this->see('"port_limit":"3"');

        $this->assertResponseStatus(200);

        $this->gateway = $this->response->getData();
    }

    public function testAdminNoPerms()
    {
        $this->testCreate();

        $admin = $this->authNewAdmin();

        /* Store */
        $this->post("{$this->url}/{$this->gateway->data->id}", $this->dataEdit());
        $this->assertResponseStatus(403);

        /* Patch */
        $this->patch("{$this->url}/{$this->gateway->data->id}", $this->dataEdit());
        $this->assertResponseStatus(403);

        $this->eraseAdmin($admin);
    }

    public function data()
    {
        return [
            'hostname'   => 'hostname',
            'port_limit' => '2',
        ];
    }

    public function dataEdit()
    {
        return [
            'hostname'   => 'hostname_2',
            'port_limit' => '3',
        ];
    }

}
