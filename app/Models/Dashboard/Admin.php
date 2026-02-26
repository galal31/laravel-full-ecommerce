<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;
    protected $fillable = ['name','email','password','role_id'];
    // عملنا اوفر رايت علي الفنكشن ال في ال تريت عشان نخلي الداله تستخدم كلاس الاشعارات الجديد
    // AdminResetPasswordNotification 
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    //role
    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permession){
        $role = $this->role;
        // نلف علي صلاحياته لو البيرميشن فيها يرجع ترو
        $database_permissions = $role->permissions;
        foreach($database_permissions as $database_permission){
            if($database_permission == $permession){
                return true;
            }
        }
        return false;
    }
}
