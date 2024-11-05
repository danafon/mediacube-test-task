<!-- resources/views/users/index.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>List of Users</h1>

    <!-- Logout Button -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @if ($users->isEmpty())
        <p>No users found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Roles</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $user->roles->implode('title', ', ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{ $users->links() }} <!-- Pagination (if applicable) -->

    <!-- Roles List -->
    <h2>List of Roles</h2>

    @if ($roles->isEmpty())
        <p>No roles found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->title }}</td>
                        <td>{{ $role->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $role->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
