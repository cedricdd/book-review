<?php

use App\Constants;

test('set_sorting', function () {
    $this->post(route('sorting', 'invalid'))->assertStatus(404);

    //Check all the valid sorting values
    foreach(Constants::BOOK_SORTING as $key => $value) {
        $this->post(route('sorting', 'book'), ['sorting' => $key])
            ->assertRedirect()
            ->assertSessionHas('book-sorting', $key);
    }

    //Using an invalid sorting value should drop the value from the session
    $this->post(route('sorting', 'book'), ['sorting' => 'invalid'])
        ->assertRedirect()
        ->assertSessionMissing('book-sorting');
});
