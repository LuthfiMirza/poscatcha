@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Kategori</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Kategori</li>
    </ol>
  </nav>
</div>

@include('admin.partials.flash')

<section class="section dashboard">
  <div class="row">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">Master Kategori</h5>
            <a href="{{ route('add_category') }}" class="btn btn-success">
              <i class="bx bx-list-plus me-1"></i> Add Category
            </a>
          </div>

          <table class="table table-borderless datatable align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Category ID</th>
                <th>Category Name</th>
                <th>Added By</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($categories as $index => $category)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $category->category_id }}</td>
                  <td>{{ $category->category_name }}</td>
                  <td>{{ $category->added_by ?? '-' }}</td>
                  <td>
                    <a href="{{ route('edit_category', $category->id) }}" class="btn badge bg-warning">Edit</a>
                    <a href="{{ route('delete_category', $category->id) }}" class="btn badge bg-danger">Delete</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
