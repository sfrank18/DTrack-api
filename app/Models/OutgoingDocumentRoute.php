<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingDocumentRoute extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at','updated_at'];

    public function receivedBy(){
        return $this->belongsTo(User::class, 'received_by')->select('users.id','users.name','employee_id');
    }

    public function completeDocumentDetails(){
        return $this->belongsTo(OutgoingDocument::class,'document_id')
        ->with(['createdBy','designation','department','division','status','category','urgency']);
    }
    
    public function documentDetails(){
        return $this->belongsTo(OutgoingDocument::class,'document_id');
    }

    public function receivedDesignation(){
        return $this->belongsTo(Designation::class, 'received_designation_id')->select('id','name');
    }

    public function receivedDepartment () {
        return $this->belongsTo(Department::class,'received_department_id')->select('id','name');
    }

    public function receivedDivision () {
        return  $this->belongsTo(Division::class,'received_division')->select('id','name');
    }

    public function comments(){
        return $this->hasMany(OutgoingDocumentComment::class,'route_id');
    }

}
