<?php

namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
  
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];
  
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
  
    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     // Define relationship to loans as a lender
     public function loansAsLender()
     {
         return $this->hasMany(Loan::class, 'lender_id');
     }
 
     // Define relationship to loans as a borrower
     public function loansAsBorrower()
     {
         return $this->hasMany(Loan::class, 'borrower_id');
     }
 
     
}