<?php

namespace App\Forward;

use Packages\Testing\App\Test\TestCase;

class PortTest extends TestCase
{
    /**
     * @var string CRUD port url
     */
    protected $url = 'http://localhost:8000/api/forward/gateway/:gateway/port';

    protected $gateway;
    protected $port;

    protected $permissions = [
        'read'  => 'network.forward.read',
        'write' => 'network.forward.write',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->gateway = factory(\App\Forward\Gateway\Gateway::class)->create();

        $this->authAdmin();
    }

    public function tearDown()
    {
        if ($this->gateway) {
            $this->authAdmin();
            $this->gateway->delete();
        }
    }

    public function testCreate()
    {
        $gatewayId = $this->gateway->id;
        $portUrl   = str_replace(':gateway', $gatewayId, $this->url);

        $this->post($portUrl, $this->portData($gatewayId));

        $this->assertResponseStatus(200);

        $this->port = $this->response->getData();
    }

    public function testShow()
    {
        $this->testCreate();

        $gatewayId = $this->gateway->id;
        $portUrl   = str_replace(':gateway', $gatewayId, $this->url);

        $this->get($portUrl);

        $this->see("\"gateway\":{\"id\":{$gatewayId},\"hostname\":\"{$this->gateway->hostname}\"}");
        $this->see('"src_port":424');
        $this->see('"is_acl_enabled":1');
        $this->see('"dest_ip":"10.10.1.54"');
        $this->see('"dest_port":567');
        $this->see('"type":{"id":1,"name":"Web (HTTPS)"}');

        $this->assertResponseStatus(200);

    }

    public function testUpdate()
    {
        $this->testCreate();

        $gatewayId = $this->gateway->id;
        $portUrl   = str_replace(':gateway', $gatewayId, $this->url);

        $this->patch("{$portUrl}/{$this->port->data->id}", $this->portDataEdit($gatewayId));

        $this->see("\"gateway\":{\"id\":{$gatewayId},\"hostname\":\"{$this->gateway->hostname}\"}");
        $this->see('"src_port":"242"');
        $this->see('"is_acl_enabled":false');
        $this->see('"dest_ip":"10.10.1.55"');
        $this->see('"dest_port":"5678"');
        $this->see('"type":{"id":2,"name":"IPMI"}');

        $this->assertResponseStatus(200);

        $this->port = $this->response->getData();
    }

    public function testSamePort()
    {
        $this->testCreate();

        $gatewayId = $this->gateway->id;
        $portUrl   = str_replace(':gateway', $gatewayId, $this->url);

        $this->post($portUrl, $this->portData($gatewayId));

        $this->assertResponseStatus(422);
    }

    public function testDeletePort()
    {
        $this->testCreate();

        $portUrl = str_replace(':gateway', $this->gateway->id, $this->url);

        $this->delete("{$portUrl}/{$this->port->data->id}");

        $this->assertResponseStatus(200);

        $this->get("{$portUrl}/{$this->port->data->id}");

        $this->assertResponseStatus(404);
    }

    public function testAdminNoPermsPort()
    {
        $this->testCreate();

        $admin = $this->authNewAdmin();

        $gatewayId = $this->gateway->id;
        $url   = str_replace(':gateway', $gatewayId, $this->url);

        /* Show */
        $this->get($url);
        $this->assertResponseStatus(403);

        /* Store */
        $this->post($url, $this->portDataEdit($gatewayId));
        $this->assertResponseStatus(403);

        /* Patch */
        $this->patch("{$url}/{$this->port->data->id}", $this->portDataEdit($gatewayId));
        $this->assertResponseStatus(403);

        /* Delete */
        $this->delete("{$url}/{$this->port->data->id}");
        $this->assertResponseStatus(403);

        $this->eraseAdmin($admin);
    }

    public function portData($gatewayId)
    {
        return [
            'gateway_id'     => $gatewayId,
            'src_port'       => '424',
            'dest_ip'        => '10.10.1.54',
            'dest_port'      => '567',
            'is_acl_enabled' => true,
            'type'           => ['id' => 1],
        ];
    }

    public function portDataEdit($gatewayId)
    {
        return [
            'gateway_id'     => $gatewayId,
            'src_port'       => '242',
            'dest_ip'        => '10.10.1.55',
            'dest_port'      => '5678',
            'is_acl_enabled' => false,
            'type'           => ['id' => 2],
        ];
    }

}
