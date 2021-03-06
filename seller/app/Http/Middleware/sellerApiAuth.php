<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Token;

class sellerApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token =$request->header("Authorization");
        $isvalid=Token::where('token',$token)->where('expired_at',NULL)->first();
        if($isvalid){
            return $next($request);
        }
        else{
            return response()->json([
                "status" => 402,
                "message" => "unautorized"
            ]);
        }
    }
}
