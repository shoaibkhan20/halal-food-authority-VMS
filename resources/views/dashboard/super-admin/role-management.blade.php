@extends('layouts.app')
@section('content')

    <div class="w-full min-h-screen grid place-items-center">
        <div class="relative w-full h-full rounded-lg bg-white backdrop:bg-gray/50">
            <div class="flex flex-col w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Role Management</h1>
                </div>
                <div class="flex justify-between">
                    <div class="mb-6 relative w-full max-w-xs">
                        <form action="{{ route('users.role-management') }}" method="GET">
                            <input 
                                type="text" 
                                id="filterSearch" 
                                name="search" 
                                placeholder="Search id or Name " 
                                class="border border-gray-300 rounded px-4 py-2 w-full pr-8" 
                                value="{{ request('search') }}"
                            >

                            @if(request('search'))
                            <a href="{{ route('users.role-management') }}" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            aria-label="Clear search"
                            >
                                &times;
                            </a>
                            @endif
                        </form>
                    </div>
                    <div class="mb-6">
                        <button onclick="my_modal_3.showModal()"
                            class="cursor-pointer bg-green-800 text-white px-4 py-2 rounded">
                            Add User
                        </button>
                    </div>
                </div>
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $headers = ['User ID', 'Name', 'Role', 'Username', 'Contact', 'Action'];

                    $rows = $users->map(function ($user) {
                        $userJson = htmlspecialchars(json_encode([
                            'id' => $user->id,
                            'name' => $user->name,
                            'role_name' => $user->role?->role_name,
                            'username' => $user->username,
                            'contact' => $user->contact
                        ]), ENT_QUOTES, 'UTF-8');

                        return [
                            $user->id,
                            $user->name,
                            $user->role?->role_name,
                            $user->username,
                            $user->contact,
                            '
                                                <div class="flex gap-1">
                                                <button onclick="openEditModal(\'' . $userJson . '\')" class="btn btn-sm cursor-pointer px-3 py-1 text-sm  text-green-800 border-b">Edit</button>
                                                <button onclick="DeleteRequest(\'' . $userJson . '\')" class="btn btn-sm btn-danger cursor-pointer px-3 py-1 text-sm  text-red-800 border-b">Delete</button>
                                                </div>
                                                ',
                        ];
                    })->toArray();
                @endphp


                <x-table :headers="$headers" :rows="$rows" :html="true" />

            </div>

            <div class="absolute right-2">
                {{-- <button class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-700"
                    onclick="document.getElementById('my_modal_5').showModal()">
                    Edit
                </button> --}}
                {{-- <button class="cursor-pointer border px-4 py-2 rounded">
                    Delete
                </button> --}}
            </div>
        </div>
    </div>
    <dialog id="my_modal_3" class="modal">
        <div class="modal-box w-full max-w-2xl p-8 rounded-2xl shadow-xl bg-white relative">
            <!-- Close Button -->
            <form method="dialog">
                <button
                    class="cursor-pointer absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
            </form>
            <!-- Heading -->
            <h3 class="text-3xl text-center text-green-700 mb-8">Add New User</h3>
            <!-- Form -->
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4 flex flex-col justify-center">
                @csrf

                <!-- Full Name -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Full Name *</label>
                    <input type="text" name="name" required placeholder="Enter full name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('name')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Contact (Phone or Email) *</label>
                    <input type="text" name="contact" required placeholder="Phone or Email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('contact')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Username</label>
                    <input type="text" name="username" placeholder="Enter username"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('username')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Role *</label>
                    <select name="role_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option disabled selected>Select role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch (optional) -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Branch (optional)</label>
                    <select name="branch_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option disabled selected>Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Password *</label>
                    <input type="password" name="password" required placeholder="Enter password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('password')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4 text-right">
                    <button type="submit" class="bg-green-800 text-white px-6 py-2 rounded hover:bg-green-700">
                        Add User
                    </button>
                </div>
            </form>

        </div>
    </dialog>

    <!-- Trigger Button -->

    <!-- Modal -->
    <dialog id="my_modal_5" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box w-full max-w-2xl rounded-lg shadow-lg bg-white p-6">
            <!-- Close Button -->
            <form method="dialog">
                <button class="absolute right-4 top-4 text-gray-500 hover:text-red-500 text-xl">&times;</button>
            </form>

            <!-- Title -->
            <h3 class="text-2xl font-semibold text-green-900 mb-6 text-center">Update User</h3>

            <!-- Update Form -->
            <form id="update-user-form" method="POST" class="space-y-4 flex flex-col justify-center">
                @csrf
                @method('PUT')
                <!-- Full Name -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Full Name *</label>
                    <input type="text" id="update-name" name="name" required placeholder="Enter full name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('name')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Contact (Phone or Email) *</label>
                    <input type="text" id="update-contact" name="contact" required placeholder="Phone or Email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('contact')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Username</label>
                    <input type="text" id="update-username" name="username" placeholder="Enter username"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('username')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Role *</label>
                    <select name="role_id" id="update-role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option disabled selected>Select role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch (optional) -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Branch (optional)</label>
                    <select name="branch_id" id="update-branch"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option disabled selected>Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block mb-1 text-sm text-gray-700">Password (leave blank to keep current)</label>
                    <input type="password" id="update-password" name="password" placeholder="Enter new password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    @error('password')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4 text-right">
                    <button type="submit" class="bg-green-800 text-white px-6 py-2 rounded hover:bg-green-700">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </dialog>


    <script>
        function openEditModal(userJson) {
            const user = JSON.parse(userJson);

            // Set form action dynamically (e.g., /users/5)
            const updateRoute = `/super-admin/users/${user.id}`;
            document.getElementById('update-user-form').action = updateRoute;
            // Fill form fields
            document.getElementById('update-name').value = user.name || '';
            document.getElementById('update-contact').value = user.contact || '';
            document.getElementById('update-username').value = user.username || '';

            // Set role and branch (you must pass role_id and branch_id in JSON)
            if (user.role_id) {
                document.getElementById('update-role').value = user.role_id;
            }
            if (user.branch_id) {
                document.getElementById('update-branch').value = user.branch_id;
            }

            // Do not prefill password

            // Show modal
            const modal = document.getElementById('my_modal_5');
            modal.showModal();
        }

        function DeleteRequest(userJson) {
            const user = JSON.parse(userJson);
            if (!confirm(`Are you sure you want to delete this user ${user.name}?`)) return;

            fetch(`/super-admin/delete-user/${user.id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            })
                .then(res => {
                    if (res.ok) {
                        alert("User deleted successfully.");
                        location.reload(); // Refresh the page to reflect changes
                    } else {
                        return res.json().then(data => {
                            throw new Error(data.message || "Failed to delete user.");
                        });
                    }
                })
                .catch(err => {
                    alert("Error: " + err.message);
                });
        }
    </script>


@endsection