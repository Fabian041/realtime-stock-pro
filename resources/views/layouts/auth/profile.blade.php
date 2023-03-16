@extends('layouts.master.main')

@section('content')
<!-- Content -->    
<div class="row">
    <h2><strong>Profile</strong></h2>
</div>   
<div class="row">
    <!-- User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <!-- User Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="user-avatar-section">
                    <div class=" d-flex align-items-center flex-column">
                        <img class="img-fluid rounded my-4" src={{ asset("img/avatars/10.png" ) }} height="110" width="110" alt="User avatar" />
                        <div class="user-info text-center">
                            <h4 class="mb-2">{{ auth()->user()->username }}</h4>
                            <span class="badge bg-label-secondary">Trainee</span>
                        </div>
                    </div>
                </div>
                <br>
                <h5 class="pb-2 border-bottom mb-4">Details</h5>
                <div class="info-container">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <span class="fw-bold me-2">Username:</span>
                            <span>{{ auth()->user()->username }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">Email:</span>
                            <span>{{ auth()->user()->email }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">Status:</span>
                            <span class="badge bg-label-success">Active</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /User Card -->
    </div>
    <!--/ User Sidebar -->
    <!-- User Content -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

        <!-- Content wrapper -->
        <div class="content-wrapper">
            
            <!-- Change Password -->
            <div class="card mb-4">
                <h5 class="card-header">Change Password</h5>
                <div class="card-body">

                    {{-- alert when registered --}}
                    @if (session()->has('updated'))
                        <div class="alert alert-success alert-dismissible mb-3" role="alert">
                            {{ session('updated') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                    @endif
                    {{-- end of alert --}}

                    {{-- alert when error --}}
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                    @endif
                    {{-- end of alert --}}

                    <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}" >
                        @method('POST')
                        @csrf    
                        <div class="row">
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" placeholder="Enter your email or username" value="{{ auth()->user()->email }}" autofocus>
                                    <span class="input-group-text cursor-pointer"></span>

                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="confirmPassword">Current Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control @error('current-password') is-invalid @enderror" type="password" name="current-password" id="current-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required/>
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>

                                    @error('current-password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 mb-1">
                                <div class="row">
                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="newPassword">New Password</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control @error('new-password') is-invalid @enderror" type="password" id="new-password" name="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required/>
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>

                                            @error('new-password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="confirmPassword">Confirm New Password</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control @error('new-password_confirmation') is-invalid @enderror" type="password" name="new-password_confirmation" id="confirmPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>

                                            @error('password_confirmation')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <p class="fw-semibold mt-2">Password Requirements:</p>
                                                <ul class="ps-3 mb-0">
                                                    <li class="mb-1">
                                                        Minimum 6 characters long - the more, the better
                                                    </li>
                                                    <li class="mb-1">At least one lowercase character</li>
                                                    <li>At least one number, symbol, or whitespace character</li>
                                                </ul>
                                            </div>
                                            <div class="col-12 mt-1">
                                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                                <button type="reset" class="btn btn-label-secondary">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Change Password -->
@endsection