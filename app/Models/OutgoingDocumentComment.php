<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingDocumentComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id','creatd_at','updated_at','deleted_at'];

    public function document(){
        return $this->belongsTo(OutgoingDocument::class,'document_id');
    }

    public function route(){
        return $this->belongsTo(OutgoingDocumentRoute::class, 'route_id');
    }

    public function comment_by(){
        return $this->belongsTo(User::class,'comment_by')->select('users.id','employee_id');
    }
    
    public function designation(){
        return $this->belongsTo(Designation::class,'designation_id')->select('id','name');
    }

    public function department(){
        return $this->belongsTo(Department::class, 'department_id')->select('id','name');
    }

    public function division(){
        return $this->belongsTo(Division::class,'division_id')->select('id','name');
    }
}
