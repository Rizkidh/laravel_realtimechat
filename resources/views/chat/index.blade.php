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
<script>
(function() {
    'use strict';

    var AUTH_USER_ID = {{ auth()->id() }};
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    var state = {
        contacts: [],
        activeContact: null,
        messages: [],
        hasMore: false,
        loading: false,
        typingTimeout: null,
        isTyping: false,
    };

    var els = {
        contactList: document.getElementById('contactList'),
        contactLoading: document.getElementById('contactLoading'),
        searchContacts: document.getElementById('searchContacts'),
        contactPanel: document.getElementById('contactPanel'),
        chatPanel: document.getElementById('chatPanel'),
        emptyChat: document.getElementById('emptyChat'),
        activeChat: document.getElementById('activeChat'),
        contactName: document.getElementById('contactName'),
        contactStatus: document.getElementById('contactStatus'),
        contactInitial: document.getElementById('contactInitial'),
        contactOnlineDot: document.getElementById('contactOnlineDot'),
        messagesContainer: document.getElementById('messagesContainer'),
        messagesList: document.getElementById('messagesList'),
        messagesLoading: document.getElementById('messagesLoading'),
        loadMoreBtn: document.getElementById('loadMoreBtn'),
        typingIndicator: document.getElementById('typingIndicator'),
        messageForm: document.getElementById('messageForm'),
        messageInput: document.getElementById('messageInput'),
        sendBtn: document.getElementById('sendBtn'),
        backToContacts: document.getElementById('backToContacts'),
    };

    // ── API Helpers ──
    function api(method, url, data) {
        var opts = {
            method: method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
        };
        if (data) opts.body = JSON.stringify(data);
        return fetch(url, opts).then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        });
    }

    // ── Load Contacts ──
    function loadContacts() {
        api('GET', '/chat/contacts').then(function(data) {
            state.contacts = data.contacts || [];
            renderContacts();
            els.contactLoading.classList.add('hidden');
        }).catch(function() {
            els.contactLoading.innerHTML = '<p class="text-sm text-gray-500">Gagal memuat kontak</p>';
        });
    }

    function renderContacts(filter) {
        var html = '';
        var filtered = state.contacts;
        if (filter) {
            var q = filter.toLowerCase();
            filtered = filtered.filter(function(c) { return c.name.toLowerCase().indexOf(q) !== -1; });
        }

        if (filtered.length === 0) {
            html = '<div class="px-4 py-12 text-center text-sm text-gray-500">Tidak ada kontak</div>';
        } else {
            filtered.forEach(function(contact) {
                var isActive = state.activeContact && state.activeContact.id === contact.id;
                var initial = contact.name.charAt(0).toUpperCase();
                var lastMsg = contact.last_message ? (contact.last_message.is_mine ? 'Anda: ' : '') + truncate(contact.last_message.message, 30) : 'Belum ada pesan';
                var timeStr = contact.last_message ? timeAgo(contact.last_message.created_at) : '';
                var unread = contact.unread_count || 0;

                html += '<div class="contact-item flex items-center gap-3 px-4 py-3 cursor-pointer transition-all duration-150 hover:bg-gray-50 border-l-4 '
                    + (isActive ? 'bg-primary-50 border-primary-500' : 'border-transparent')
                    + '" data-id="' + contact.id + '" onclick="ChatApp.selectContact(' + contact.id + ')">'
                    + '<div class="relative flex-shrink-0">'
                    + '  <div class="w-11 h-11 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center">'
                    + '    <span class="text-white font-semibold text-sm">' + initial + '</span>'
                    + '  </div>'
                    + (contact.is_online ? '<span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>' : '')
                    + '</div>'
                    + '<div class="flex-1 min-w-0">'
                    + '  <div class="flex items-center justify-between">'
                    + '    <h4 class="font-semibold text-sm text-gray-900 truncate">' + escHtml(contact.name) + '</h4>'
                    + '    <span class="text-xs text-gray-400 flex-shrink-0 ml-2">' + timeStr + '</span>'
                    + '  </div>'
                    + '  <div class="flex items-center justify-between mt-0.5">'
                    + '    <p class="text-xs text-gray-500 truncate">' + escHtml(lastMsg) + '</p>'
                    + (unread > 0 ? '<span class="bg-primary-600 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 font-semibold flex-shrink-0 ml-2">' + unread + '</span>' : '')
                    + '  </div>'
                    + '</div>'
                    + '</div>';
            });
        }
        els.contactList.innerHTML = html;
    }

    // ── Select Contact ──
    function selectContact(contactId) {
        var contact = state.contacts.find(function(c) { return c.id === contactId; });
        if (!contact) return;

        state.activeContact = contact;
        state.messages = [];
        state.hasMore = false;

        // Update header
        els.contactName.textContent = contact.name;
        els.contactInitial.textContent = contact.name.charAt(0).toUpperCase();
        updateContactStatus(contact);

        // Show chat panel (mobile)
        els.contactPanel.classList.add('hidden');
        els.contactPanel.classList.remove('flex');
        els.chatPanel.classList.remove('hidden');
        els.chatPanel.classList.add('flex');

        // Show active chat
        els.emptyChat.classList.add('hidden');
        els.activeChat.classList.remove('hidden');
        els.activeChat.classList.add('flex');

        renderContacts(); // highlight active
        loadMessages();

        // Mark as read
        api('POST', '/chat/read/' + contactId);
        contact.unread_count = 0;
        renderContacts();
        if (window.updateNavUnread) window.updateNavUnread();

        els.messageInput.focus();
    }

    function updateContactStatus(contact) {
        if (contact.is_online) {
            els.contactStatus.textContent = 'Online';
            els.contactStatus.className = 'text-xs text-green-600 font-medium';
            els.contactOnlineDot.classList.remove('hidden');
        } else {
            els.contactStatus.textContent = contact.last_seen ? 'Terakhir dilihat ' + timeAgo(contact.last_seen) : 'Offline';
            els.contactStatus.className = 'text-xs text-gray-500';
            els.contactOnlineDot.classList.add('hidden');
        }
    }

    // ── Load Messages ──
    function loadMessages(beforeId) {
        if (state.loading || !state.activeContact) return;
        state.loading = true;

        var url = '/chat/messages/' + state.activeContact.id;
        if (beforeId) url += '?before_id=' + beforeId;

        els.messagesLoading.classList.remove('hidden');

        api('GET', url).then(function(data) {
            var isInitialLoad = !beforeId;
            var oldScrollHeight = els.messagesContainer.scrollHeight;

            if (isInitialLoad) {
                state.messages = data.messages || [];
            } else {
                state.messages = (data.messages || []).concat(state.messages);
            }
            state.hasMore = data.has_more;

            renderMessages();
            els.messagesLoading.classList.add('hidden');
            els.loadMoreBtn.classList.toggle('hidden', !state.hasMore);

            if (isInitialLoad) {
                scrollToBottom();
            } else {
                // Maintain scroll position
                els.messagesContainer.scrollTop = els.messagesContainer.scrollHeight - oldScrollHeight;
            }

            state.loading = false;
        }).catch(function() {
            els.messagesLoading.classList.add('hidden');
            state.loading = false;
        });
    }

    function loadOlderMessages() {
        if (state.messages.length === 0) return;
        loadMessages(state.messages[0].id);
    }

    function renderMessages() {
        var html = '';
        var lastDate = '';

        state.messages.forEach(function(msg) {
            var date = new Date(msg.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            if (date !== lastDate) {
                lastDate = date;
                html += '<div class="flex justify-center my-3"><span class="bg-white/80 text-gray-500 text-xs px-3 py-1 rounded-full shadow-sm">' + date + '</span></div>';
            }

            var isMine = msg.sender_id === AUTH_USER_ID;
            var time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            html += '<div class="flex ' + (isMine ? 'justify-end' : 'justify-start') + ' mb-1">'
                + '<div class="max-w-[75%] ' + (isMine
                    ? 'bg-primary-600 text-white rounded-2xl rounded-br-sm'
                    : 'bg-white text-gray-900 rounded-2xl rounded-bl-sm shadow-sm')
                + ' px-3.5 py-2 relative">'
                + '<p class="text-sm whitespace-pre-wrap break-words">' + escHtml(msg.message) + '</p>'
                + '<div class="flex items-center justify-end gap-1 mt-0.5">'
                + '<span class="text-xs ' + (isMine ? 'text-primary-200' : 'text-gray-400') + '">' + time + '</span>'
                + (isMine ? '<span class="text-xs ' + (msg.is_read ? 'text-blue-300' : 'text-primary-300') + '">' + (msg.is_read ? '✓✓' : '✓') + '</span>' : '')
                + '</div></div></div>';
        });

        els.messagesList.innerHTML = html;
    }

    // ── Send Message ──
    function sendMessage() {
        var text = els.messageInput.value.trim();
        if (!text || !state.activeContact) return;

        els.messageInput.value = '';
        els.sendBtn.disabled = true;
        autoResizeInput();

        api('POST', '/chat/send', {
            receiver_id: state.activeContact.id,
            message: text
        }).then(function(data) {
            state.messages.push(data.message);
            renderMessages();
            scrollToBottom();

            // Update contact list
            var contact = state.contacts.find(function(c) { return c.id === state.activeContact.id; });
            if (contact) {
                contact.last_message = { message: text, created_at: data.message.created_at, is_mine: true };
            }
            renderContacts();
        }).catch(function(err) {
            console.error('Send failed:', err);
        });

        // Stop typing
        sendTypingStatus(false);
    }

    // ── Typing Indicator ──
    function sendTypingStatus(typing) {
        if (!state.activeContact) return;
        if (state.isTyping === typing) return;
        state.isTyping = typing;

        api('POST', '/chat/typing', {
            receiver_id: state.activeContact.id,
            is_typing: typing
        }).catch(function() {});
    }

    function handleTyping() {
        sendTypingStatus(true);
        clearTimeout(state.typingTimeout);
        state.typingTimeout = setTimeout(function() {
            sendTypingStatus(false);
        }, 2000);
    }

    // ── Scroll ──
    function scrollToBottom() {
        setTimeout(function() {
            els.messagesContainer.scrollTop = els.messagesContainer.scrollHeight;
        }, 50);
    }

    // ── Echo / Realtime Listeners ──
    function initEcho() {
        if (!window.Echo) return;

        window.Echo.private('chat.' + AUTH_USER_ID)
            .listen('.message.sent', function(data) {
                // Message from another user
                if (state.activeContact && data.sender_id === state.activeContact.id) {
                    state.messages.push(data);
                    renderMessages();
                    scrollToBottom();
                    // Mark as read
                    api('POST', '/chat/read/' + data.sender_id);
                } else {
                    // Update unread in contacts
                    var contact = state.contacts.find(function(c) { return c.id === data.sender_id; });
                    if (contact) {
                        contact.unread_count = (contact.unread_count || 0) + 1;
                        contact.last_message = { message: data.message, created_at: data.created_at, is_mine: false };
                        renderContacts();
                    } else {
                        loadContacts(); // reload contacts to pick up new user
                    }
                }
            })
            .listen('.user.typing', function(data) {
                if (state.activeContact && data.sender_id === state.activeContact.id) {
                    els.typingIndicator.classList.toggle('hidden', !data.is_typing);
                    if (data.is_typing) scrollToBottom();
                }
            })
            .listen('.message.read', function(data) {
                if (state.activeContact && data.read_by === state.activeContact.id) {
                    state.messages.forEach(function(m) {
                        if (m.sender_id === AUTH_USER_ID && !m.is_read) {
                            m.is_read = true;
                        }
                    });
                    renderMessages();
                }
            });

        // Online status (public channel)
        window.Echo.channel('online-status')
            .listen('.user.online-status', function(data) {
                var contact = state.contacts.find(function(c) { return c.id === data.user_id; });
                if (contact) {
                    contact.is_online = data.is_online;
                    contact.last_seen = data.last_seen;
                    renderContacts();
                    if (state.activeContact && state.activeContact.id === data.user_id) {
                        state.activeContact.is_online = data.is_online;
                        state.activeContact.last_seen = data.last_seen;
                        updateContactStatus(state.activeContact);
                    }
                }
            });
    }

    // ── Event Bindings ──
    els.messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    els.messageInput.addEventListener('input', function() {
        els.sendBtn.disabled = !this.value.trim();
        handleTyping();
        autoResizeInput();
    });

    els.messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    els.searchContacts.addEventListener('input', function() {
        renderContacts(this.value);
    });

    if (els.backToContacts) {
        els.backToContacts.addEventListener('click', function() {
            els.chatPanel.classList.add('hidden');
            els.chatPanel.classList.remove('flex');
            els.contactPanel.classList.remove('hidden');
            els.contactPanel.classList.add('flex');
        });
    }

    function autoResizeInput() {
        els.messageInput.style.height = 'auto';
        els.messageInput.style.height = Math.min(els.messageInput.scrollHeight, 120) + 'px';
    }

    // ── Scroll detection for load older ──
    els.messagesContainer.addEventListener('scroll', function() {
        if (this.scrollTop < 50 && state.hasMore && !state.loading) {
            loadOlderMessages();
        }
    });

    // ── Helpers ──
    function escHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function truncate(str, len) {
        return str.length > len ? str.substring(0, len) + '...' : str;
    }

    function timeAgo(dateStr) {
        var now = new Date();
        var date = new Date(dateStr);
        var diffMs = now - date;
        var diffMin = Math.floor(diffMs / 60000);
        var diffH = Math.floor(diffMs / 3600000);
        var diffD = Math.floor(diffMs / 86400000);

        if (diffMin < 1) return 'Baru saja';
        if (diffMin < 60) return diffMin + ' mnt';
        if (diffH < 24) return diffH + ' jam';
        if (diffD === 1) return 'Kemarin';
        if (diffD < 7) return diffD + ' hari';
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }

    // ── Init ──
    loadContacts();
    initEcho();

    // Set online
    api('POST', '/chat/online').catch(function() {});

    // Public API
    window.ChatApp = {
        selectContact: selectContact,
        loadOlderMessages: loadOlderMessages,
    };
})();
</script>
@endpush
