<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ChatMail;


class ChatController extends Controller {

    public function index() {

    
        return view('chat');


    }


    public function sendMail(Request $request)
{
    $data = [
        'from' => $request->from,
        'to' => $request->to,
        'subject' => $request->subject,
        'message' => $request->message,
    ];

    Mail::to($data['to'])->send(new ChatMail($data));

    return response()->json(['success' => 'Mail sent successfully!']);
}

}