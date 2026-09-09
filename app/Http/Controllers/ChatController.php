<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The customer assistant.
 *
 * Answers are composed here rather than in the browser so the bot can speak
 * about the signed-in customer's own reservations, and so a conversation is
 * the same whether it is opened on a phone or a laptop. A visitor who is not
 * signed in still gets answers; nothing about their chat is stored.
 */
class ChatController extends Controller
{
    public function history(Request $request): JsonResponse
    {
        $customer = $this->customer($request);

        if (! $customer) {
            return response()->json(['data' => [], 'stored' => false]);
        }

        $messages = ChatMessage::where('user_id', $customer->id)
            ->orderBy('id')
            ->get()
            ->map(fn (ChatMessage $m) => [
                'author' => $m->author,
                'body' => $m->body,
                'quick_replies' => $m->quick_replies ?? [],
            ]);

        return response()->json(['data' => $messages, 'stored' => true]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $customer = $this->customer($request);
        $reply = $this->answer($data['message'], $customer);

        if ($customer) {
            ChatMessage::create([
                'user_id' => $customer->id,
                'author' => ChatMessage::AUTHOR_CUSTOMER,
                'body' => $data['message'],
            ]);

            ChatMessage::create([
                'user_id' => $customer->id,
                'author' => ChatMessage::AUTHOR_BOT,
                'body' => $reply['body'],
                'quick_replies' => $reply['quick_replies'],
            ]);

            $this->trim($customer);
        }

        return response()->json($reply + ['stored' => (bool) $customer]);
    }

    public function clear(Request $request): JsonResponse
    {
        $customer = $this->customer($request);

        if ($customer) {
            ChatMessage::where('user_id', $customer->id)->delete();
        }

        return response()->json(['cleared' => true]);
    }

    /**
     * The signed-in customer, or null for a visitor.
     *
     * The browser carries a session and the app carries a Sanctum token, so
     * both are accepted and the conversation is the same account either way.
     */
    private function customer(Request $request): ?User
    {
        // Named explicitly: the chat routes are open to visitors, so there is
        // no auth middleware to resolve a bearer token for us.
        $viaToken = $request->user('sanctum');
        if ($viaToken && $viaToken->role === 'customer') {
            return $viaToken;
        }

        // API routes are stateless, so there may be no session to consult.
        if (! $request->hasSession()) {
            return null;
        }

        $id = $request->session()->get('customer_user_id');

        return $id ? User::find($id) : null;
    }

    /** Drops the oldest messages once a conversation gets long. */
    private function trim(User $customer): void
    {
        $keepFrom = ChatMessage::where('user_id', $customer->id)
            ->orderByDesc('id')
            ->skip(ChatMessage::KEEP_PER_USER)
            ->value('id');

        if ($keepFrom) {
            ChatMessage::where('user_id', $customer->id)->where('id', '<=', $keepFrom)->delete();
        }
    }

    /**
     * Works out a reply. Deliberately keyword driven rather than a language
     * model: the answers are about this shop's menu and this customer's
     * reservations, and they have to be right.
     *
     * @return array{body: string, quick_replies: array<int, string>}
     */
    private function answer(string $message, ?User $customer): array
    {
        $text = mb_strtolower(trim($message));
        $name = $customer ? explode(' ', $customer->name)[0] : 'there';
        $has = fn (string ...$needles) => collect($needles)->contains(fn ($n) => str_contains($text, $n));

        if ($has('my order', 'my reservation', 'my booking', 'track', 'status', 'ready')) {
            return $this->reservationAnswer($customer);
        }

        if ($has('menu', 'drinks', 'what do you have', 'available')) {
            return $this->menuAnswer();
        }

        if ($has('price', 'how much', 'cost', 'magkano')) {
            return $this->priceAnswer();
        }

        if ($has('take out', 'takeout', 'take-out', 'cup fee', 'surcharge')) {
            $fee = number_format((float) config('queenscup.takeout_fee_per_cup', 5.00), 2);

            return [
                'body' => "Take out adds ₱{$fee} per cup for the cup and lid. Dine in has no extra charge.",
                'quick_replies' => ['See the menu', 'How do I reserve?'],
            ];
        }

        if ($has('reserve', 'how do i order', 'book')) {
            return [
                'body' => 'Pick your drinks from the menu, choose dine in or take out, and confirm. '
                    .'You get a reference code — show it at the counter, pay there, and collect.',
                'quick_replies' => ['See the menu', 'Where are you?', 'My reservations'],
            ];
        }

        if ($has('where', 'branch', 'location', 'address', 'saan')) {
            return [
                'body' => 'We have two branches in Madridejos, Cebu:<br><br>'
                    .'<strong>Kota Park</strong> — beside the boardwalk<br>'
                    .'<strong>MCC</strong> — inside Madridejos Community College',
                'quick_replies' => ['Opening hours', 'See the menu'],
            ];
        }

        if ($has('hour', 'open', 'close', 'time')) {
            return [
                'body' => 'Both branches are open <strong>10:00 AM to 9:00 PM</strong>, daily.',
                'quick_replies' => ['Where are you?', 'See the menu'],
            ];
        }

        if ($has('pay', 'gcash', 'maya', 'cash')) {
            return [
                'body' => 'Pay at the counter when you collect: cash, GCash or PayMaya. '
                    .'Nothing is charged when you reserve.',
                'quick_replies' => ['How do I reserve?', 'See the menu'],
            ];
        }

        if ($has('hello', 'hi ', 'hey', 'kumusta', 'good morning', 'good afternoon') || $text === 'hi') {
            return [
                'body' => "Hello, {$name}! 👑 Welcome to The Queen's Cup. What can I help you with?",
                'quick_replies' => ['See the menu', 'How do I reserve?', 'My reservations', 'Where are you?'],
            ];
        }

        if ($has('thank', 'salamat')) {
            return [
                'body' => "Anytime, {$name}! Enjoy your drink 🧋",
                'quick_replies' => ['See the menu'],
            ];
        }

        return [
            'body' => "I can help with the menu, prices, reserving, our branches, and tracking your reservations. What would you like?",
            'quick_replies' => ['See the menu', 'How do I reserve?', 'My reservations', 'Opening hours'],
        ];
    }

    private function menuAnswer(): array
    {
        $byCategory = Inventory::where('stock', '>', 0)
            ->orderBy('category')
            ->get()
            ->groupBy(fn (Inventory $i) => $i->category ?: 'Other');

        if ($byCategory->isEmpty()) {
            return ['body' => 'The menu is being updated right now — please check back shortly.', 'quick_replies' => []];
        }

        $lines = $byCategory->map(fn ($items, $category) => '<strong>'.e($category).'</strong> ('.$items->count().')')
            ->implode('<br>');

        return [
            'body' => 'We have '.$byCategory->flatten()->count()." drinks available 🧋<br><br>{$lines}",
            'quick_replies' => ['Prices', 'How do I reserve?', 'Take-out fee'],
        ];
    }

    private function priceAnswer(): array
    {
        $drinks = Inventory::where('stock', '>', 0)->orderBy('regular_price')->limit(6)->get();

        if ($drinks->isEmpty()) {
            return ['body' => 'Prices are being updated — please check back shortly.', 'quick_replies' => []];
        }

        $lines = $drinks->map(function (Inventory $d) {
            $regular = '₱'.number_format((float) $d->regular_price, 0);
            $large = (float) $d->large_price > 0 ? ' / ₱'.number_format((float) $d->large_price, 0).' (22oz)' : '';

            return e($d->name)." — {$regular} (16oz){$large}";
        })->implode('<br>');

        return [
            'body' => "Some of what we pour:<br><br>{$lines}",
            'quick_replies' => ['See the menu', 'How do I reserve?'],
        ];
    }

    private function reservationAnswer(?User $customer): array
    {
        if (! $customer) {
            return [
                'body' => 'Sign in and I can tell you exactly where your reservation is up to.',
                'quick_replies' => ['See the menu', 'How do I reserve?'],
            ];
        }

        $live = Reservation::where('user_id', $customer->id)
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_PREPARING, Reservation::STATUS_READY])
            ->orderByDesc('id')
            ->with('items')
            ->get();

        if ($live->isEmpty()) {
            return [
                'body' => "You have nothing waiting at the moment. Fancy something?",
                'quick_replies' => ['See the menu', 'How do I reserve?'],
            ];
        }

        $said = [
            Reservation::STATUS_PENDING => 'received, not started yet',
            Reservation::STATUS_PREPARING => 'being made now',
            Reservation::STATUS_READY => 'ready for pick up',
        ];

        $lines = $live->map(function (Reservation $r) use ($said) {
            $cups = $r->items->sum('quantity');

            return '<strong>'.e($r->reference).'</strong> — '.$said[$r->status]
                .' · '.$cups.($cups === 1 ? ' cup' : ' cups')
                .' · ₱'.number_format((float) $r->total, 2);
        })->implode('<br>');

        return [
            'body' => "Here is where things stand:<br><br>{$lines}",
            'quick_replies' => ['See the menu', 'Where are you?'],
        ];
    }
}
