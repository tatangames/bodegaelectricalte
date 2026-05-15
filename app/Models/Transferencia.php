<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;
    protected $table = 'transferencia';
    public $timestamps = false;

    protected $fillable = ['id_tipoproyecto', 'fecha', 'descripcion', 'documento'];

}
