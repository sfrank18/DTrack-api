<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OutgoingDocument extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = ['attachments'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function getAttachmentsAttribute () {
        $path = public_path('storage\\outgoing\\'.$this->document_no);

        if(File::exists($path)){
            $files = [];
            foreach(File::allFiles($path) as $file){
                $files[] = [
                    'name'=>$file->getFilename(),
                    'path'=>url('storage//outgoing//'.$this->document_no."//".$file->getFilename()),
                    'type'=>mime_content_type('storage//outgoing//'.$this->document_no."//".$file->getFilename()),
                    'size'=>filesize('storage//outgoing//'.$this->document_no."//".$file->getFilename())
                ];
            }
            return $files;
        }
        return null;
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function status(){
        return $this->belongsTo(DocumentStatus::class,'document_status_id')->select('id', 'name');
    }

    public function category(){
        return $this->belongsTo(OutgoingDocumentCategory::class,'outgoing_category_id')->select('id', 'name');
    }

    public function urgency(){
        return $this->belongsTo(DocumentUrgency::class,'urgency_id')->select('id', 'name');
    }

    public function designation(){
        return $this->belongsTo(Designation::class,'designation_id')->select('id', 'name');
    }

    public function department(){
        return $this->belongsTo(Department::class,'department_id')->select('id', 'name');
    }

    public function division(){
        return $this->belongsTo(Division::class,'division_id')->select('id', 'name');
    }

    public function routes(){
        return $this->hasMany(OutgoingDocumentRoute::class, 'document_id')
        ->with(['receivedBy','receivedDesignation','receivedDepartment','receivedDivision'])
        ->latest();
    }

    public function documentComments(){
        return $this->hasMany(OutgoingDocumentComment::class,'document_id')
        ->select(['comment_by','designation_id','department_id','division_id','document_id','comment','created_at'])
        ->with(['comment_by','designation','department','division']);
    }

}
