<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
    @foreach ($months as $month)
        @php
            $monthKey = $month->format('Y-m');
            $submission = $submissionsMap[$monthKey] ?? null;
            $status = $submission->status ?? 'not_submitted';

            // Configuração visual de cada status
            $ui = [
                'approved' => [
                    'class' => 'bg-green-50 border-green-200 text-green-800 shadow-sm',
                    'icon' => '<svg class="w-5 h-5 text-green-600 mb-1 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'label' => 'Aprovado'
                ],
                'pending' => [
                    'class' => 'bg-yellow-50 border-yellow-200 text-yellow-800 shadow-sm',
                    'icon' => '<svg class="w-5 h-5 text-yellow-600 mb-1 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'label' => 'Em Análise'
                ],
                'rejected' => [
                    'class' => 'bg-red-50 border-red-300 text-red-900 shadow-sm relative overflow-hidden',
                    'icon' => '<svg class="w-5 h-5 text-red-600 mb-1 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'label' => 'Reprovado'
                ],
                'not_submitted' => [
                    'class' => 'bg-gray-50 border-gray-200 text-gray-400 opacity-70 hover:opacity-100 border-dashed',
                    'icon' => '<svg class="w-5 h-5 text-gray-300 mb-1 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>',
                    'label' => 'Aberto'
                ],
            ];
            
            $currentUI = $ui[$status];
        @endphp

        <div class="border rounded-xl p-3 text-center transition-all duration-200 {{ $currentUI['class'] }}">
            {!! $currentUI['icon'] !!}
            <div class="font-bold text-sm uppercase tracking-wider">{{ $month->translatedFormat('M/y') }}</div>
            <div class="text-[11px] font-semibold mt-0.5 opacity-90">{{ $currentUI['label'] }}</div>
            
            {{-- Tooltip/Badge para observações futuras --}}
            @if($status === 'rejected' && $submission && $submission->observation)
                <div class="mt-2 pt-2 border-t border-red-200 text-[10px] leading-tight text-red-700 line-clamp-2" title="{{ $submission->observation }}">
                    💬 {{ $submission->observation }}
                </div>
            @endif
        </div>
    @endforeach
</div>