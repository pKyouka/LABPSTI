<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function feed(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        $base = DB::table('notifications')
            ->where('notifiable_type', $user::class)
            ->where('notifiable_id', $user->id);

        $unreadCount = (clone $base)->whereNull('read_at')->count();

        $rows = (clone $base)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $items = $rows->map(function ($row) use ($isAdmin) {
            $data = is_string($row->data) ? json_decode($row->data, true) : (array) $row->data;
            $data = is_array($data) ? $data : [];

            [$title, $subtitle] = $this->present($data, $isAdmin);

            return [
                'id' => $row->id,
                'title' => $title,
                'subtitle' => $subtitle,
                'is_unread' => $row->read_at === null,
                'created_at' => Carbon::parse($row->created_at)->diffForHumans(),
                'read_url' => route('notifications.read', $row->id),
            ];
        })->values();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function present(array $data, bool $isAdmin): array
    {
        $type = (string) ($data['type'] ?? '');

        $title = $data['title']
            ?? $data['status_label']
            ?? ($type === 'vm_borrow_request_received' ? ('VM Baru dari '.($data['user_name'] ?? '')) : null)
            ?? ($type === 'reset_request_received' ? ('Reset VM oleh '.($data['user_name'] ?? '')) : null)
            ?? ($data['os_info'] ?? null)
            ?? ($isAdmin
                ? (! empty($data['requester_name']) ? 'Permintaan baru dari '.$data['requester_name'] : 'Notifikasi baru')
                : 'Notifikasi');

        $subtitle = $data['category_label']
            ?? $data['reset_reason']
            ?? $data['status']
            ?? '';

        return [(string) $title, (string) $subtitle];
    }
}
