@extends('layouts.app')

@section('title', 'Chat')
@section('main-class', 'max-w-7xl mx-auto px-0 sm:px-4 py-0 sm:py-6 w-full')
@section('hide-footer', true)

@section('content')
<div id="chatApp" class="bg-white rounded-none sm:rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[calc(100dvh-4rem)] sm:h-[calc(100dvh-7rem)]">
    <div class="flex h-full min-h-0">

        {{-- ═══════ LEFT: Contact List ═══════ --}}
        <div id="contactPanel" class="w-full md:w-80 lg:w-96 border-r border-gray-200 flex flex-col bg-white h-full">
            {{-- Header --}}
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-primary-600 to-primary-700 flex-shrink-0">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Chats
                </h2>
            </div>

            {{-- Search --}}
            <div class="p-3 border-b border-gray-100 flex-shrink-0">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="searchContacts" type="text" placeholder="Cari kontak..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                </div>
            </div>

            {{-- Contact List --}}
            <div id="contactList" class="flex-1 overflow-y-auto min-h-0">
                <div id="contactLoading" class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                </div>
            </div>
        </div>

        {{-- ═══════ RIGHT: Chat Window ═══════ --}}
        <div id="chatPanel" class="hidden md:flex flex-1 flex-col bg-gray-50 h-full min-h-0 relative">
            {{-- Empty State --}}
            <div id="emptyChat" class="flex-1 flex items-center justify-center p-4">
                <div class="text-center">
                    <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Selamat datang di Chat</h3>
                    <p class="text-gray-500 text-sm">Pilih kontak untuk memulai percakapan</p>
                </div>
            </div>

            {{-- Active Chat --}}
            <div id="activeChat" class="hidden flex-1 flex flex-col h-full min-h-0">
                {{-- Chat Header --}}
                <div id="chatHeader" class="px-4 py-3 bg-white border-b border-gray-200 flex items-center gap-3 flex-shrink-0">
                    <button id="backToContacts" class="md:hidden p-1 rounded-lg hover:bg-gray-100 transition mr-1">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="relative">
                        <div id="contactAvatar" class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm" id="contactInitial">?</span>
                        </div>
                        <span id="contactOnlineDot" class="hidden absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 id="contactName" class="font-semibold text-gray-900 truncate">-</h3>
                        <p id="contactStatus" class="text-xs text-gray-500">-</p>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div id="messagesContainer" class="flex-1 overflow-y-auto px-4 py-3 space-y-1 min-h-0 scroll-smooth" style="background: linear-gradient(135deg, #f0f4f8 0%, #e8ecf1 100%);">
                    <div id="loadMoreBtn" class="hidden text-center py-2">
                        <button onclick="ChatApp.loadOlderMessages()" class="text-xs text-primary-600 hover:text-primary-800 font-medium px-4 py-1.5 bg-white rounded-full shadow-sm hover:shadow transition">
                            Muat pesan lama ↑
                        </button>
                    </div>
                    <div id="messagesLoading" class="hidden flex justify-center py-8">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
                    </div>
                    <div id="messagesList"></div>
                    <div id="typingIndicator" class="hidden flex items-center gap-2 py-1">
                        <div class="bg-white px-4 py-2.5 rounded-2xl rounded-bl-sm shadow-sm">
                            <div class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Message Input --}}
                <div class="px-4 py-3 bg-white border-t border-gray-200 flex-shrink-0">
                    <form id="messageForm" class="flex items-end gap-2">
                        <div class="flex-1 relative">
                            <textarea id="messageInput" rows="1" placeholder="Ketik pesan..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" style="max-height: 120px;"></textarea>
                        </div>
                        <button type="submit" id="sendBtn" class="p-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0" disabled>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('storage/script/chat/service.js') }}"></script>
<script src="{{ asset('storage/script/chat/controller.js') }}"></script>
@endpush

