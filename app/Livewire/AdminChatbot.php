<?php

namespace App\Livewire;

use App\Services\AdminChatbotService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminChatbot extends Component
{
    public array $messages = [];
    public array $context = [];
    public string $question = '';
    public array $quickQuestions = [
        'Bantuan',
        'Ringkasan penjualan minggu ini',
        'Produk terlaris bulan ini',
        'Penjualan per metode pembayaran bulan ini',
        'Sales per kasir bulan ini',
        'Selisih shift kasir bulan ini',
        'Riwayat stok bahan gula',
        'Riwayat stok produk M001',
        'Cek stok bahan Es Batu',
        'Produk stok menipis',
        'Penjualan minggu ini dibanding minggu lalu',
        'Kasir mana yang naik omzetnya bulan ini',
        'Profit per produk bulan ini',
        'Produk mana yang penjualannya turun bulan ini',
        'Stok masuk keluar bulan ini',
        'Produk akan expired 30 hari',
        'Top kategori bulan ini',
    ];

    public function mount(): void
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('admin'), 403);

        $this->messages = session('admin_chatbot_messages', []);
        $this->context = session('admin_chatbot_context', []);

        if ($this->messages === []) {
            $this->messages[] = $this->makeMessage(
                'assistant',
                'Halo! Saya bisa bantu cek stok, penjualan, profit, shift, dan analisis perbandingan. Semua jawaban diambil langsung dari database.',
                [
                    'meta' => [
                        'intent' => 'bantuan_chatbot',
                        'success' => true,
                    ],
                    'actions' => [
                        ['label' => 'Lihat Produk', 'url' => route('admin.products.index')],
                        ['label' => 'Lihat Penjualan', 'url' => route('sales_data')],
                    ],
                ]
            );
            $this->persistConversation();
        }
    }

    public function ask(): void
    {
        $this->question = trim($this->question);

        $this->validate([
            'question' => ['required', 'string', 'max:500'],
        ]);

        $this->messages[] = $this->makeMessage('user', $this->question);

        $response = app(AdminChatbotService::class)->handle(
            $this->question,
            $this->context,
            Auth::id(),
            session()->getId()
        );

        $this->context = $response['context'] ?? $this->context;

        $this->messages[] = $this->makeMessage('assistant', $response['message'], [
            'meta' => [
                'intent' => $response['intent'],
                'success' => $response['success'],
                'latency_ms' => $response['latency_ms'] ?? null,
                'insight_label' => $response['meta']['insight_label'] ?? null,
                'insight_tier' => $response['meta']['insight_tier'] ?? null,
            ],
            'actions' => $response['actions'] ?? [],
            'log_id' => $response['log_id'] ?? null,
            'feedback' => null,
        ]);

        $this->reset('question');
        $this->persistConversation();

        $this->dispatch('admin-chatbot-scroll');
    }

    public function askQuick(string $question): void
    {
        $this->question = $question;
        $this->ask();
    }

    public function clearConversation(): void
    {
        $this->messages = [];
        $this->context = [];

        session()->forget([
            'admin_chatbot_messages',
            'admin_chatbot_context',
        ]);

        $this->mount();
        $this->dispatch('admin-chatbot-scroll');
    }

    public function submitFeedback(int $messageIndex, string $feedback): void
    {
        $message = $this->messages[$messageIndex] ?? null;

        if (!$message || ($message['role'] ?? null) !== 'assistant' || empty($message['log_id'])) {
            return;
        }

        if (!empty($message['feedback'])) {
            return;
        }

        $saved = app(AdminChatbotService::class)->submitFeedback((int) $message['log_id'], $feedback);

        if (!$saved) {
            return;
        }

        $this->messages[$messageIndex]['feedback'] = $feedback;
        $this->persistConversation();
    }

    public function render()
    {
        return view('livewire.admin-chatbot');
    }

    private function makeMessage(string $role, string $text, array $extra = []): array
    {
        return array_merge([
            'role' => $role,
            'text' => $text,
            'time' => now()->format('H:i'),
        ], $extra);
    }

    private function persistConversation(): void
    {
        session()->put('admin_chatbot_messages', $this->messages);
        session()->put('admin_chatbot_context', $this->context);
    }
}
