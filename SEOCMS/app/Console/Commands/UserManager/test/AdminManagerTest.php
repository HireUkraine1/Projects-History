<?php

namespace App\Console\Commands\UserManager\Test;

use Tests\TestCase;
use App\Console\Commands\UserManager\AdminManager;
use Mockery;
use App\Models\Admin;
use Faker\Factory as Faker;

class AdminManagerTest extends TestCase
{
    /**
     * @var
     */
    private $adminManager;

    /**
     * @var
     */
    private $faker;

    /**
     * @var
     */
    private $admin;

    /**
     * @var
     */
    private $admin_email;

    /**
     * Start test
     */
    public function setUp()
    {
        parent::setUp();

        $this->adminManager = Mockery::mock(AdminManager::class . '[choice, newAdminData, info, ask, confirm]')->makePartial();
        $this->faker = Faker::create();
    }

    /**
     * Create method test
     */
    public function testCreate()
    {
        $this->setChoise('create');
        $this->setAdminData();
        $this->adminManager->handle();

        $this->checkAdmin();
    }

    /**
     * Delete method test
     */
    public function testDelete()
    {
        $this->createTestAdmin();

        if (!$this->admin) {
            $this->assertTrue(false);
        }

        $this->setChoise('delete');
        $this->adminManager->handle();
        $this->checkAdmin(true);
    }

    /**
     * Down test
     */
    public function tearDown()
    {
        parent::tearDown();

        Mockery::close();

        if ($this->admin) {
            $this->admin->delete();
        }
    }

    /**
     * @param $response
     */
    private function setChoise($response)
    {
        $this->adminManager
            ->shouldReceive('choice')
            ->once()
            ->andReturn($response)
        ;

        $this->adminManager
            ->shouldReceive('info')
            ->once()
            ->andReturn(true)
        ;

        if ($response == 'delete') {
            $this->adminManager
                ->shouldReceive('ask')
                ->with(__('console/users-manager.admin.email'))
                ->andReturn($this->admin_email)
            ;

            $this->adminManager
                ->shouldReceive('confirm')
                ->andReturn(true)
            ;
        }
    }

    /**
     * Set admin data
     */
    private function setAdminData()
    {
        $this->adminManager
            ->shouldReceive('newAdminData')
            ->once()
            ->andReturn($this->getAdminData())
        ;
    }

    /**
     * Return admin data
     * @return array
     */
    private function getAdminData()
    {
        $name = $this->faker->name;
        $this->admin_email = $email = $this->faker->email;
        $password = 'secret';
        $password_confirmation = $password;

        return compact(
            'name', 'email', 'password', 'password_confirmation'
        );
    }

    /**
     * Check admin user
     */
    private function checkAdmin($delete=false)
    {
        $this->admin = Admin::emailFilter($this->admin_email)->first();
        $check = $this->admin ? true : false;
        $this->assertTrue($delete ? !$check : $check);
    }

    /**
     * Create test admin
     */
    private function createTestAdmin()
    {
        $data = $this->getAdminData();
        $this->admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
