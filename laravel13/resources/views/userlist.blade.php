<x-layout>
    @foreach ($users as $user)
        <div>
            <dl>
                <dt>Name</dt>
                <dd>{{ $user->name }}</dd>
                <dt>Email</dt>
                <dd>{{ $user->email }}</dd>
                <dt>Created At</dt>
                <dd>{{ $user->created_at->format('c') }}</dd>
            </dl>
        </div>
    @endforeach
</x-layout>
