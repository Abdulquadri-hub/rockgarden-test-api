<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Announcement;
use App\Http\Requests\AnnouncementRequest;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    public function index()
    {
        try {
            $announcements = Announcement::with(['author', 'attachments'])
                ->where('start_date', '<=', now())
                ->where(function($query) {
                    $query->where('end_date', '>=', now())
                          ->orWhereNull('end_date');
                })
                ->where('status', 'active')
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $announcements
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }

    public function store(Request $request)
    {
        try {
            
            
       $validated = $request->validate([
           'title' => 'required|string|max:255',
           'content' => 'required|string',
           'start_date' => 'required|date|after_or_equal:today',
           'end_date' => 'nullable|date|after:start_date',
           'priority' => 'required|in:low,normal,high,urgent',
           'status' => 'required|in:draft,active,archived',
           'attachments.*' => 'nullable|file|max:10240',
        ]);
        
            $announcement = new Announcement([
                'title' => $request->title,
                'content' => $request->content,
                'author_id' => auth()->id(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority ?? 'normal',
                'status' => 'active'
            ]);

            $announcement->save();

            if ($request->hasFile('attachments')) {
                $this->processAttachments($announcement, $request->file('attachments'));
            }
            
            if ($announcement->status === 'active') {
                $this->notifyUsers($announcement);
            }

            return response()->json([
                "success" => true,
                'message' => 'Announcement created successfully',
                'data' => $announcement->load(['author', 'attachments'])
            ], 201);
        }catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                "success" => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }

    private function processAttachments($announcement, $attachments)
    {
        foreach ($attachments as $attachment) {
            $path = $attachment->store('announcement-attachments', 'public');

            $announcement->attachments()->create([
                'file_name' => $attachment->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
            ]);
        }
    }
    
    private function notifyUsers(Announcement $announcement)
    {
        $users = User::whereHas('employee')->get();
        
        Notification::send($users, new NewAnnouncementNotification($announcement));
    }

    public function markAsRead(Announcement $announcement)
    {
        try {
            auth()->user()->notifications()
                ->where('type', NewAnnouncementNotification::class)
                ->where('data->announcement_id', $announcement->id)
                ->update(['read_at' => now()]);

            return response()->json([
                "success" => true,
                'message' => 'Announcement marked as read'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function unreadCount()
    {
        try {
            $count = auth()->user()->unreadNotifications()
                ->where('type', NewAnnouncementNotification::class)
                ->count();

            return response()->json([
                'count' => $count
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => $th->getMessage()
            ], 422);
        }
    }
    
    public function getUnread()
    {
        try {
            $unreadNotification = auth()->user()->unreadNotifications()
                ->where('type', NewAnnouncementNotification::class);

            return response()->json([
                "success" => true,
                 'message' => 'Unread Announcement',
                'data' => $unreadNotification
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Announcement $announcement)
    {
        
        try {
            
        $validated = $request->validate([
           'title' => 'required|string|max:255',
           'content' => 'required|string',
           'start_date' => 'required|date|after_or_equal:today',
           'end_date' => 'nullable|date|after:start_date',
           'priority' => 'required|in:low,normal,high,urgent',
           'status' => 'required|in:draft,active,archived',
           'attachments.*' => 'nullable|file|max:10240',
        ]);
        
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority,
                'status' => $request->status
            ]);

            if ($request->hasFile('attachments')) {
                $this->processAttachments($announcement, $request->file('attachments'));
            }

            return response()->json([
                "success" => true,
                'message' => 'Announcement updated successfully',
                'data' => $announcement->load(['author', 'attachments'])
            ], 200);
        }catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                "success" => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 422);
        }
    }

    public function destroy(Announcement $announcement)
    {
        try {
            $announcement->delete();
            return response()->json([
                "success" => true,
                'message' => 'Announcement deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'message' => $th->getMessage()
            ], 422);
        }
    }
}