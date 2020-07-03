<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable=['title', 'author_id'];

    public function path()
    {
        return 'books/'.$this->id;
    }

    public function checkout($user)
    {
        $this->reservations()->create([
            'user_id' => $user->id,
//            'book_id'=> $this->id,
            'checked_out_at'=> today(),
        ]);
    }

    public function checkin($user)
    {
        $reservation = $this->reservations()->where('user_id', $user->id)
            ->whereNotNull('checked_out_at')
            ->whereNull('checked_in_at')
            ->first();

        if (is_null($reservation)){
            throw new \Exception();
        }

        $reservation->update(['checked_in_at'=>today()]);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function setAuthorIdAttribute($author)
    {
        $this->attributes['author_id'] = (Author::firstOrCreate([
            'name' => $author,
        ]))->id;
    }
}
