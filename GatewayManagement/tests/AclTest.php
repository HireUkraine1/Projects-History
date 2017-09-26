<?php

namespace App\Forward;

use Packages\Testing\App\Test\TestCase;

class AclTest extends TestCase
{
    /**
     * @var string CRUD port url
     */
    protected $portUrl = 'http://localhost:8000/api/forward/gateway/:gateway/port';

    /**
     * @var string CRUD ACL url
     */
    protected $aclUrl = 'http://localhost:8000/api/forward/port/:port/acl';

    protected $port;
    protected $acl;

    protected $permissions = [
        'read'  => 'network.forward.read',
        'write' => 'network.forward.write',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->port = factory(\App\Forward\Port\Port::class)->create();

        $this->authAdmin();
    }

    public function tearDown()
    {
        if ($this->port) {
            $this->authAdmin();
            $this->port->gateway->delete();
        }
    }

    public function testCreateAcl()
    {
        $portId = $this->port->id;
        $url    = str_replace(':port', $portId, $this->aclUrl);

        $this->post($url, $this->aclData($portId));

        $this->see("\"port_id\":{$portId}");
        $this->see('"src_ip":"10.10.1.54"');
        $this->see('"expires_at":"2015-12-31 10:42:34"');

        $this->assertResponseStatus(200);

        $this->acl = $this->response->getData();
    }

    public function testUpdateAcl()
    {
        $this->testCreateAcl();

        $portId = $this->port->id;
        $url    = str_replace(':port', $portId, $this->aclUrl);

        $this->patch("{$url}/{$this->acl->data->id}", $this->aclDataEdit($portId));

        $this->see("\"port_id\":{$portId}");
        $this->see('"src_ip":"10.10.1.55"');
        $this->see('"expires_at":"2017-05-07 08:11:56"');

        $this->assertResponseStatus(200);

        $this->acl = $this->response->getData();
    }

    public function testDeleteAcl()
    {
        $this->testCreateAcl();

        $url = str_replace(':port', $this->port->id, $this->aclUrl);

        $this->delete("{$url}/{$this->acl->data->id}");

        $this->assertResponseStatus(200);

        $this->get("{$url}/{$this->acl->data->id}");

        $this->assertResponseStatus(404);
    }

    public function testChangeIsAclEnabled()
    {
        $this->testCreateAcl();

        $gatewayId = $this->port->gateway->id;
        $portUrl   = str_replace(':gateway', $gatewayId, $this->portUrl);

        /* portDataEdit returns is_acl_enabled false */
        $this->patch("{$portUrl}/{$this->port->id}", $this->portDataAclDisabled($gatewayId));
        $this->assertResponseStatus(200);

        /* ACL should be deleted */
        $aclUrl = str_replace(':port', $this->port->id, $this->aclUrl);
        $this->get("{$aclUrl}/{$this->acl->data->id}");
        $this->assertResponseStatus(404);
    }

    public function testAdminNoPermsAcl()
    {
        $this->testCreateAcl();

        $admin = $this->authNewAdmin();

        $portId = $this->port->id;
        $url    = str_replace(':port', $portId, $this->aclUrl);

        /* Show */
        $this->get("{$url}");
        $this->assertResponseStatus(403);

        /* Store */
        $this->post("{$url}", $this->aclDataEdit($portId));
        $this->assertResponseStatus(403);

        /* Patch */
        $this->assertResponseStatus(403);
        $this->patch("{$url}/{$this->acl->data->id}", $this->aclDataEdit($portId));
        $this->assertResponseStatus(403);

        /* Delete */
        $this->delete("{$url}/{$this->acl->data->id}");
        $this->assertResponseStatus(403);

        $this->eraseAdmin($admin);
    }

    public function testDisableAcl()
    {
        $this->testCreateAcl();
    }

    public function aclData($portId)
    {
        return [
            'port_id'    => $portId,
            'src_ip'     => '10.10.1.54',
            'expires_at' => '2015-12-31 10:42:34',
        ];
    }

    public function aclDataEdit($portId)
    {
        return [
            'port_id'    => $portId,
            'src_ip'     => '10.10.1.55',
            'expires_at' => '2017-05-07 08:11:56',
        ];
    }

    public function portDataAclDisabled($gatewayId)
    {
        return [
            'gateway_id'     => $gatewayId,
            'src_port'       => '424',
            'dest_ip'        => '10.10.1.54',
            'dest_port'      => '567',
            'is_acl_enabled' => false,
            'type'           => ['id' => 1],
        ];
    }

}
