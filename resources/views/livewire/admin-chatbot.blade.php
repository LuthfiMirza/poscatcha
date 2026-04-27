<div id="admin-chatbot-root" class="admin-chatbot-root" aria-live="polite">
  <div id="admin-chatbot-overlay" class="admin-chatbot-overlay" aria-hidden="true"></div>

  <aside id="admin-chatbot-panel" class="admin-chatbot-panel" aria-hidden="true">
    <div class="admin-chatbot-panel__header">
      <div class="admin-chatbot-panel__identity">
        <div class="admin-chatbot-panel__avatar">AI</div>
        <div>
          <div class="admin-chatbot-panel__title">Admin Chatbot</div>
          <div class="admin-chatbot-panel__subtitle">Read-only · Konteks percakapan aktif · Data dari database</div>
        </div>
      </div>

      <div class="admin-chatbot-panel__header-actions">
        <button type="button" class="admin-chatbot-panel__ghost" wire:click="clearConversation">
          Reset
        </button>
        <button type="button" id="admin-chatbot-panel-close" class="admin-chatbot-panel__close" aria-label="Tutup Admin Chatbot">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    <div id="admin-chatbot-history" class="admin-chatbot-panel__messages">
      @foreach ($messages as $message)
        @php
          $intent = $message['meta']['intent'] ?? null;
          $success = $message['meta']['success'] ?? null;
          $latency = $message['meta']['latency_ms'] ?? null;
          $insightLabel = $message['meta']['insight_label'] ?? null;
          $insightTier = $message['meta']['insight_tier'] ?? null;
          $feedback = $message['feedback'] ?? null;
        @endphp

        <div class="admin-chatbot-message admin-chatbot-message--{{ $message['role'] }}" wire:key="admin-chatbot-message-{{ $loop->index }}">
          @if ($message['role'] === 'assistant' && $intent)
            <div class="admin-chatbot-message__meta">
              @if ($insightLabel)
                <span class="admin-chatbot-badge admin-chatbot-badge--insight {{ $insightTier === 'primary' ? 'is-primary' : '' }}">{{ $insightLabel }}</span>
              @endif
              <span class="admin-chatbot-badge">{{ str_replace('_', ' ', $intent) }}</span>
              @if ($success === true)
                <span class="admin-chatbot-badge admin-chatbot-badge--success">ok</span>
              @elseif ($success === false)
                <span class="admin-chatbot-badge admin-chatbot-badge--warning">cek</span>
              @endif
              @if ($latency !== null)
                <span class="admin-chatbot-badge admin-chatbot-badge--muted">{{ $latency }} ms</span>
              @endif
            </div>
          @endif

          <div class="admin-chatbot-message__bubble">
            {{ $message['text'] }}

            @if (($message['actions'] ?? []) !== [])
              <div class="admin-chatbot-actions">
                @foreach ($message['actions'] as $action)
                  <a href="{{ $action['url'] }}" class="admin-chatbot-action" @if (($action['url'] ?? '#') === '#') aria-disabled="true" @endif>
                    {{ $action['label'] }}
                  </a>
                @endforeach
              </div>
            @endif

            @if (($message['role'] ?? null) === 'assistant' && !empty($message['log_id']))
              <div class="admin-chatbot-feedback">
                <button
                  type="button"
                  class="admin-chatbot-feedback__button {{ $feedback === 'helpful' ? 'is-active' : '' }}"
                  wire:click="submitFeedback({{ $loop->index }}, 'helpful')"
                  @disabled(!empty($feedback))
                >
                  Membantu
                </button>
                <button
                  type="button"
                  class="admin-chatbot-feedback__button {{ $feedback === 'not_helpful' ? 'is-active is-negative' : '' }}"
                  wire:click="submitFeedback({{ $loop->index }}, 'not_helpful')"
                  @disabled(!empty($feedback))
                >
                  Tidak membantu
                </button>
              </div>
            @endif
          </div>

          <div class="admin-chatbot-message__time">{{ $message['time'] ?? now()->format('H:i') }}</div>
        </div>
      @endforeach
    </div>

    <div class="admin-chatbot-panel__composer">
      <div class="admin-chatbot-chips" aria-label="Quick suggestions">
        @foreach (array_slice($quickQuestions, 0, 8) as $quickQuestion)
          <button
            type="button"
            class="admin-chatbot-chip"
            wire:click="askQuick('{{ $quickQuestion }}')"
          >
            {{ $quickQuestion }}
          </button>
        @endforeach
      </div>

      <form wire:submit.prevent="ask" class="admin-chatbot-inputbar">
        <input
          type="text"
          class="admin-chatbot-inputbar__field"
          placeholder="Ketik pertanyaan..."
          wire:model.defer="question"
        >
        <button type="submit" class="admin-chatbot-inputbar__send" aria-label="Kirim pertanyaan">
          <i class="bi bi-send-fill"></i>
        </button>
      </form>

      @error('question')
        <div class="admin-chatbot-inputbar__error">{{ $message }}</div>
      @enderror

      <div class="admin-chatbot-panel__hint">
        Contoh: <span>"cek stok gula"</span>, <span>"penjualan minggu ini dibanding minggu lalu"</span>, <span>"kasir mana yang naik omzetnya bulan ini"</span>
      </div>
    </div>
  </aside>

  <button
    type="button"
    id="admin-chatbot-toggle"
    class="admin-chatbot-toggle"
    aria-label="Buka Admin Chatbot"
    data-tooltip="Admin Chatbot"
  >
    <span class="admin-chatbot-toggle__icon admin-chatbot-toggle__icon--chat">
      <i class="bi bi-chat-dots-fill"></i>
    </span>
    <span class="admin-chatbot-toggle__icon admin-chatbot-toggle__icon--close d-none">
      <i class="bi bi-x-lg"></i>
    </span>
  </button>

  @once
    <style>
      .admin-chatbot-root {
        position: relative;
        z-index: 1090;
      }

      .admin-chatbot-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.25);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 250ms ease-out, visibility 250ms ease-out;
        z-index: 1091;
      }

      .admin-chatbot-panel {
        position: fixed;
        top: 0;
        right: 0;
        width: 400px;
        max-width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
        background: #f7f9fc;
        border-left: 1px solid rgba(1, 41, 112, 0.08);
        box-shadow: -18px 0 48px rgba(17, 24, 39, 0.16);
        transform: translateX(100%);
        transition: transform 250ms ease-out;
        z-index: 1092;
      }

      .admin-chatbot-panel__header {
        min-height: 68px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #ffffff;
        border-bottom: 1px solid rgba(1, 41, 112, 0.08);
        flex-shrink: 0;
      }

      .admin-chatbot-panel__identity {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
      }

      .admin-chatbot-panel__avatar {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #012970, #0d6efd);
        color: #fff;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        flex-shrink: 0;
      }

      .admin-chatbot-panel__title {
        color: #012970;
        font-weight: 700;
        line-height: 1.15;
      }

      .admin-chatbot-panel__subtitle {
        color: #6c757d;
        font-size: 0.76rem;
        line-height: 1.25;
      }

      .admin-chatbot-panel__header-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }

      .admin-chatbot-panel__ghost,
      .admin-chatbot-panel__close {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: #52627d;
        transition: background-color 160ms ease, color 160ms ease;
      }

      .admin-chatbot-panel__ghost {
        width: auto;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
      }

      .admin-chatbot-panel__ghost:hover,
      .admin-chatbot-panel__close:hover {
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
      }

      .admin-chatbot-panel__messages {
        flex: 1 1 auto;
        min-height: 0;
        padding: 18px 16px;
        overflow-y: auto;
        background: #f7f9fc;
      }

      .admin-chatbot-message {
        display: flex;
        flex-direction: column;
        margin-bottom: 18px;
      }

      .admin-chatbot-message--assistant {
        align-items: flex-start;
      }

      .admin-chatbot-message--user {
        align-items: flex-end;
      }

      .admin-chatbot-message__meta {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
      }

      .admin-chatbot-badge {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        padding: 0 8px;
        border-radius: 999px;
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        font-size: 10px;
        line-height: 1;
        text-transform: uppercase;
      }

      .admin-chatbot-badge--success {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
      }

      .admin-chatbot-badge--warning {
        background: rgba(255, 193, 7, 0.18);
        color: #9a6c00;
      }

      .admin-chatbot-badge--muted {
        background: rgba(148, 163, 184, 0.18);
        color: #52627d;
      }

      .admin-chatbot-badge--insight {
        background: rgba(232, 101, 10, 0.12);
        color: #b45309;
      }

      .admin-chatbot-badge--insight.is-primary {
        background: rgba(232, 101, 10, 0.16);
        color: #9a3412;
      }

      .admin-chatbot-message__bubble {
        max-width: 92%;
        padding: 12px 14px;
        font-size: 0.92rem;
        line-height: 1.55;
        word-break: break-word;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
      }

      .admin-chatbot-message--assistant .admin-chatbot-message__bubble {
        background: #ffffff;
        color: #1f2937;
        border-radius: 4px 12px 12px 12px;
      }

      .admin-chatbot-message--user .admin-chatbot-message__bubble {
        background: #0d6efd;
        color: #ffffff;
        border-radius: 12px 4px 12px 12px;
      }

      .admin-chatbot-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
      }

      .admin-chatbot-action {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 10px;
        border: 1px solid rgba(13, 110, 253, 0.18);
        border-radius: 999px;
        background: rgba(13, 110, 253, 0.05);
        color: #0d6efd;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
      }

      .admin-chatbot-action:hover {
        background: rgba(13, 110, 253, 0.12);
        color: #0b5ed7;
      }

      .admin-chatbot-feedback {
        display: flex;
        gap: 8px;
        margin-top: 10px;
      }

      .admin-chatbot-feedback__button {
        min-height: 28px;
        padding: 0 10px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 999px;
        background: #ffffff;
        color: #52627d;
        font-size: 11px;
        font-weight: 600;
      }

      .admin-chatbot-feedback__button.is-active {
        background: rgba(25, 135, 84, 0.12);
        border-color: rgba(25, 135, 84, 0.2);
        color: #198754;
      }

      .admin-chatbot-feedback__button.is-active.is-negative {
        background: rgba(220, 53, 69, 0.12);
        border-color: rgba(220, 53, 69, 0.2);
        color: #dc3545;
      }

      .admin-chatbot-feedback__button:disabled {
        cursor: default;
        opacity: 0.95;
      }

      .admin-chatbot-message__time {
        margin-top: 6px;
        color: #8c98ad;
        font-size: 0.7rem;
      }

      .admin-chatbot-panel__composer {
        flex-shrink: 0;
        background: #ffffff;
        border-top: 1px solid rgba(1, 41, 112, 0.08);
        padding: 10px 12px 12px;
      }

      .admin-chatbot-chips {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 10px;
        scrollbar-width: none;
      }

      .admin-chatbot-chips::-webkit-scrollbar {
        display: none;
      }

      .admin-chatbot-chip {
        flex: 0 0 auto;
        padding: 6px 12px;
        border: 1px solid rgba(1, 41, 112, 0.14);
        border-radius: 999px;
        background: transparent;
        color: #52627d;
        font-size: 12px;
        line-height: 1.2;
        transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease;
      }

      .admin-chatbot-chip:hover {
        background: rgba(13, 110, 253, 0.08);
        border-color: rgba(13, 110, 253, 0.25);
        color: #0d6efd;
      }

      .admin-chatbot-inputbar {
        min-height: 64px;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .admin-chatbot-inputbar__field {
        flex: 1 1 auto;
        width: 100%;
        height: 40px;
        border: 1px solid rgba(1, 41, 112, 0.14);
        border-radius: 999px;
        padding: 0 14px;
        background: #ffffff;
        color: #1f2937;
        outline: none;
        transition: border-color 160ms ease, box-shadow 160ms ease;
      }

      .admin-chatbot-inputbar__field:focus {
        border-color: rgba(13, 110, 253, 0.5);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
      }

      .admin-chatbot-inputbar__field::placeholder {
        color: #9aa6b2;
      }

      .admin-chatbot-inputbar__send {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #0d6efd;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.22);
        transition: background-color 160ms ease, transform 160ms ease;
      }

      .admin-chatbot-inputbar__send:hover {
        background: #0b5ed7;
        transform: translateY(-1px);
      }

      .admin-chatbot-inputbar__error {
        padding-left: 4px;
        color: #dc3545;
        font-size: 0.76rem;
      }

      .admin-chatbot-panel__hint {
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.45;
      }

      .admin-chatbot-panel__hint span {
        color: #52627d;
      }

      .admin-chatbot-toggle {
        position: fixed;
        right: 24px;
        bottom: 24px;
        width: 52px;
        height: 52px;
        border: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #012970, #0d6efd);
        color: #ffffff;
        box-shadow: 0 16px 34px rgba(13, 110, 253, 0.32);
        z-index: 1093;
        transition: transform 250ms ease-out, box-shadow 160ms ease, background-color 160ms ease;
        animation: admin-chatbot-pulse 2.4s ease-in-out infinite;
      }

      body .back-to-top {
        right: 30px;
        bottom: 88px;
        z-index: 1088;
      }

      .admin-chatbot-toggle:hover {
        box-shadow: 0 20px 40px rgba(13, 110, 253, 0.38);
      }

      .admin-chatbot-toggle::after {
        content: attr(data-tooltip);
        position: absolute;
        right: 64px;
        top: 50%;
        transform: translateY(-50%) translateX(6px);
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.9);
        color: #ffffff;
        font-size: 12px;
        line-height: 1;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 160ms ease, transform 160ms ease;
      }

      .admin-chatbot-toggle:hover::after {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
      }

      .admin-chatbot-toggle__icon {
        font-size: 1.2rem;
        line-height: 1;
      }

      body.admin-chatbot-open .admin-chatbot-overlay {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
      }

      body.admin-chatbot-open .admin-chatbot-panel {
        transform: translateX(0);
      }

      body.admin-chatbot-open .admin-chatbot-toggle {
        transform: translateX(calc(-400px - 16px));
        animation: none;
      }

      body.admin-chatbot-open .admin-chatbot-toggle__icon--chat {
        display: none !important;
      }

      body.admin-chatbot-open .admin-chatbot-toggle__icon--close {
        display: inline-flex !important;
      }

      @keyframes admin-chatbot-pulse {
        0%, 100% {
          box-shadow: 0 16px 34px rgba(13, 110, 253, 0.32), 0 0 0 0 rgba(13, 110, 253, 0.18);
        }
        50% {
          box-shadow: 0 16px 34px rgba(13, 110, 253, 0.34), 0 0 0 10px rgba(13, 110, 253, 0);
        }
      }

      @media (max-width: 576px) {
        .admin-chatbot-panel {
          width: 100vw;
        }

        body.admin-chatbot-open .admin-chatbot-toggle {
          transform: none;
          right: 16px;
          bottom: 16px;
        }

        .admin-chatbot-toggle {
          right: 16px;
          bottom: 16px;
        }

        body .back-to-top {
          right: 16px;
          bottom: 80px;
        }

        .admin-chatbot-toggle::after {
          display: none;
        }
      }
    </style>

    <script>
      document.addEventListener('livewire:init', function () {
        const storageKey = 'admin-chatbot-open';

        if (window.__adminChatbotBound === true) {
          return;
        }

        window.__adminChatbotBound = true;

        const getElements = () => ({
          toggle: document.getElementById('admin-chatbot-toggle'),
          overlay: document.getElementById('admin-chatbot-overlay'),
          panelClose: document.getElementById('admin-chatbot-panel-close'),
          panel: document.getElementById('admin-chatbot-panel'),
          history: document.getElementById('admin-chatbot-history'),
        });

        const scrollToBottom = () => {
          const { history } = getElements();

          if (history) {
            history.scrollTop = history.scrollHeight;
          }
        };

        const syncState = (isOpen) => {
          localStorage.setItem(storageKey, isOpen ? 'true' : 'false');
          document.body.classList.toggle('admin-chatbot-open', isOpen);

          const { toggle, overlay, panel } = getElements();

          if (toggle) {
            toggle.setAttribute('aria-label', isOpen ? 'Tutup Admin Chatbot' : 'Buka Admin Chatbot');
            toggle.setAttribute('data-tooltip', isOpen ? 'Tutup' : 'Admin Chatbot');
          }

          if (panel) {
            panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
          }

          if (overlay) {
            overlay.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
          }

          if (isOpen) {
            requestAnimationFrame(() => {
              requestAnimationFrame(scrollToBottom);
            });
          }
        };

        const toggleState = () => {
          syncState(!document.body.classList.contains('admin-chatbot-open'));
        };

        const closePanel = () => syncState(false);

        document.addEventListener('click', function (event) {
          if (event.target.closest('#admin-chatbot-toggle')) {
            toggleState();
            return;
          }

          if (event.target.closest('#admin-chatbot-overlay') || event.target.closest('#admin-chatbot-panel-close')) {
            closePanel();
          }
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && document.body.classList.contains('admin-chatbot-open')) {
            closePanel();
          }
        });

        syncState(localStorage.getItem(storageKey) === 'true');

        Livewire.on('admin-chatbot-scroll', function () {
          syncState(true);
          setTimeout(scrollToBottom, 30);
        });
      });
    </script>
  @endonce
</div>
