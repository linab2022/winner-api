<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use app\Models\User;
use App\Models\Participant;

class ParticipantController extends BaseController
{
    public function addParticipant(Request $request)
    {
        try
        {
            $input=$request->all();
            $validator = Validator::make($input,[
                'participant_name'=>'required',
                'comment'=>'required',
                'image' => 'mimes:jpg,jpeg,png|max:5048'
                ]);
            if( $validator->fails())
                return $this->SendError('Validate Error',$validator->errors());
            $userId=Auth::id();
            $user=User::find($userId);
            if($user->is_admin == 1)
            {
                $participant = Participant::create($input);
                if ($request->has('image')) {
                    $newImageName=time() . '-' . $request->image->getClientOriginalName();
                    $request->image->move(public_path("/images"),$newImageName);
                    $imageURL=url('/images'.'/'.$newImageName);
                    DB::table('participants')->where('participant_name', $request['participant_name'])->update([
                        'image_url' => $imageURL,
                    ]);
                }
                return $this->SendResponse($participant, 'Participant is added Successfully');
            }
            else
                return $this->SendError('You do not have rights to add participant');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function addImage(Request $request)
    {
        try
        {
            $input=$request->all();
            $validator = Validator::make($input,[
                'participant_id'=>'required',
                'image'=>'required|mimes:jpg,jpeg,png|max:5048'  //max is max image size in KB
                ]);
            if( $validator->fails())
                return $this->SendError('Validate Error',$validator->errors());
            $userId=Auth::id();
            $user=User::find($userId);
            if($user->is_admin == 1)
            {
                $participant=Participant::find($request->participant_id);
                if(is_null($participant))
                    return $this->SendError('Participant is not found');
                $newImageName=time() . '-' . $request->image->getClientOriginalName();
                $request->image->move(public_path("/images"),$newImageName);
                $imageURL=url('/images'.'/'.$newImageName);
                $participant->image_url=$imageURL;
                $participant->save();
                return $this->SendResponse($participant, 'image is added Successfully');
            }
            else
                return $this->SendError('You do not have rights to add image');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function deleteParticipant($id)
    {
        try {
            $user=User::find(Auth::id());
            if($user->is_admin == 1)
            {
                $deletedParticipant=Participant::find($id);
                if(is_null($deletedParticipant))
                    return $this->SendError('Participant is not found');
                $deletedParticipant->delete();
                return $this->SendResponse($deletedParticipant, 'Participant is deleted Successfully!');
            }
            else
            else
                return $this->SendError('You do not have rights to delete participant');            
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function isWinner ($id)
    {
        try
        {
            $userId=Auth::id();
            $user=User::find($userId);
            if($user->is_admin == 1)
            {
                $participant=Participant::find($id);
                if(is_null($participant))
                    return $this->SendError('Participant is not found');
                $participant->isWinner=1;
                $participant->save();
                return $this->SendResponse($participant, 'Participant is added to winners Successfully');
            }
            else
                return $this->SendError('You do not have rights to add winner');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function isNotWinner ($id)
    {
        try
        {
            $userId=Auth::id();
            $user=User::find($userId);
            if($user->is_admin == 1)
            {
                $participant=Participant::find($id);
                if(is_null($participant))
                    return $this->SendError('Participant is not found');
                $participant->isWinner=0;
                $participant->save();
                return $this->SendResponse($participant, 'Participant is removed from winners Successfully');
            }
            else
                return $this->SendError('You do not have rights to remove winner');
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function showAllWinners()
    {
        try {
            $user=User::find(Auth::id());
            if($user->is_admin !=1){
                return $this->sendError('You do not have rights to access, You must be admin');
            }
            else
            {
                $Participants=Participant::where('isWinner',1)->get();
                if($Participants->count()==0)
                    return $this->SendError('There is no Winners');
                return $this->SendResponse($Participants, 'Winners are retrieved Successfully!');
            }
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function showAllParticipants()
    {
        try {
            $user=User::find(Auth::id());
            if($user->is_admin !=1){
                return $this->sendError('You do not have rights to access, You must be admin');
            }
            else
            {
                $Participants=Participant::all();
                if($Participants->count()==0)
                    return $this->SendError('There is no Participants');
                return $this->SendResponse($Participants, 'Participants are retrieved Successfully!');
            }
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

    public function deleteAllParticipants()
    {
        try {
            $user=User::find(Auth::id());
            if($user->is_admin !=1)
                return $this->sendError('You do not have rights to access, You must be admin');
            else
            {
                $deletedParticipant=Participant::all();
                if(is_null($deletedParticipant))
                    return $this->SendError('There is no participants');
                $deletedParticipant->each->delete();
                return $this->SendResponse($deletedParticipant, 'All participant are deleted Successfully!');
            }
        } catch (\Throwable $th) {
            return $this->SendError('Error',$th->getMessage());
        }
    }

}
