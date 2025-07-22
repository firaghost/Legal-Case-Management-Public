@props(['updates'])
@php use Illuminate\Support\Facades\Storage; @endphp

<ul class="relative border-l-2 border-gray-200 pl-6 space-y-8">
    @foreach($updates as $update)
        <li class="group">
            <span class="absolute -left-2 top-1.5 w-4 h-4 rounded-full bg-white border-2 {{ $update->status === 'Executed' ? 'border-green-600' : 'border-emerald-400' }}"></span>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $update->status }}</p>
                    <p class="text-xs text-gray-500">{{ $update->created_at->format('d M Y') }} • by {{ $update->user->name }}</p>
                    @if($update->notes)
                        <p class="mt-1 text-sm text-gray-700">{{ $update->notes }}</p>
                    @endif
                    @if($update->attachment_path)
                        <a href="{{ Storage::url($update->attachment_path) }}" class="mt-1 inline-flex items-center text-sm text-blue-600 hover:underline" target="_blank">
                            <svg data-lucide="file-text" class="w-4 h-4 mr-1"></svg> View Attachment
                        </a>
                    @endif
                </div>
            </div>
        </li>
    @endforeach
</ul>






