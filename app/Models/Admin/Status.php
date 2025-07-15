<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'label_pt'];

    public function stores()
    {
        return $this->hasMany(Store::class, 'status_id');
    }
}
