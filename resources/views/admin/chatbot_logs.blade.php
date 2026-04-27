@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Audit Chatbot</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Audit Chatbot</li>
    </ol>
  </nav>
</div>

@include('admin.partials.flash')

<section class="section dashboard">
  <div class="row">
    <div class="col-xxl-2 col-md-4">
      <div class="card info-card sales-card">
        <div class="card-body">
          <h5 class="card-title">Total Query</h5>
          <h6>{{ number_format($summary['total_questions']) }}</h6>
          <span class="text-muted small">Pertanyaan chatbot</span>
        </div>
      </div>
    </div>

    <div class="col-xxl-2 col-md-4">
      <div class="card info-card revenue-card">
        <div class="card-body">
          <h5 class="card-title">Berhasil</h5>
          <h6>{{ number_format($summary['success_count']) }}</h6>
          <span class="text-muted small">Intent dikenali</span>
        </div>
      </div>
    </div>

    <div class="col-xxl-2 col-md-4">
      <div class="card info-card customers-card">
        <div class="card-body">
          <h5 class="card-title">Gagal</h5>
          <h6>{{ number_format($summary['failure_count']) }}</h6>
          <span class="text-muted small">Butuh evaluasi</span>
        </div>
      </div>
    </div>

    <div class="col-xxl-2 col-md-4">
      <div class="card info-card sales-card">
        <div class="card-body">
          <h5 class="card-title">Helpful</h5>
          <h6>{{ number_format($summary['helpful_count']) }}</h6>
          <span class="text-muted small">Feedback positif</span>
        </div>
      </div>
    </div>

    <div class="col-xxl-2 col-md-4">
      <div class="card info-card customers-card">
        <div class="card-body">
          <h5 class="card-title">Not Helpful</h5>
          <h6>{{ number_format($summary['not_helpful_count']) }}</h6>
          <span class="text-muted small">Feedback negatif</span>
        </div>
      </div>
    </div>

    <div class="col-xxl-2 col-md-4">
      <div class="card info-card revenue-card">
        <div class="card-body">
          <h5 class="card-title">Avg Latency</h5>
          <h6>{{ number_format($summary['avg_latency_ms']) }} ms</h6>
          <span class="text-muted small">Rata-rata respons</span>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Filter Audit Chatbot</h5>

          <form method="GET" action="{{ route('admin.chatbot.logs') }}" class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Cari</label>
              <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Pertanyaan, jawaban, session">
            </div>
            <div class="col-md-2">
              <label class="form-label">Intent</label>
              <select name="intent" class="form-select">
                <option value="">Semua</option>
                @foreach ($intents as $intent)
                  <option value="{{ $intent }}" @selected(request('intent') === $intent)>{{ $intent }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Status</label>
              <select name="success" class="form-select">
                <option value="">Semua</option>
                <option value="1" @selected(request('success') === '1')>Berhasil</option>
                <option value="0" @selected(request('success') === '0')>Gagal</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Feedback</label>
              <select name="feedback" class="form-select">
                <option value="">Semua</option>
                <option value="helpful" @selected(request('feedback') === 'helpful')>Helpful</option>
                <option value="not_helpful" @selected(request('feedback') === 'not_helpful')>Not Helpful</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Admin</label>
              <select name="user_id" class="form-select">
                <option value="">Semua Admin</option>
                @foreach ($adminUsers as $adminUser)
                  <option value="{{ $adminUser->id }}" @selected((string) request('user_id') === (string) $adminUser->id)>{{ $adminUser->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Tanggal Dari</label>
              <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label">Tanggal Sampai</label>
              <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
              <a href="{{ route('admin.chatbot.logs') }}" class="btn btn-secondary">Reset</a>
              <button type="submit" class="btn btn-primary">Filter</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Log Chatbot</h5>

          <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Waktu</th>
                  <th>Admin</th>
                  <th>Intent</th>
                  <th>Pertanyaan</th>
                  <th>Ringkasan Jawaban</th>
                  <th>Status</th>
                  <th>Feedback</th>
                  <th>Latency</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($logs as $index => $log)
                  <tr>
                    <td>{{ $logs->firstItem() + $index }}</td>
                    <td>
                      <div>{{ $log->created_at->format('d M Y H:i') }}</div>
                      <div class="small text-muted">{{ $log->session_id ?: '-' }}</div>
                    </td>
                    <td>{{ $log->user?->name ?? '-' }}</td>
                    <td><span class="badge bg-primary">{{ $log->intent }}</span></td>
                    <td style="min-width: 220px;">{{ $log->question }}</td>
                    <td style="min-width: 280px;">{{ \Illuminate\Support\Str::limit($log->response_summary, 180) }}</td>
                    <td>
                      @if ($log->success)
                        <span class="badge bg-success">Berhasil</span>
                      @else
                        <span class="badge bg-danger">Gagal</span>
                      @endif
                    </td>
                    <td>
                      @if ($log->feedback === 'helpful')
                        <span class="badge bg-success">Helpful</span>
                      @elseif ($log->feedback === 'not_helpful')
                        <span class="badge bg-danger">Not Helpful</span>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>{{ number_format($log->latency_ms) }} ms</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center text-muted py-4">Belum ada log chatbot yang sesuai filter.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if ($logs->hasPages())
            <div class="mt-3">
              {{ $logs->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
