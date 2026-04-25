<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Services\GasYemenNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        private readonly GasYemenNotificationService $notifications,
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'string', 'max:20'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'open',
        ]);

        return response()->json($ticket, 201);
    }

    public function myTickets(Request $request): JsonResponse
    {
        return response()->json(
            SupportTicket::query()->where('user_id', $request->user()->id)->latest('created_at')->get()
        );
    }

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        $query = SupportTicket::query();
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        return response()->json($query->latest('created_at')->get());
    }

    public function update(Request $request, int $ticketId): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        $data = $request->validate([
            'status' => ['nullable', 'string', 'max:20'],
            'priority' => ['nullable', 'string', 'max:20'],
            'admin_response' => ['nullable', 'string'],
        ]);

        $ticket = SupportTicket::query()->findOrFail($ticketId);
        $ticket->fill($data);
        if (in_array($ticket->status, ['resolved', 'closed'], true)) {
            $ticket->resolved_at = now();
        }
        $ticket->save();

        $this->notifications->createSystemNotification(
            $ticket->user_id,
            'تحديث على تذكرتك',
            'تذكرة رقم #'.$ticket->id.' تم تحديث حالتها إلى: '.$ticket->status,
            'support_ticket_update'
        );

        return response()->json($ticket->refresh());
    }
}
