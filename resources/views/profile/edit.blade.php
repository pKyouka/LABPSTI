<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-neutral-800">{{ __('Profile') }}</h2>
    </x-slot>

    <div class="space-y-6 max-w-xl">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            @include('profile.partials.update-password-form')
        </div>
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>