<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'name'=>'required|unique:users,name',
                'password'=>'required',
                'c_password'=>'required|same:password'
            ]);
            if( $validator->fails())
                return $this->sendError('Validate Error',$validator->errors());
            $input =$request->all();
            $input['password']=Hash::make($input['password']);
            $user=User::create($input);
            $success['token']=$user->createToken('12345678')->accessToken;
            $success['name']=$user->name;
            // $success['is_admin'] = $user->is_admin;
            return $this->sendResponse($success, 'User is registered Successfully!');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function login(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(),[
                'name'=>'required',
                'password'=>'required'
            ]);
            if( $validator->fails())
                return $this->sendError('Validate Error',$validator->errors());
            $remember_me = ($request->input('remember_me')=='1')?true:false;
            if ($remember_me)
                Passport::personalAccessTokensExpireIn(Carbon::now()->addYears(1));
            if (Auth::attempt(['name' => $request->name, 'password' => $request->password]))
            {
                $user = Auth::user();
                $success['token']= $user->createToken('12345678')->accessToken;
                $success['name']=$user->name;
                return $this->sendResponse($success, 'User is login Successfully');
            }
            else
                return $this->sendError('Unauthorized',['error','Unauthorized']);
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function changeAdminPassword(Request $request)
    {
        try
        {
            $user = Auth::user();
            $user=User::find(Auth::id());
            if($user->is_admin == 1)
            {
                $validator = Validator::make($request->all(),[
                    'password'=>'required',
                    'c_password'=>'required|same:password'
                ]);
                if( $validator->fails())
                    return $this->sendError('Validate Error',$validator->errors());
                $input =$request->all();
                $user->password = Hash::make($input['password']);
                $user->save();
                return $this->sendResponse('success', 'Password is changed Successfully!');
            }
            else
                return $this->sendError('You do not have rights to change your password, You must be admin');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function showUsers()
    {
        try {
            $user=User::find(Auth::id());
            if($user->is_admin == 1)
            {
                $users=User::where('is_admin','!=', 1)->get();
                if($users->count()==0)
                    return $this->SendError('There is no users');
                return $this->SendResponse($users, 'Usesrs are retrieved Successfully!');
            }
            else
                return $this->sendError('You do not have rights to access, You must be admin');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            $user=User::find(Auth::id());
            if($user->is_admin ==1)
            {
                $deletedUser=User::find($id);
                if(is_null($deletedUser))
                    return $this->SendError('user is not found');
                else if($deletedUser->is_admin == 1)
                    return $this->sendError('You can not remove admin account');
                $deletedUser->delete();
                return $this->SendResponse($deletedUser, 'User is deleted Successfully!');
            }
            else
                return $this->sendError('You do not have rights to access, You must be admin');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }
}
