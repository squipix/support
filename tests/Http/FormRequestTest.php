<?php

declare(strict_types=1);

namespace Squipix\Support\Tests\Http;

use Squipix\Support\Tests\Stubs\FormRequestController;
use Squipix\Support\Tests\TestCase;
use Illuminate\Routing\Router;

/**
 * Class     FormRequestTest
 *
 * @author   SQUIPIX <info@squipix.com>
 */
class FormRequestTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupRoutes($this->app['router']);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_check_validation(): void
    {
        $this->post('form-request')
             ->assertStatus(302)
             ->assertRedirect('/');

        $response = $this->post('form-request', [
            'name'  => 'SQUIPIX',
            'email' => 'squipix@example.com',
        ]);

        $response
            ->assertSuccessful()
            ->assertJson([
                'name'  => 'SQUIPIX',
                'email' => 'squipix@example.com',
            ]);
    }

    /** @test */
    public function it_can_sanitize(): void
    {
        $response = $this->post('form-request', [
            'name'  => 'Arcanedev',
            'email' => ' SQUIPIX@example.COM ',
        ]);

        $response
            ->assertSuccessful()
            ->assertJson([
                'name'  => 'ARCANEDEV',
                'email' => 'squipix@example.com',
            ]);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Setup the routes.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    private function setupRoutes(Router $router): void
    {
        $router->post('form-request', [FormRequestController::class, 'form'])
               ->name('form-request');
    }
}
