<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Log;

class MailboxController extends Controller
{
    public function getEmails($folder = 'INBOX')
    {
        try {
            // Connect to IMAP client
            $client = Client::account('default');
            $client->connect();

            // Fetch folder
            $mailbox = $client->getFolder($folder);

            if (!$mailbox) {
                Log::error("IMAP Error: Folder '{$folder}' not found.");
                return response()->json(['error' => "IMAP folder '{$folder}' not found."], 404);
            }

            // Retrieve emails
            $messages = $mailbox->query()->all()->limit(10)->get();

            if ($messages->count() === 0) {
                return response()->json(['message' => 'No emails found in ' . $folder], 200);
            }

            $emails = [];

            foreach ($messages as $message) {
                // Ensure date is valid
                $emailDate = $message->getDate();
                $formattedDate = $emailDate instanceof \Carbon\Carbon ? $emailDate->format('Y-m-d H:i:s') : 'Unknown';

                $emails[] = [
                    'subject' => $message->getSubject(),
                    'from' => $message->getFrom()[0]->mail ?? 'Unknown',
                    'date' => $formattedDate,
                    'body' => $message->getTextBody(),
                ];
            }

            return response()->json($emails);

        } catch (\Exception $e) {
            Log::error('IMAP Fetch Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch emails: ' . $e->getMessage()], 500);
        }
    }

    
}

