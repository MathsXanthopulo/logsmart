<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
    use HasFactory;

    protected $fillable = ['giver_id', 'receiver_id'];

    public function giver()
    {
        return $this->belongsTo(Person::class, 'giver_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Person::class, 'receiver_id');
    }
}
