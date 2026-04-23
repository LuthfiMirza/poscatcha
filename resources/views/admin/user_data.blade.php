@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>User Data</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="">Home</a></li>
      <li class="breadcrumb-item active">User Data</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <!-- Recent Sales -->
        <div class="col-12">
          <div class="card recent-sales overflow-auto">

            <div class="filter">
              <a class="icon" href="{{ route('add_user')}}"><button type="button" class="btn btn-success"><i class="bx bx-list-plus me-1"></i> Add User</button></a>
            </div>

            <div class="card-body">
              <h5 class="card-title">User Data</h5>

              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Added</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $index => $user)
                    <tr>
                        <th scope="row"><a href="#">{{ $index + 1 }}</a></th>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ date('d F Y', strtotime($user->created_at)) }}</td>
                        <td>
                        <a href="{{ route('edit_user', $user->id)}}" class="btn badge bg-warning">Edit</a>
                        <a href="{{ route('delete_user', $user->id)}}" class="btn badge bg-danger">Delete</a>
                        </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- End Recent Sales -->
    </div>
</section>
@endsection
