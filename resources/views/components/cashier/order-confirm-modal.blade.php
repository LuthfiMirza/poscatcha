@props([
    'id',
    'form',
    'icon' => 'bi-exclamation-circle',
    'title',
    'message',
    'confirmLabel' => 'Ya, Lanjutkan',
    'danger' => false,
])

<div class="modal fade online-order-confirm-modal" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="online-order-confirm-modal__body">
        <div class="online-order-confirm-modal__icon {{ $danger ? 'text-danger bg-danger-subtle' : '' }}">
          <i class="bi {{ $icon }}"></i>
        </div>
        <h2 class="online-order-confirm-modal__title" id="{{ $id }}Label">{{ $title }}</h2>
        <p class="online-order-confirm-modal__text">{{ $message }}</p>
        <div class="online-order-confirm-modal__actions">
          <button type="button" class="online-order-confirm-modal__btn online-order-confirm-modal__btn--cancel" data-bs-dismiss="modal">Batal</button>
          <button type="submit" form="{{ $form }}" class="online-order-confirm-modal__btn online-order-confirm-modal__btn--confirm {{ $danger ? 'is-danger' : '' }}">{{ $confirmLabel }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
