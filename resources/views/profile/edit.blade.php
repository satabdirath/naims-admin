@extends('layouts.app')

@section('content')
    <!-- Add container and row for proper layout -->
    <div class="container-fluid">
    <div class="container py-4">
        <div class="row">
            <!-- Add a column for the profile content and adjust its margin -->
            <div class="col-12 col-md-8 offset-md-2">
                <!-- Profile Heading -->
                <div class="mb-4">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Profile') }}
                    </h2>
                </div>

                <!-- Update Profile Information Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete User Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


