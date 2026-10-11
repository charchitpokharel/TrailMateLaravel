
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            TrailMate Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg">
                <h3 class="text-lg font-bold">
                    Welcome, {{ auth()->user()->name }}!
                </h3>
                <p class="mt-2">Administrator access confirmed.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Trail Management</h4>
                        <p>Manage hiking trails.</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">User Management</h4>
                        <p>Manage registered users.</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Review Management</h4>
                        <p>Manage trail reviews.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
