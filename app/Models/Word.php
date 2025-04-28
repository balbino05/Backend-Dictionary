<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Word",
 *   type="object",
 *   required={"word", "language"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="word", type="string", example="fire"),
 *   @OA\Property(property="language", type="string", example="en")
 * )
 */
class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'language'
    ];
}
