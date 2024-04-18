<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiManager extends Controller
{
    
    /*
    0=>error,
    1=>Success,
    2=>DuplicateEntry
    3=>Does Not Exist
    */
    // Retrieve all users
    public function index(Response $response)
    {
        return "<p>hii</p>";
    }
    public function GetAllUsers(Response $response)
    {
        $users = User::all();
        return response()->json(["data"=>$users,"status"=>'1']);
    }

    // Retrieve a specific user by ID
    public function GetUserById(Request $request)
    {
        $requestData = $request->json()->all();
        $id = $requestData['id'];
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found',"status"=>'3'], 404);
        }
        return response()->json(["data"=>$user,"status"=>'1']);
    }

    // Create a new user
    public function CreateUser(Request $request)
    {

        try {


            $requestData = $request->json()->all();
            //verifying uniqueness
            $isExist = User::where('User_Email', $requestData['User_Email'])->exists();
            if ($isExist) {
                return response()->json(['message' => "Duplicate entry","status"=>'2'], 409);
            }
            // Create a new user instance
            $user = new User();

            // Set user properties from decoded JSON data
            $user->User_FullName = $requestData['User_FullName'];
            $user->User_UserName = $requestData['User_UserName'];
            $user->User_Email = $requestData['User_Email'];
            $user->User_Phone = $requestData['User_Phone'];
            $user->User_DOB = $requestData['User_DOB'];
            $user->User_Password = bcrypt($requestData['User_Password']); // Encrypt password before saving
            // Add more fields as needed
            // return response()->json($requestData);
            // Save the user
            $user->save();

            // Return a response
            return response()->json(['message' => "Data Added Successfully", 'status' => "1"], 201);
        } catch (Exception $e) {
            return response()->json(['message' => "Unexpected Error", 'status' => "0", 'error' => $e->getMessage()], 400);
        }
        // Decode JSON request body
    }
    // Update an existing user
    public function UpdateUser(Request $request)
    {
        try {

            $requestData = $request->json()->all();
            $id = $requestData['id'];
            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found',"status"=>'3'], 404);
            }

            $user->id = $requestData['id'];
            $user->User_FullName = $requestData['User_FullName'];
            $user->User_UserName = $requestData['User_UserName'];

            $user->User_Email = $requestData['User_Email'];
            $user->User_Phone = $requestData['User_Phone'];
            $user->User_DOB = $requestData['User_DOB'];
            $user->save();
            return response()->json(["data"=>$user,"status"=>'1'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(),"status"=>'0'], 200);
        }
    }

    // Delete a user
    public function DeleteUser($id)
    {
         
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found',"status"=>'3'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted',"status"=>'1'], 200);
    }
    
     public function LoginUser(Request $request)
    { 
            try {
                // Retrieve JSON data from the request
                $requestData = $request->json()->all();
                
                // Retrieve the email from the request data
                $email = $requestData["User_Email"];
                
                // Retrieve the user record by email
                $user = User::where("User_Email", $email)->first();
                
                // If user exists and password matches

               if ($user && Hash::check($requestData["User_Password"],  $user->User_Password)) {
                    return response()->json(['status' => '1', 'message' => 'User authenticated successfully', 'user' => $user], 200);
                }
                // If user does not exist or password doesn't match, return failure response
                return response()->json(['status' => '3', 'message' => 'Invalid email or password'], 200);
                
            } catch (Exception $e) {
                // If an exception occurs, return error response
                return response()->json(['status' => '0', 'error' => $e->getMessage()], 500);
            }
    }
    
    
     public function ForgotPassword(Request $request)
    {
        try {
            $requestData = $request->json()->all();
            $email = $requestData["User_Email"];

            $user = User::where("User_Email", $email)->first();
            if (!$user) {
                return response()->json(['error' => 'User does not exist', 'status' => '3'], 404);
            }

            // Generate OTP
            $otp = '';
            $length = 6; // Length of the OTP

            for ($i = 0; $i < $length; $i++) {
                $otp .= mt_rand(0, 9); // Append a random number between 0 and 9 to the OTP string
            } // Generate a random OTP of 6 characters

            // Send OTP to the user's email
              $url = 'https://api.elasticemail.com/v2/email/send';


            $post = array(
                'from' => 'aptech.saif@gmail.com',
                'fromName' => 'Coffe Shop',
                'apikey' => '097932A7C3813B3C4AE33DDFB3C8499313C066FD51AB4CD1B0B06C179F8ACF48F364349B13D92BD5C57C0EDCEA7FA39A',
                'subject' => 'OTP',
                'to' => $user->User_Email,
                'bodyHtml' => "<h1>otp is $otp</h1>",
                'bodyText' => 'Text Body',
                'isTransactional' => true
            );

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));

            $result = curl_exec($ch);
            curl_close($ch);
 
            
 


            return response()->json(['data' => $user, 'status' => '1', 'mail' => json_decode($result), 'otp' => $otp], 200);

            // You may also store the OTP in the database for verification purposes

            return response()->json(['data' => $user, 'status' => '1', 'otp' => $otp ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0'], 500);
        }
    }
    
    public function ChangePassword(Request $request){
        $requestData=$request->json()->all();
        $Email=$requestData['User_Email'];
        $Pass=$requestData['User_Password'];
        $EncPass=bcrypt($Pass);
        try{
            $user = User::where("User_Email", $Email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found',"status"=>'3'], 404);
            }
            $user->User_Password=$EncPass;
            $user->save();
              return response()->json(['status' => '1', 'data' => $user], 200);
        }catch(Exception $e){
           return response()->json(['status' => '0', 'error' => $e->getMessage()], 500);
        }
    }
    
    
}
