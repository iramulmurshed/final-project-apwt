<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Models\Token;
use Illuminate\Support\Str;


class SellerController extends Controller
{
    function viewSellerSignUpPage()
    {
        if (session()->has('seller')) {
            return redirect()->route('home');
        }
        return view('pages.sellerSignUp');
    }

    function viewSellerLogInPage()
    {
        if (session()->has('seller')) {
            return redirect()->route('home');
        }

        return view('pages.sellerLogin');
    }

    function viewSellerHomePage()
    {
        return view('pages.home');
    }


    function profilePage(Request $request)
    {

        $data = Seller::where('s_id', $request->s_id)->first();

        return $data;


    }

    function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                's_name' => 'required|min:3|max:25',

                's_phone' => 'required|numeric',
                's_dob' => 'required',


            ],
            [
                's_name.required' => 'please enter your full name',
                's_name.min' => 'name can contain minimum 3 character',
                's_name.max' => 'name can contain maximum 25 character',


                's_phone.required' => 'Enter a valid phone number',
                's_phone.numeric' => 'Only use Numbers',

                's_dob.required' => 'Date of Birth is required',


            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "validation_errors" => $validator->messages(),
            ]);
        } else {
            $Seller = Seller::where('s_id', $request->s_id)->first();
            $Seller->s_name = $request->s_name;

            $Seller->s_phone = $request->s_phone;
            $Seller->s_dob = $request->s_dob;
            $Seller->save();
            return response()->json([
                "status" => 200,
                "message" => "Seller profile update successful"
            ]);
        }
    }


    function verifySignup(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                's_name' => 'required|min:3|max:25',
                's_password' => 'required|min:6',
                's_phone' => 'required|numeric',
                's_email' => 'required|unique:sellers,s_email|email',
                's_dob' => 'required',
                's_gender' => 'required',

            ],
            [
                's_name.required' => 'please enter your full name',
                's_name.min' => 'name can contain minimum 3 character',
                's_name.max' => 'name can contain maximum 25 character',

                's_email.required' => 'please enter your email',
                's_email.email' => 'please enter valid email',
                's_email.unique' => 'this email is already taken',

                's_password.required' => 'please enter your password',
                's_password.min' => 'password must contain 6 character',

                's_phone.required' => 'Enter a valid phone number',
                's_phone.numeric' => 'Only use Numbers',

                's_dob.required' => 'Date of Birth is required',

                's_gender.required' => 'gender field is required',


            ]
        );
        if ($validator->fails()) {
            return response()->json([
                "validation_errors" => $validator->messages(),
            ]);
        } else {

            $Seller = new Seller();
            $Seller->s_name = $request->s_name;
            $Seller->s_password = password_hash($request->s_password, PASSWORD_DEFAULT);
            $Seller->s_phone = $request->s_phone;
            $Seller->s_email = $request->s_email;
            $Seller->s_dob = $request->s_dob;
            $Seller->s_gender = $request->s_gender;
            $Seller->s_status = "valid";
            $Seller->save();

            return response()->json([
                "status" => 200,
                "message" => "Seller signup successful"
            ]);
        }


    }


    function verifyLogin(Request $request)
    {
        $validator = Validator::make($request->all(),
            [

                's_password' => 'required|min:6',
                's_email' => 'required|email',

            ],

            [

                's_email.required' => 'please enter your email',
                's_email.email' => 'please enter valid email',
                's_password.required' => 'please enter your password',
                's_password.min' => 'password must contain 6 character',

            ]);
        if ($validator->fails()) {
            return response()->json([
                "validation_errors" => $validator->messages(),
            ]);
        } else {
            $data = Seller::where('s_email', $request->s_email)->first();
            $verify = password_verify($request->s_password, $data->s_password);

            if ($verify) {
                $api_token = Str::random(64);
                $token = new Token();
                $token->userid = $data->s_id;
                $token->token = $api_token;
                $token->created_at = new DateTime();
                $token->save();
                return $token;
            } else {
                return "not found";

            }
        }


    }


    function sellerLogout(Request $request)
    {
        $token = $request->header("Authorization");
        $validToken = Token::where('token', $token)->first();
        $validToken->expired_at = new DateTime();
        $validToken->save();
        return "logout";
    }


}
