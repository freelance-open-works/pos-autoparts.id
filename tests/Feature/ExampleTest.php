<?php

it('returns a successful response', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('/');

    $response->assertStatus(302);
});
