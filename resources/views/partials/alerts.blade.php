@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
    <i class="fa-solid fa-circle-check text-green-500"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
    <div class="flex items-center gap-2 mb-1 font-semibold">
        <i class="fa-solid fa-triangle-exclamation"></i> Please fix the following errors:
    </div>
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
