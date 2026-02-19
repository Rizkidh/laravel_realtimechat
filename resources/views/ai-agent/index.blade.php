@extends('layouts.app')

@section('title', 'AI Agent')
@section('main-class', 'max-w-4xl mx-auto px-4 py-6 w-full')
@section('hide-footer', true)

@section('content')
<div class="bg-white rounded-none sm:rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[calc(100dvh-4rem)] sm:h-[calc(100dvh-7rem)]">

    {{-- Header --}}
    <div class="px-5 py-4 bg-gradient-to-r from-violet-600 to-purple-700 flex items-center gap-3 flex-shrink-0">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-white">AI Agent</h2>
            <p class="text-xs text-purple-200">Tanyakan apapun kepada AI</p>
        </div>
        <div class="ml-auto">
            <button id="clearHistoryBtn" onclick="AiApp.confirmClear()" class="text-xs text-purple-200 hover:text-white px-3 py-1.5 rounded-lg hover:bg-white/10 transition hidden">
                Hapus History
            </button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div id="aiMessages" class="flex-1 overflow-y-auto px-4 py-4 space-y-4 min-h-0 scroll-smooth" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 50%, #ede9fe 100%);">

        {{-- History from DB --}}
        @forelse($history as $conv)
        <div class="flex justify-end">
            <div class="max-w-[80%] bg-violet-600 text-white px-4 py-3 rounded-2xl rounded-br-sm shadow-sm">
                <p class="text-sm whitespace-pre-wrap">{{ $conv->question }}</p>
                <span class="text-xs text-violet-300 block mt-1 text-right">{{ $conv->created_at->format('H:i') }}</span>
            </div>
        </div>
        <div class="flex justify-start">
            <div class="flex items-start gap-2 max-w-[80%]">
                <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="bg-white text-gray-900 px-4 py-3 rounded-2xl rounded-bl-sm shadow-sm">
                    <p class="text-sm whitespace-pre-wrap">{{ $conv->answer }}</p>
                    <span class="text-xs text-gray-400 block mt-1">{{ $conv->created_at->format('H:i') }}</span>
                </div>
            </div>
        </div>
        @empty
        <div id="aiWelcome" class="flex items-center justify-center h-full">
            <div class="text-center">
                <div class="w-20 h-20 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Halo! Saya AI Agent</h3>
                <p class="text-gray-500 text-sm max-w-sm">Silakan ajukan pertanyaan apapun. Saya akan berusaha membantu Anda.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Input Area --}}
    <div class="px-4 py-3 bg-white border-t border-gray-200 flex-shrink-0">
        <form id="aiForm" class="flex items-end gap-2">
            <div class="flex-1 relative">
                <textarea id="aiInput" rows="1" placeholder="Ketik pertanyaan Anda..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm resize-none focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition" style="max-height: 120px;"></textarea>
            </div>
            <button type="submit" id="aiSendBtn" class="p-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </form>
        <p id="rateLimitMsg" class="hidden text-xs text-red-500 mt-2 text-center">Batas request tercapai. Silakan tunggu sebentar.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var sending = false;

    var els = {
        form: document.getElementById('aiForm'),
        input: document.getElementById('aiInput'),
        sendBtn: document.getElementById('aiSendBtn'),
        messages: document.getElementById('aiMessages'),
        welcome: document.getElementById('aiWelcome'),
        rateLimitMsg: document.getElementById('rateLimitMsg'),
        clearBtn: document.getElementById('clearHistoryBtn'),
    };

    // Show clear btn if history exists
    if (els.messages.querySelectorAll('.flex').length > 0 && !els.welcome) {
        els.clearBtn.classList.remove('hidden');
    }

    function scrollToBottom() {
        setTimeout(function() {
            els.messages.scrollTop = els.messages.scrollHeight;
        }, 50);
    }

    scrollToBottom();

    function escHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function addUserBubble(question) {
        // Remove welcome if present
        if (els.welcome) {
            els.welcome.remove();
            els.welcome = null;
        }

        var now = new Date();
        var time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        var div = document.createElement('div');
        div.className = 'flex justify-end';
        div.innerHTML = '<div class="max-w-[80%] bg-violet-600 text-white px-4 py-3 rounded-2xl rounded-br-sm shadow-sm">'
            + '<p class="text-sm whitespace-pre-wrap">' + escHtml(question) + '</p>'
            + '<span class="text-xs text-violet-300 block mt-1 text-right">' + time + '</span>'
            + '</div>';
        els.messages.appendChild(div);
        scrollToBottom();
    }

    function addLoadingBubble() {
        var div = document.createElement('div');
        div.className = 'flex justify-start';
        div.id = 'aiLoading';
        div.innerHTML = '<div class="flex items-start gap-2 max-w-[80%]">'
            + '<div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">'
            + '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'
            + '</div>'
            + '<div class="bg-white text-gray-900 px-4 py-3 rounded-2xl rounded-bl-sm shadow-sm">'
            + '<div class="flex items-center gap-1.5">'
            + '<div class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay:0ms"></div>'
            + '<div class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay:150ms"></div>'
            + '<div class="w-2 h-2 bg-violet-400 rounded-full animate-bounce" style="animation-delay:300ms"></div>'
            + '<span class="text-xs text-gray-400 ml-2">AI sedang berpikir...</span>'
            + '</div></div></div>';
        els.messages.appendChild(div);
        scrollToBottom();
    }

    function removeLoadingBubble() {
        var el = document.getElementById('aiLoading');
        if (el) el.remove();
    }

    function addAiBubble(answer) {
        removeLoadingBubble();

        var now = new Date();
        var time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        var div = document.createElement('div');
        div.className = 'flex justify-start';
        div.innerHTML = '<div class="flex items-start gap-2 max-w-[80%]">'
            + '<div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">'
            + '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'
            + '</div>'
            + '<div class="bg-white text-gray-900 px-4 py-3 rounded-2xl rounded-bl-sm shadow-sm">'
            + '<p class="text-sm whitespace-pre-wrap">' + escHtml(answer) + '</p>'
            + '<span class="text-xs text-gray-400 block mt-1">' + time + '</span>'
            + '</div></div>';
        els.messages.appendChild(div);
        scrollToBottom();

        els.clearBtn.classList.remove('hidden');
    }

    function sendQuestion() {
        var question = els.input.value.trim();
        if (!question || sending) return;

        sending = true;
        els.sendBtn.disabled = true;
        els.input.value = '';
        autoResize();
        els.rateLimitMsg.classList.add('hidden');

        addUserBubble(question);
        addLoadingBubble();

        fetch('/ai-agent/ask', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ question: question }),
        })
        .then(function(r) {
            if (r.status === 429) {
                removeLoadingBubble();
                els.rateLimitMsg.classList.remove('hidden');
                throw new Error('Rate limit');
            }
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            addAiBubble(data.conversation.answer);
        })
        .catch(function(err) {
            if (err.message !== 'Rate limit') {
                removeLoadingBubble();
                addAiBubble('Maaf, terjadi kesalahan. Silakan coba lagi.');
            }
        })
        .finally(function() {
            sending = false;
            els.sendBtn.disabled = !els.input.value.trim();
        });
    }

    function autoResize() {
        els.input.style.height = 'auto';
        els.input.style.height = Math.min(els.input.scrollHeight, 120) + 'px';
    }

    els.form.addEventListener('submit', function(e) {
        e.preventDefault();
        sendQuestion();
    });

    els.input.addEventListener('input', function() {
        els.sendBtn.disabled = !this.value.trim();
        autoResize();
    });

    els.input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendQuestion();
        }
    });

    window.AiApp = {
        confirmClear: function() {
            if (confirm('Hapus semua history percakapan AI?')) {
                // Note: No backend delete endpoint yet, just reload
                location.reload();
            }
        }
    };
})();
</script>
@endpush
