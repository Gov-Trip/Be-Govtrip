@props([
    'steps' => [],
    'orientation' => 'horizontal',
])

@if($orientation === 'vertical')
    <div {{ $attributes->merge(['class' => 'flex flex-col gap-4']) }}>
        @foreach($steps as $idx => $step)
            @php
                $status = $step['status'] ?? 'upcoming';
                $isCompleted = $status === 'completed';
                $isCurrent = $status === 'current';
                $isLast = $loop->last;
            @endphp
            <div class="flex items-start gap-3 relative">
                @if(!$isLast)
                    <div class="absolute left-[15px] top-[32px] bottom-[-16px] w-[2px] {{ $isCompleted ? 'bg-[#12A9A5]' : 'bg-[#E8EEF3]' }}"></div>
                @endif

                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-semibold shrink-0 z-10 {{ $isCompleted ? 'bg-[#12A9A5] text-white' : ($isCurrent ? 'bg-[#F5A000] text-[#1E293B] ring-4 ring-[#FFF1D1]' : 'bg-[#E8EEF3] text-[#64748B]') }}">
                    @if($isCompleted)
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    @else
                        {{ $idx + 1 }}
                    @endif
                </div>

                <div class="flex flex-col pt-1">
                    <span class="text-[13px] font-semibold leading-tight {{ $isCurrent ? 'text-[#123B72]' : ($isCompleted ? 'text-[#1E293B]' : 'text-[#64748B]') }}">
                        {{ $step['label'] }}
                    </span>
                    @if(!empty($step['sublabel']))
                        <span class="text-[11.5px] text-[#64748B] mt-0.5">
                            {{ $step['sublabel'] }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div {{ $attributes->merge(['class' => 'w-full overflow-x-auto py-2']) }}>
        <div class="flex items-center justify-between min-w-[500px] relative">
            @foreach($steps as $idx => $step)
                @php
                    $status = $step['status'] ?? 'upcoming';
                    $isCompleted = $status === 'completed';
                    $isCurrent = $status === 'current';
                    $isLast = $loop->last;
                @endphp
                <div class="flex-1 flex flex-col items-center relative {{ $isLast ? 'flex-none' : '' }}">
                    @if(!$isLast)
                        <div class="absolute top-[16px] left-[50%] right-[-50%] h-[2px] -z-0 {{ $isCompleted ? 'bg-[#12A9A5]' : 'bg-[#E8EEF3]' }}"></div>
                    @endif

                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-semibold shrink-0 z-10 transition-all {{ $isCompleted ? 'bg-[#12A9A5] text-white' : ($isCurrent ? 'bg-[#F5A000] text-[#1E293B] ring-4 ring-[#FFF1D1]' : 'bg-[#E8EEF3] text-[#64748B]') }}">
                        @if($isCompleted)
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>

                    <div class="flex flex-col items-center mt-2 text-center max-w-[120px]">
                        <span class="text-[12px] font-semibold leading-tight {{ $isCurrent ? 'text-[#123B72]' : ($isCompleted ? 'text-[#1E293B]' : 'text-[#64748B]') }}">
                            {{ $step['label'] }}
                        </span>
                        @if(!empty($step['sublabel']))
                            <span class="text-[11px] text-[#64748B] mt-0.5">
                                {{ $step['sublabel'] }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
