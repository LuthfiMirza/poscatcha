@extends('layouts.admin')

@section('content')

<div class="pagetitle">
    <h1>Add Product</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard_admin')}}">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Add Category Form</h5>

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('add_category_process') }}">
              @csrf
              <div class="row mb-3">
                <label for="category_id" class="col-sm-2 col-form-label">Category ID</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="category_id" name="category_id" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="category_name" class="col-sm-2 col-form-label">Category Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="category_name" name="category_name" required>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
              </div>

            </form><!-- End General Form Elements -->
        </div>
    </div>
  </div>
@endsection