<div id="navbar" class="sticky">
    <navbar logout-route="{{ route('auth.logout') }}"
            logo="{{ asset('images/logo.svg') }}"
            default-profile-image="{{ asset('images/user/profile/profile_default.svg') }}"
            user-profile-image="{{ $userProfileImage }}"
            name="{{ $name }}"
    ></navbar>
</div>
