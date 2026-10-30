<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Tamu yang membuka halaman utama diarahkan ke login.
     */
    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
