<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Response;

class RegisterUserValidate extends FormRequest{

    protected $errorBag = 'correctMemberDetailsForm';
    public function rules(){
        return [
            'user_name' => 'required|min:5|regex:/(^[A-Za-z. ]+$)+/',
            'user_email' => 'required|email|unique:login,username',
            'user_contact' => 'required|digits:10|unique:User_Register,contact'
           ];
    }

    public function authorize(){
        // Only allow logged in users
        // return \Auth::check();
        // Allows all users in
        return true;
    }
}