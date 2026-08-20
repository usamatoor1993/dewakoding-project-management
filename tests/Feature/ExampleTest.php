<?php

it('redirects the root to the admin login page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin/login');
});