<?php

namespace Tests\Feature;

use App\Book;
use App\Reservation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BookCheckoutTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function a_book_can_be_checked_out_by_a_signed_in_user()
    {

        $book = factory(Book::class)->create();
        $this->actingAs($user = factory(User::class)->create())
            ->post('/checkout/'.$book->id);

        $this->assertCount(1, Reservation::all());
        $this->assertEquals($user->id, Reservation::first()->user_id);
        $this->assertEquals($book->id, Reservation::first()->book_id);
        $this->assertEquals(today(), Reservation::first()->checked_out_at);

    }

    /** @test */
    public function only_signed_in_users_can_checkout_a_book()
    {
//        $this->withoutExceptionHandling();

        $book = factory(Book::class)->create();

        $this->post('/checkout/'.$book->id)
            ->assertRedirect('login');

        $this->assertCount(0, Reservation::all());
    }

    /** @test */
    public function only_real_books_can_be_checked_out()
    {
//        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create())
            ->post('/checkout/12')
        ->assertStatus(404);

        $this->assertCount(0, Reservation::all());
    }

    /** @test */
    public function a_book_can_be_checked_in_by_a_signed_in_user()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user)
            ->post('/checkout/'.$book->id);

        $this->actingAs($user)
            ->post('/checkin/'.$book->id);

        $this->assertCount(1, Reservation::all());
        $this->assertEquals($user->id, Reservation::first()->user_id);
        $this->assertEquals($book->id, Reservation::first()->book_id);
        $this->assertEquals(today(), Reservation::first()->checked_in_at);
    }

    /** @test */
    public function only_signed_in_users_can_checkin_a_book()
    {
//        $this->withoutExceptionHandling();

        $book = factory(Book::class)->create();
        $this->actingAs(factory(User::class)->create())
            ->post('/checkout/'.$book->id);
        Auth::logout();

        $this->post('/checkin/'.$book->id)
            ->assertRedirect('/login');

        $this->assertCount(1, Reservation::all());
        $this->assertNull(Reservation::first()->checked_in_at);
    }

    /** @test */
    public function a_404_thrown_if_book_is_not_checked_out_first()
    {
//        $this->withoutExceptionHandling();
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post('/checkin/'.$book->id)
            ->assertStatus(404);

        $this->assertCount(0, Reservation::all());

    }
}
