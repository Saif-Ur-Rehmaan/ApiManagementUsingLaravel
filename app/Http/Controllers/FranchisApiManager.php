<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Franchise;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;

/*
    0=>error,
    1=>Success,
    2=>DuplicateEntry
    3=>Does Not Exist
    */

class FranchisApiManager extends Controller
{
    public function GetAllFranchise()
    {
        try {
            $franchises = Franchise::where('Fran_IsAvailable', "Available")->get()->toArray();

            if (count($franchises)==0) {
                return response()->json(['message' => 'No franchises found', 'status' => '0']);
            }

            return response()->json(['data' => $franchises, 'status' => '1']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0']);
        }
    }
    public function GetAvailableMenuAndSeasonalCat(Request $request)
    {

        try {
            $requestData = $request->json()->all();
            $Franid = $requestData['id'];
            $MenuCat = Category::all()->where("F_Franchise", $Franid)->where('Cat_Type', "Menu")->where('Cat_IsAvailable', "Available");
            $SeasonalCat = Category::all()->where("F_Franchise", $Franid)->where('Cat_Type', "Seasonal")->where('Cat_IsAvailable', "Available");
            $res = [
                'Menu' => $MenuCat,
                'Seasonal' => $SeasonalCat,
            ];
            return response()->json(['data' => $res, 'status' => '1']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0']);
        }
    }
    public function GetAvailableItemsOfCategory(Request $request)
    {
        try {
            $requestData = $request->json()->all();
            $Catid = $requestData['id'];
            $Items = item::all()->where('Item_IsAvailable', 'Available')->where('_CategoryId', $Catid)->toArray();
            $responceArr = [];
            foreach ($Items as $Itm) {
                $item = $Itm;
                $SizesAvailableForThatItem = $this->GetSizesOfItem2($item["Item_Id"]);

                $obj = [
                    "Item" => $item,
                    "Sizes" => $SizesAvailableForThatItem["data"]
                ];
                array_push($responceArr, $obj);
            }

            $res =  $responceArr;
            return response()->json(['data' => $res, 'status' => '1']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0']);
        }
    }
    public function GetSizesOfItem2($request)
    {
        try {
            $ItemId = $request;
            $sizes = ItemSize::all()->where("IS_ItemId", $ItemId);

            $res =  $sizes;

            return ['data' => $res, 'status' => '1'];
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0']);
        }
    }
    public function GetSizesOfItem(Request $request)
    {
        try {
            $requestData = $request->json()->all();
            $SizesIdArray = json_decode($requestData['SizeOptionsIdArray'], true);
            $In = array_map('intval', $SizesIdArray);

            // Assuming you have a Model named 'Size' for your sizes table
            $sizes = ItemSize::whereIn('IS_Id', $In)->get();

            // Assuming you want to return just the IDs of the retrieved sizes
            $res = $sizes;

            return response()->json(['data' => $res, 'status' => '1']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0']);
        }
    }
    public function InsertOrder(Request $request)
    {


        try {
            // Get the JSON data from the request           
            $data = $request->json()->all();
            // Create a new order instance and fill it with the provided data
            $order = new Order();
            $order->OrderId = null;
            $order->UserId = $data['UserId'];
            $order->ItemId = $data['ItemId'];
            $order->SizeId = $data['SizeId'];
            $order->EstimatedTimeByUser = $data['EstimatedTimeByUser'];
            $order->ItemTotalPrice = $data['ItmTotalPrice'];
            $order->PaymentStatus = $data['PaymentStatus'];
            $order->PaymentType = $data['PaymentType'];
            $order->Subtotal = $data['Subtotal'];
            $order->Type = $data['Type'];
            $order->Vattotal = $data['Vattotal'];
            $order->WhatsIncluded = $data['WhatsIncluded']; // Convert array to JSON string
            $order->Quantity = $data['quantity'];
            $order->OrderNumber = $data['OrderNumber'];
            $order->FranchiseId = $data['FranchiseId'];
            $order->totalPrice = $data['totalPrice'];

            // Save the order to the database
            $order->save();
            return response()->json(['data' => $order, 'status' => '1']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0']);
        }
    }
    public function GetOrderOfOrderNumber(Request $request)
    {

        try {
            $req = $request->json()->all();
            $id = $req["OrderNumber"];
            
          $Orders = Order::where("OrderNumber", $id)->get()->toArray();

            if (count($Orders)==0) {
                return response()->json(['message' => 'No franchises found', 'status' => '0']);
            } 
            $Franchise=Franchise::all()->where("id",$Orders[0]["FranchiseId"])->first();
            $res =[
                "Orders"=>$Orders,
                "Franchise"=>$Franchise
            ] ;

            return response()->json(['data' => $res, 'status' => '1']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => '0']);
        }
    }
}
