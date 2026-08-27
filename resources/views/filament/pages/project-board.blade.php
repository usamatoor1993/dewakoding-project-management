<x-filament-panels::page>

    {{-- Project Selector --}}
    @if(!$selectedProject)
        <div class="mb-6">
            <x-filament::section>
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Select Project
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Choose a project to view its board
                    </p>
                </div>

                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchProject"
                            placeholder="Search projects by name or prefix..."
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        />
                        @if($searchProject)
                            <button
                                wire:click="$set('searchProject', '')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                @if($projects->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">No Projects Available</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">You don't have access to any projects yet.</p>
                    </div>
                @elseif($this->filteredProjects->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">No Projects Found</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search terms</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        @foreach($this->filteredProjects as $project)
                            <button
                                wire:click="selectProject({{ $project->id }})"
                                class="relative p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-all text-left overflow-hidden"
                                style="border-left: 4px solid {{ $project->color ?? '#6B7280' }};"
                            >
                                @if($project->is_pinned)
                                    <div class="absolute top-2 right-2">
                                        <div class="flex items-center justify-center w-6 h-6 rounded-full shadow-sm"
                                             style="background-color: {{ $project->color ?? '#6B7280' }};"
                                             title="Pinned Project">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M16 9V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v5c0 1.66-1.34 3-3 3v2h5.97v7l1 1 1-1v-7H19v-2c-1.66 0-3-1.34-3-3z"/>
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                @if($project->ticket_prefix)
                                    @php
                                        $color = $project->color ?? '#6B7280';
                                        $hex = ltrim($color, '#');
                                        $r = hexdec(substr($hex, 0, 2));
                                        $g = hexdec(substr($hex, 2, 2));
                                        $b = hexdec(substr($hex, 4, 2));
                                        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                                        $textColor = $brightness > 155 ? '#1F2937' : '#FFFFFF';
                                    @endphp
                                    <div class="inline-flex px-2.5 py-1 rounded text-xs font-semibold mb-3"
                                         style="background-color: {{ $color }}; color: {{ $textColor }};">
                                        {{ $project->ticket_prefix }}
                                    </div>
                                @endif

                                <h3 class="font-semibold text-base text-gray-900 dark:text-white line-clamp-2">
                                    {{ $project->name }}
                                </h3>
                            </button>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        </div>
    @else
        {{-- Jira-style header bar --}}
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            {{-- Left: Project switcher --}}
            <div class="flex items-center gap-3" x-data="{ open: false }">
                @if($selectedProject->ticket_prefix)
                    @php
                        $color = $selectedProject->color ?? '#6B7280';
                        $hex = ltrim($color, '#');
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                        $textColor = $brightness > 155 ? '#1F2937' : '#FFFFFF';
                    @endphp
                    <span class="px-2 py-0.5 rounded text-xs font-bold"
                          style="background-color: {{ $color }}; color: {{ $textColor }};">
                        {{ $selectedProject->ticket_prefix }}
                    </span>
                @endif

                <div class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        class="inline-flex items-center gap-1.5 px-2 py-1 text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded transition-colors"
                    >
                        {{ $selectedProject->name }}
                        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute top-full left-0 mt-1 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto"
                        style="display: none;"
                    >
                        <div class="p-1">
                            @foreach($this->filteredProjects as $project)
                                <button
                                    wire:click="selectProject({{ $project->id }})"
                                    @click="open = false"
                                    class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left {{ $project->id === $selectedProject->id ? 'bg-gray-50 dark:bg-gray-700' : '' }}"
                                >
                                    @if($project->ticket_prefix)
                                        @php
                                            $pColor = $project->color ?? '#6B7280';
                                            $pHex = ltrim($pColor, '#');
                                            $pR = hexdec(substr($pHex, 0, 2));
                                            $pG = hexdec(substr($pHex, 2, 2));
                                            $pB = hexdec(substr($pHex, 4, 2));
                                            $pBrightness = (($pR * 299) + ($pG * 587) + ($pB * 114)) / 1000;
                                            $pTextColor = $pBrightness > 155 ? '#1F2937' : '#FFFFFF';
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-xs font-bold"
                                              style="background-color: {{ $pColor }}; color: {{ $pTextColor }};">
                                            {{ $project->ticket_prefix }}
                                        </span>
                                    @endif
                                    <div class="flex-1 min-w-0 text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $project->name }}
                                    </div>
                                    @if($project->id === $selectedProject->id)
                                        <svg class="w-4 h-4 flex-shrink-0 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2">
                @if($this->selectedProject !== null && auth()->user()->can('create_ticket'))
                    <x-filament::button
                        wire:click="$dispatch('openModal', { name: 'new_ticket', arguments: {} })"
                        icon="heroicon-m-plus"
                        size="sm"
                    >
                        Create
                    </x-filament::button>
                @endif

                <x-filament::button
                    wire:click="refreshBoard"
                    icon="heroicon-m-arrow-path"
                    size="sm"
                    color="gray"
                >
                    Refresh
                </x-filament::button>

                @if($this->selectedProject !== null && auth()->user()->hasRole(['super_admin']))
                    @php
                        $exportAction = \App\Filament\Actions\ExportTicketsAction::make();
                    @endphp
                @endif

                @if($this->selectedProject !== null && $this->projectUsers->isNotEmpty())
                    <div x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            @click.away="open = false"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            <x-heroicon-m-user-group class="w-4 h-4" />
                            Filter
                            @if(count($selectedUserIds) > 0)
                                <span class="ml-1 px-1.5 py-0.5 text-xs font-semibold bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 rounded-full">{{ count($selectedUserIds) }}</span>
                            @endif
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-1 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50"
                            style="display: none;"
                        >
                            <div class="p-3">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                    Filter by assignee
                                </div>
                                @foreach($this->projectUsers as $user)
                                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedUserIds"
                                            value="{{ $user->id }}"
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                        />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                                    </label>
                                @endforeach
                                @if(count($selectedUserIds) > 0)
                                    <button
                                        wire:click="clearUserFilter"
                                        @click="open = false"
                                        class="mt-2 w-full text-center text-xs text-primary-600 hover:text-primary-500 font-medium py-1"
                                    >
                                        Clear filter
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- View Only Mode Indicator --}}
        @if(!$this->canMoveTickets())
            <div class="flex justify-center mb-4">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg text-amber-800 dark:text-amber-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="text-sm font-medium">View Only Mode</span>
                </div>
            </div>
        @endif

        {{-- Board --}}
        <div
            x-data="{
                draggingTicket: null,
                isTouchDevice: false,
                touchStartX: 0,
                touchStartY: 0,
                scrollStartX: 0,
                columnScrollPositions: {},

                moveTicketToStatus(ticketId, statusId) {
                    $wire.call('moveTicket', parseInt(ticketId), parseInt(statusId));
                },

                saveScrollPositions() {
                    const columns = document.querySelectorAll('.status-column .overflow-y-auto');
                    columns.forEach((column, index) => {
                        this.columnScrollPositions[index] = column.scrollTop;
                    });
                },

                restoreScrollPositions() {
                    const columns = document.querySelectorAll('.status-column .overflow-y-auto');
                    columns.forEach((column, index) => {
                        if (this.columnScrollPositions[index] !== undefined) {
                            column.scrollTop = this.columnScrollPositions[index];
                        }
                    });
                },

                init() {
                    this.$nextTick(() => {
                        this.attachAllEventListeners();
                        this.setupTouchScrolling();
                        this.isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
                        this.setupPageVisibilityListener();
                    });
                },

                setupPageVisibilityListener() {
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) {
                            this.saveScrollPositions();
                            setTimeout(() => {
                                this.attachAllEventListeners();
                                this.restoreScrollPositions();
                            }, 100);
                        }
                    });

                    window.addEventListener('focus', () => {
                        this.saveScrollPositions();
                        setTimeout(() => {
                            this.attachAllEventListeners();
                            this.restoreScrollPositions();
                        }, 100);
                    });

                    window.addEventListener('popstate', () => {
                        this.saveScrollPositions();
                        setTimeout(() => {
                            this.attachAllEventListeners();
                            this.restoreScrollPositions();
                        }, 200);
                    });

                    document.addEventListener('livewire:navigated', () => {
                        this.saveScrollPositions();
                        setTimeout(() => {
                            this.attachAllEventListeners();
                            this.restoreScrollPositions();
                        }, 300);
                    });

                    document.addEventListener('livewire:load', () => {
                        this.saveScrollPositions();
                        setTimeout(() => {
                            this.attachAllEventListeners();
                            this.restoreScrollPositions();
                        }, 100);
                    });

                    document.addEventListener('livewire:updated', () => {
                        this.saveScrollPositions();
                        setTimeout(() => {
                            this.attachAllEventListeners();
                            this.restoreScrollPositions();
                        }, 100);
                    });

                    window.addEventListener('ticket-updated', () => {
                        this.saveScrollPositions();
                        setTimeout(() => {
                            this.attachAllEventListeners();
                            this.restoreScrollPositions();
                        }, 150);
                    });

                    setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            this.saveScrollPositions();
                            this.ensureDragDropInitialized();
                            this.restoreScrollPositions();
                        }
                    }, 2000);
                },

                ensureDragDropInitialized() {
                    this.attachAllEventListeners();
                },

                setupTouchScrolling() {
                    const container = document.getElementById('board-container');
                    if (!container) return;

                    container.addEventListener('touchstart', (e) => {
                        this.touchStartX = e.touches[0].clientX;
                        this.touchStartY = e.touches[0].clientY;
                        this.scrollStartX = container.scrollLeft;
                    }, { passive: true });

                    container.addEventListener('touchmove', (e) => {
                        if (e.touches.length !== 1) return;
                        const touchX = e.touches[0].clientX;
                        const touchY = e.touches[0].clientY;
                        const moveX = this.touchStartX - touchX;
                        const moveY = this.touchStartY - touchY;
                        if (Math.abs(moveX) > Math.abs(moveY)) {
                            e.preventDefault();
                            container.scrollLeft = this.scrollStartX + moveX;
                        }
                    }, { passive: false });
                },

                attachAllEventListeners() {
                    @if(!$this->canMoveTickets())
                        return;
                    @endif

                    const tickets = document.querySelectorAll('.ticket-card');
                    tickets.forEach(ticket => {
                        if (ticket.dataset.boardBound === 'true') return;
                        ticket.dataset.boardBound = 'true';
                        ticket.setAttribute('draggable', true);

                        ticket.addEventListener('dragstart', (e) => {
                            this.draggingTicket = ticket.getAttribute('data-ticket-id');
                            ticket.classList.add('opacity-50');
                            e.dataTransfer.effectAllowed = 'move';
                        });

                        ticket.addEventListener('dragend', () => {
                            ticket.classList.remove('opacity-50');
                            this.draggingTicket = null;
                        });

                        let longPressTimer;
                        let isDragging = false;
                        let originalColumn;

                        ticket.addEventListener('touchstart', (e) => {
                            if (isDragging) return;
                            longPressTimer = setTimeout(() => {
                                originalColumn = ticket.closest('.status-column');
                                this.draggingTicket = ticket.getAttribute('data-ticket-id');
                                ticket.classList.add('opacity-50', 'relative', 'z-30');
                                isDragging = true;
                                ticket.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
                            }, 500);
                        }, { passive: true });

                        ticket.addEventListener('touchmove', (e) => {
                            if (!isDragging) { clearTimeout(longPressTimer); return; }
                            const touch = e.touches[0];
                            const columns = document.querySelectorAll('.status-column');
                            columns.forEach(column => {
                                const rect = column.getBoundingClientRect();
                                if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                                    touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                                    column.classList.add('bg-primary-50', 'dark:bg-primary-950');
                                } else {
                                    column.classList.remove('bg-primary-50', 'dark:bg-primary-950');
                                }
                            });
                        });

                        ticket.addEventListener('touchend', (e) => {
                            clearTimeout(longPressTimer);
                            if (!isDragging) return;
                            isDragging = false;
                            ticket.classList.remove('opacity-50', 'relative', 'z-30');
                            ticket.style.boxShadow = '';
                            const touch = e.changedTouches[0];
                            const columns = document.querySelectorAll('.status-column');
                            let targetColumn = null;
                            columns.forEach(column => {
                                const rect = column.getBoundingClientRect();
                                if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                                    touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                                    targetColumn = column;
                                }
                                column.classList.remove('bg-primary-50', 'dark:bg-primary-950');
                            });
                            if (targetColumn && targetColumn !== originalColumn) {
                                this.moveTicketToStatus(this.draggingTicket, targetColumn.getAttribute('data-status-id'));
                            }
                            this.draggingTicket = null;
                        });

                        ticket.addEventListener('touchcancel', () => {
                            clearTimeout(longPressTimer);
                            if (!isDragging) return;
                            isDragging = false;
                            ticket.classList.remove('opacity-50', 'relative', 'z-30');
                            ticket.style.boxShadow = '';
                            this.draggingTicket = null;
                            document.querySelectorAll('.status-column').forEach(c => c.classList.remove('bg-primary-50', 'dark:bg-primary-950'));
                        });
                    });

                    const columns = document.querySelectorAll('.status-column');
                    columns.forEach(column => {
                        if (column.dataset.boardBound === 'true') return;
                        column.dataset.boardBound = 'true';
                        column.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            e.dataTransfer.dropEffect = 'move';
                            column.classList.add('bg-primary-50/50', 'dark:bg-primary-950/50');
                        });
                        column.addEventListener('dragleave', () => {
                            column.classList.remove('bg-primary-50/50', 'dark:bg-primary-950/50');
                        });
                        column.addEventListener('drop', (e) => {
                            e.preventDefault();
                            column.classList.remove('bg-primary-50/50', 'dark:bg-primary-950/50');
                            if (this.draggingTicket) {
                                this.moveTicketToStatus(this.draggingTicket, column.getAttribute('data-status-id'));
                                this.draggingTicket = null;
                            }
                        });
                    });
                }
            }"
            x-init="init()"
            @ticket-moved.window="init()"
            @ticket-updated.window="init()"
            @refresh-board.window="init()"
            wire:key="board-container-{{ $selectedProject->id }}"
            class="relative overflow-x-auto pb-6 {{ !$this->canMoveTickets() ? 'view-only-mode' : '' }}"
            id="board-container"
        >
            {{-- Mobile swipe hint --}}
            <div class="md:hidden flex justify-center mb-2 text-xs text-gray-500 dark:text-gray-400 items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Swipe to view all columns</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </div>

            {{-- Kanban columns --}}
            <div class="inline-flex gap-3 min-w-full">
                @foreach ($this->ticketStatuses as $status)
                    <div
                        wire:key="status-column-{{ $status->id }}"
                        class="status-column flex flex-col w-[280px] min-w-[280px] max-w-[320px] flex-shrink-0"
                        data-status-id="{{ $status->id }}"
                    >
                        {{-- Column header --}}
                        <div class="flex items-center gap-2 px-1 py-2 mb-1">
                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $status->color ?? '#DFE1E6' }};"></div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                {{ $status->name }}
                            </h3>
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ $status->tickets->count() }}</span>

                            @if($status->is_completed)
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif

                            {{-- Sort dropdown --}}
                            <div class="relative ml-auto" x-data="{ open: false }">
                                <button
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors text-gray-400 dark:text-gray-500"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>

                                <div
                                    x-show="open"
                                    x-transition
                                    class="absolute top-8 right-0 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 py-1"
                                    style="display: none;"
                                >
                                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                        Sort by
                                    </div>
                                    <button wire:click="setSortOrder({{ $status->id }}, 'date_created_newest')" @click="open = false" class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Date created (newest)</button>
                                    <button wire:click="setSortOrder({{ $status->id }}, 'date_created_oldest')" @click="open = false" class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Date created (oldest)</button>
                                    <button wire:click="setSortOrder({{ $status->id }}, 'card_name_alphabetical')" @click="open = false" class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Card name</button>
                                    <button wire:click="setSortOrder({{ $status->id }}, 'due_date')" @click="open = false" class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Due date</button>
                                    <button wire:click="setSortOrder({{ $status->id }}, 'priority')" @click="open = false" class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Priority</button>
                                </div>
                            </div>
                        </div>

                        {{-- Cards container --}}
                        <div class="flex-1 overflow-y-auto rounded-lg bg-gray-100/50 dark:bg-gray-800/50 p-2 space-y-2" style="min-height: 200px; max-height: calc(100vh - 260px);"
                             x-data="{ visibleTickets: 10, totalTickets: {{ $status->tickets->count() }} }"
                             x-init="$nextTick(() => { $el.addEventListener('scroll', () => { if ($el.scrollTop + $el.clientHeight >= $el.scrollHeight - 100 && visibleTickets < totalTickets) { visibleTickets = Math.min(visibleTickets + 10, totalTickets); } }); })"
                        >
                            @foreach ($status->tickets as $index => $ticket)
                                <div
                                    wire:key="ticket-{{ $status->id }}-{{ $ticket->id }}"
                                    class="ticket-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 cursor-move hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
                                    data-ticket-id="{{ $ticket->id }}"
                                    x-show="{{ $index }} < visibleTickets"
                                >
                                    <div class="p-3">
                                        {{-- Top row: key + priority --}}
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-mono text-gray-400 dark:text-gray-500">
                                                {{ $ticket->uuid }}
                                            </span>
                                            @if ($ticket->priority)
                                                <span class="text-xs px-1.5 py-0.5 rounded font-medium text-white" style="background-color: {{ $ticket->priority->color }};">
                                                    {{ $ticket->priority->name }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Title --}}
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2 leading-snug">
                                            {{ $ticket->name }}
                                        </h4>

                                        {{-- Epic badge --}}
                                        @if ($ticket->epic)
                                            <div class="mb-2">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                    <x-heroicon-m-flag class="w-3 h-3" />
                                                    {{ $ticket->epic->name }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Bottom row: due date + assignee --}}
                                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100 dark:border-gray-700/50">
                                            <div class="flex items-center gap-2">
                                                @if ($ticket->due_date)
                                                    <span class="inline-flex items-center gap-1 text-xs {{ $ticket->due_date->isPast() ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                                        <x-heroicon-o-calendar class="w-3 h-3" />
                                                        {{ $ticket->due_date->format('MMM d') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                @if ($ticket->assignees->isNotEmpty())
                                                    @foreach($ticket->assignees->take(2) as $assignee)
                                                        @php
                                                            $nameColors = ['#0052CC','#36B37E','#FF5630','#FFAB00','#6554C0','#00B8D9','#57D9A3','#FF8B00','#0065FF','#FF7452'];
                                                            $colorIndex = crc32($assignee->name) % count($nameColors);
                                                        @endphp
                                                        <div
                                                            class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium text-white flex-shrink-0"
                                                            style="background-color: {{ $nameColors[$colorIndex] }};"
                                                            title="{{ $assignee->name }}"
                                                        >
                                                            {{ substr($assignee->name, 0, 1) }}
                                                        </div>
                                                    @endforeach
                                                    @if($ticket->assignees->count() > 2)
                                                        <span class="text-xs text-gray-400 dark:text-gray-500">+{{ $ticket->assignees->count() - 2 }}</span>
                                                    @endif
                                                @else
                                                    <div class="w-6 h-6 rounded-full flex items-center justify-center bg-gray-200 dark:bg-gray-700" title="Unassigned">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif

                                                <a
                                                    href="#"
                                                    wire:click.prevent="showTicketDetails({{ $ticket->id }})"
                                                    class="w-6 h-6 flex items-center justify-center rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                                >
                                                    <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if ($status->tickets->isEmpty())
                                <div class="flex items-center justify-center h-20 text-gray-400 dark:text-gray-500 text-xs">
                                    No issues
                                </div>
                            @endif

                            @if ($status->tickets->isNotEmpty() && $status->tickets->count() > 10)
                                <div x-show="visibleTickets < totalTickets" class="flex items-center justify-center py-3 text-gray-400 dark:text-gray-500 text-xs">
                                    Loading more...
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($this->ticketStatuses->isEmpty())
                    <div class="w-full flex items-center justify-center h-40 text-gray-500 dark:text-gray-400">
                        No status columns found for this project
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
