<?php

namespace App\Livewire;

use App\Services\AdminChatbotService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminChatbot extends Component
{
    public array $messages = [];
    public string $question = '';
    public array $quickQuestions = [
        'Produk stok menipis',
        'Produk terlaris bulan ini',
        'Ringkasan penjualan minggu ini',
        'Riwayat stock movement bubuk matcha',
        'Cek stok gula',
        'Produk akan expired 30 hari',
        'Sales per kasir bulan ini',
        'Stok masuk keluar bulan ini',
    ];

    public function mount(): void
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('admin'), 403);

        $this->messages[] = $this->makeMessage(
            'assistant',
            'Halo! Saya bisa bantu cek stok, produk terlaris, ringkasan penjualan, dan lainnya. Semua jawaban diambil langsung dari database.'
        );
    }

    public function ask(): void
    {
        $this->question = trim($this->question);

        $this->validate([
            'question' => ['required', 'string', 'max:500'],
        ]);

        $this->messages[] = $this->makeMessage('user', $this->question);

        $response = app(AdminChatbotService::class)->handle($this->question);

        $this->messages[] = $this->makeMessage('assistant', $response['message'], [
            'meta' => [
                'intent' => $response['intent'],
                'success' => $response['success'],
            ],
        ]);

        $this->reset('question');

        $this->dispatch('admin-chatbot-scroll');
    }

    public function askQuick(string $question): void
    {
        $this->question = $question;
        $this->ask();
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
}
