@extends('layouts.app')
@section('title', 'My Profile Settings')

@section('content')
<div class="space-y-6">

    {{-- Profile Information Card --}}
    <div class="premium-card rounded-2xl p-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-700"></div>
        <div class="max-w-xl">
            <h3 class="text-base font-bold font-title text-white mb-2">Update Profile Information</h3>
            <p class="text-xs text-slate-400 mb-6">Update your account name and email address details.</p>
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Update Password Card --}}
    <div class="premium-card rounded-2xl p-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold-500 to-gold-700"></div>
        <div class="max-w-xl">
            <h3 class="text-base font-bold font-title text-white mb-2">Change Account Password</h3>
            <p class="text-xs text-slate-400 mb-6">Ensure your account is using a long, random password to stay secure.</p>
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Delete Account Card --}}
    <div class="premium-card rounded-2xl p-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-brand-rose"></div>
        <div class="max-w-xl">
            <h3 class="text-base font-bold font-title text-white mb-2">Delete Chama Account</h3>
            <p class="text-xs text-slate-400 mb-6">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
