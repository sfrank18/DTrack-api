<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['role','employee'];

    public function getRoleAttribute(){
        $role = Role::join('model_has_roles', 'model_has_roles.role_id', '=', 'roles.id')->where('model_id', $this->id)->first();
        return $role ? ["role_id"=>$role->id,"role_name"=>$role->name] : null; 
    }

    public function getEmployeeAttribute(){
        return Employee::select([
            'employees.name',
            'designations.id as designation_id',
            'designations.name as designation_name',
            'departments.id as department_id',
            'departments.name as department_name',
            'divisions.id as division_id',
            'divisions.name as division_name',
            ])
        ->leftJoin('designations','designations.id','employees.designation_id')
        ->leftJoin('departments','departments.id','employees.department_id')
        ->leftJoin('divisions','divisions.id','departments.division_id')
        ->where('employees.id',$this->employee_id)->first();
    }

}
