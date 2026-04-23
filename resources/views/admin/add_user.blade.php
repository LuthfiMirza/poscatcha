@extends('layouts.admin')

@section('content')

<div class="pagetitle">
    <h1>Add User</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard_admin')}}">Home</a></li>
        <li class="breadcrumb-item active">Add User</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Add User</h5>

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('add_user_process') }}" enctype="multipart/form-data">
              @csrf
              <div class="row mb-3">
                <label for="name" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="password" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>
              </div>
                <div class="row mb-3">
                    <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

              <div class="row mb-3">
                <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary">Add User</button>
                </div>
              </div>

            </form><!-- End General Form Elements -->
        </div>
    </div>
  </div>
@endsection