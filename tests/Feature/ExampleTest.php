<?php

test('redirects to the localized home', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect();
});
