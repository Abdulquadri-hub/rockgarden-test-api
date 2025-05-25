<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use function response;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Mail\ExternalMessage;
use App\Mail\InternalMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;


class MessageController extends Controller
{
    public function bulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'message_type' => 'required|in:internal,external,broadcast',
            'recipient_types' => 'required|array',
            'recipient_types.*' => 'in:client,staff,family,friend,all',
            'attachment_ids' => 'sometimes|array',
            'attachment_ids.*' => 'exists:attachments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        try {

            DB::beginTransaction();

            // Create the base message
            $message = new Message([
                'subject' => $request->subject,
                'body' => $request->body,
                'sender_id' => auth()->id(),
                'message_type' => $request->message_type,
            ]);
            $message->save();

            if ($request->has('attachments')) {
                $this->processAttachments($message, $request->attachments);
            }

            // Prepare recipients based on recipient types
            $recipients = $this->prepareRecipientsForBulkMessage(
                $request->recipient_types, 
                $request->message_type
            );
            
            
            $this->processBulkRecipients($message, $recipients);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk message sent successfully',
                'total_recipients' => count($recipients),
                'message_id' => $message->id
            ], 201);

        } catch (\Throwable $th) {
            // Rollback in case of any error
            DB::rollBack();

            // Log the error for debugging
            Log::error('Bulk Message Send Error: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send bulk message',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function getUserMessages(Request $request, User $user)
    {
       try {

        $messages = Message::with(['sender', 'recipients', 'attachments', 'replies'])
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhereHas('recipients', function($recipientQuery) use ($user) {
                          $recipientQuery->where('user_id', $user->id);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'User messages retrieved successfully',
            'data' => [
                'user' => $user,
                'messages' => $messages
            ]
        ], 200);

       } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }

    public function outbox(Request $request)
    {
        try {
            $messages = Message::with(['sender', 'recipients', 'attachments'])
                ->whereHas('sender', function($query) {
                    $query->where('sender_id', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
    
            return response()->json([
                    'success' => true,
                    'data' => $messages
                ], 200);
        } catch (\Throwable $th) {
            
            return response()->json([
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }
    
    public function inbox(Request $request)
    {
        try {
            $messages = Message::with(['sender', 'recipients', 'attachments', 'replies'])
                ->whereHas('recipients', function($query) {
                    $query->where('user_id', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
    
            return response()->json([
                    'success' => true,
                    'data' => $messages
                ], 200);
        } catch (\Throwable $th) {
            
            return response()->json([
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }

    public function store(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(),[
                'subject' => 'required|string|max:255',
                'body' => 'required|string',
                'message_type' => 'required|in:internal,external',
                'recipients' => 'required|array|min:1',
                'recipients.*.type' => 'required|in:internal,external',
                'recipients.*.id' => 'required_if:recipients.*.type,internal|exists:users,id',
                'recipients.*.email' => 'required_if:recipients.*.type,external|email',
                'attachments.*' => 'sometimes|string', 
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 400);
            }
            
            $message = new Message([
                'subject' => $request->subject,
                'body' => $request->body,
                'sender_id' => auth()->id(),
                'message_type' => $request->message_type,
            ]);
    
            $message->save();

            if ($request->has('attachments')) {
                $this->processAttachments($message, $request->attachments);
            }
    
            $this->processRecipients($message, $request->recipients);
    
    
    
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message->load(['sender', 'recipients', 'attachments'])
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
        
    }

    public function show(Message $message)
    {
        try {
            $this->authorize('view', $message);
            
            return response()->json([
                'success' => true,
                'message' => 'Message fetched successfully',
                'data' => $message->load(['sender', 'recipients', 'attachments', 'replies'])
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function reply(Request $request, Message $message)
    {
        try {
            $reply = new Message([
                'subject' => "Re: {$message->subject}",
                'body' => $request->body,
                'sender_id' => auth()->id(),
                'parent_id' => $message->id,
                'message_type' => $message->message_type,
            ]);
            
    
            $reply->save();
    
            $this->processRecipients($reply, $message->recipients()
                ->where('user_id', '!=', auth()->id())
                ->get()
                ->toArray());
    
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully',
                'data' => $reply->load(['sender', 'recipients', 'attachments'])
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }

    public function forward(Request $request, Message $message)
    {
        try {
            $forwarded = new Message([
                'subject' => "Fwd: {$message->subject}",
                'body' => $message->body,
                'sender_id' => auth()->id(),
                'message_type' => $request->message_type ?? $message->message_type,
            ]);
    
            $forwarded->save();
    
    
            $this->processRecipients($forwarded, $request->recipients);
    
    
            foreach ($message->attachments as $attachment) {
                $newPath = Storage::disk('private')->copy(
                    $attachment->file_path,
                    'message-attachments/' . uniqid() . '_' . basename($attachment->file_path)
                );
    
                $forwarded->attachments()->create([
                    'file_name' => $attachment->file_name,
                    'file_path' => $newPath,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Message forwarded successfully',
                'data' => $forwarded->load(['sender', 'recipients', 'attachments'])
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }

    public function updateReadStatus(Request $request)
    {
        $message = Message::findOrFail($request->message_id);
        
        $message->recipients()->updateExistingPivot(
            auth()->id(),
            ['is_read' => $request->is_read]
        );

        return response()->json(['message' => 'Read status updated successfully']);
    }

    public function search(Request $request)
    {
        $query = Message::query()
            ->with(['sender', 'recipients', 'attachments'])
            ->whereHas('recipients', function($query) {
                $query->where('user_id', auth()->id());
            });

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('body', 'like', "%{$request->search}%");
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return response()->json($query->paginate(15));
    }

    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        $message->delete();
        
        return response()->json(['message' => 'Message moved to trash']);
    }

    public function restore(Message $message)
    {
        $this->authorize('restore', $message);
        $message->restore();
        
        return response()->json(['message' => 'Message restored successfully']);
    }

    private function processRecipients($message, $recipients)
    {
        // echo json_encode($recipients);
        $recipientType = "";
        
        foreach ($recipients as $recipient) {
            
            if(isset($recipient['type'])){
                $recipientType = $recipient['type'];
            }
            else{
                $recipientType = $recipient['pivot']['recipient_type'];
            }
            
            if ($recipientType === 'internal') {
                
                Mail::send('emails.messages.internal-message', ['messageData' => $message], function($m) use ($recipient, $message) {
                    $m->to($recipient['email']);
                    $m->subject($message->subject);
                    
                    foreach ($message->attachments as $attachment) {
                        
                         $m->attach($attachment->file_path);
                    }
                });
                
                $message->recipients()->attach($recipient['id']?? null, [
                    'recipient_type' => 'internal',
                    'email' => $recipient['email'],
                    'is_read' => false
                ]);

            } elseif ($recipientType === 'external') {

                Mail::send('emails.messages.external-message', ['messageData' => $message], function($m) use ($recipient, $message) {
                    $m->to($recipient['email']);
                    $m->subject($message->subject);
                    
                    foreach ($message->attachments as $attachment) {
                         $m->attach($attachment->file_path);
                    }
                });
                
                $message->recipients()->attach($recipient['id'] ?? null, [
                    'recipient_type' => 'external',
                    'email' => $recipient['email'],
                    'is_read' => false
                ]);
            }
        }
    }

    private function processAttachments($message, $attachments)
    {
        foreach ($attachments as $attachment) {

            // $fileSize = $attachment->getSize();
            // $originalName = $attachment->getClientOriginalName();
            // $mimeType = $attachment->getMimeType();
        
            // $path = $this->uploadFile($attachment);
            
            $originalName = basename($attachment);

            $message->attachments()->create([
                'file_name' => $originalName,
                'file_path' => $attachment,
                'file_size' => null,
                'mime_type' => null,
            ]);
        }
    }
    
    protected  function uploadFile($file, $path = '/messages-attachments')
    {
        $fileName = $file->getClientOriginalName();
        $file->move(public_path($path), $fileName);
        $fullpath = config("app.url") . "$path/$fileName";

        return $fullpath;
    }

    private function prepareRecipientsForBulkMessage(array $recipientTypes, string $messageType)
    {
    $recipients = [];

    foreach ($recipientTypes as $type) {
        switch ($type) {
            case 'client':
                $clients = Client::with('user')->get();
                foreach ($clients as $client) {
                    if ($client->user && filter_var($client->user->email, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = [
                            'id' => $client->user->id,
                            'email' => $client->user->email,
                            'type' => 'external'
                        ];
                    }
                }
                break;

            case 'staff':
                $employees = Employee::with('user')->get();
                foreach ($employees as $employee) {
                    if ($employee->user && filter_var($employee->user->email, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = [
                            'id' => $employee->user->id,
                            'email' => $employee->user->email,
                            'type' => 'internal'
                        ];
                    }
                }
                break;

            case 'all':
                $users = User::all();
                foreach ($users as $user) {
                    if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = [
                            'id' => $user->id,
                            'email' => $user->email,
                            'type' => $this->determineUserType($user)
                        ];
                    }
                }
                break;
        }
    }

    // Filter based on message type
    if ($messageType === 'internal') {
        $recipients = array_filter($recipients, function($recipient) {
            return $recipient['type'] === 'internal';
        });
    } elseif ($messageType === 'external') {
        $recipients = array_filter($recipients, function($recipient) {
            return $recipient['type'] === 'external';
        });
    }

    // Remove duplicates
    $recipients = array_map("unserialize", array_unique(array_map("serialize", $recipients)));

    return $recipients;
}

    

    private function determineUserType($user)
    {
        // Check if the user is associated with an employee
        if (Employee::where('user_id', $user->id)->exists()) {
            return 'internal';
        }
        
        // Check if the user is associated with a client
        if (Client::where('user_id', $user->id)->exists()) {
            return 'external';
        }
        
        // Default fallback
        return 'external';
    }
 
    private function processBulkRecipients(Message $message, array $recipients)
    {
        // Process recipients in chunks of 100
        $chunks = array_chunk($recipients, 100);

        foreach ($chunks as $chunk) {
            $this->processRecipients($message, $chunk);
        }
    }


    private function attachExistingAttachments(Message $message, array $attachmentIds)
    {
        foreach ($attachmentIds as $attachmentId) {
            $originalAttachment = Attachment::findOrFail($attachmentId);
            
            // Create a copy of the attachment for this message
            $newAttachment = $message->attachments()->create([
                'file_name' => $originalAttachment->file_name,
                'file_path' => $originalAttachment->file_path,
                'file_size' => $originalAttachment->file_size,
                'mime_type' => $originalAttachment->mime_type,
            ]);
        }
    }
}