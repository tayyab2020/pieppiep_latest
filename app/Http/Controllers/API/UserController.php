<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\messages;
use Illuminate\Support\Facades\Auth; 
use Validator;

class UserController extends Controller 
{
    public $successStatus = 200;

    public function AuthRouteAPI(Request $request){
        return $request->user();
    }
    
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] = $user->createToken('MyApp')->accessToken; 
            return response()->json(['success' => $success], $this->successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')->accessToken; 
        $success['name'] =  $user->name;
        
        return response()->json(['success'=>$success], $this->successStatus); 
    }

    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus); 
    }

    public function getChatList(Request $request)
    {
        $users = User::where("id","!=",$request->UserID)->with(['from_messages' => function($q) use($request) {
            $q->where('to_user_id', '=', $request->UserID);
        }])->with(['last_from_message' => function($q) use($request) {
            $q->where('to_user_id', '=', $request->UserID);
        }])->withCount(['unseen_messages' => function($q) use($request) {
            $q->where('to_user_id', '=', $request->UserID);
        }])->get();
        
        return $users;
    }

    public function update_messages_seen($user_id,$to_user_id)
    {
        messages::where('from_user_id',$to_user_id)->where('to_user_id',$user_id)->update(["seen" => 1]);
    }

    public function getMessages(Request $request)
    {
        $this->update_messages_seen($request->UserID,$request->ToUserID);

        $messages = messages::select("id", "from_user_id as fromUserId", "to_user_id as toUserId", "seen", "message", "time", "date", "type", "file_format as fileFormat", "file_path as filePath")
        ->where(function($query) use($request) {
            $query->where('from_user_id',$request->UserID)->where('to_user_id',$request->ToUserID);
        })
        ->orWhere(function($query) use($request) {
            $query->where('from_user_id',$request->ToUserID)->where('to_user_id',$request->UserID);
        })->orderBy("id","asc")->get();

        return $messages;
    }
}