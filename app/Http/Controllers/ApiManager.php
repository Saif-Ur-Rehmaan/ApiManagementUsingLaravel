<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

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
    public function DeleteUser(Request $request)
    {
        $requestData = $request->json()->all();
        $id = $requestData['id'];
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found',"status"=>'3'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted',"status"=>'1'], 200);
    }
}
