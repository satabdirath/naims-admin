<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ChatMail;
use Exception;


class ChatController extends Controller {

    public function index() {

    
        return view('chat');


    }


    public function sendMail(Request $request)
{
    try {
        $data = [
            'from' => $request->from,
            'to' => $request->to,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        Mail::to($data['to'])->send(new ChatMail($data));

        return response()->json(['success' => 'Mail sent successfully!']);
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Mail sending failed!',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}
}