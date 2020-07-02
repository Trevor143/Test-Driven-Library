<?php

namespace Tests\Feature;

use App\Author;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorManagementTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function an_author_can_be_created()
    {
        $this->post('author', [
            'name'=> 'Name here',
            'dob'=> '04/22/1997',
    ]);
        $this->assertCount(1, Author::all());
        $this->assertInstanceOf(Carbon::class, Author::first()->dob);
        $this->assertEquals('1997/04/22', Author::first()->dob->format('Y/m/d'));
    }
}
