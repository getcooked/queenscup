/*
 * The Queen's Cup assistant.
 *
 * One floating widget, used by both the landing page and the customer app, so
 * a visitor gets the same helper wherever they are. Replies are composed on
 * the server: it knows the live menu and, for a signed-in customer, that
 * person's own reservations. A signed-in customer's conversation is stored
 * against their account and follows them between devices; a visitor who is
 * not signed in still gets answers, but nothing is written down.
 */
(function () {
    'use strict';

    var STYLE = [
        '.qc-chat-launch{position:fixed;right:20px;bottom:20px;width:56px;height:56px;border-radius:50%;border:0;',
        'background:linear-gradient(135deg,#12864e,#16a65f);color:#fff;font-size:21px;cursor:pointer;z-index:9500;',
        'box-shadow:0 12px 30px rgba(18,53,36,.28);display:grid;place-items:center;transition:transform .18s ease}',
        '.qc-chat-launch:hover{transform:translateY(-2px) scale(1.04)}',
        '.qc-chat-launch .qc-dot{position:absolute;top:2px;right:2px;width:12px;height:12px;border-radius:50%;',
        'background:#ef3d5f;border:2px solid #fff;display:none}',

        '.qc-chat{position:fixed;right:20px;bottom:86px;width:min(360px,calc(100vw - 40px));height:min(520px,calc(100vh - 130px));',
        'background:#fff;border-radius:18px;box-shadow:0 24px 70px rgba(18,53,36,.26);z-index:9500;display:flex;',
        'flex-direction:column;overflow:hidden;opacity:0;transform:translateY(12px) scale(.97);pointer-events:none;',
        'transition:opacity .2s ease,transform .2s ease;font-family:"DM Sans",system-ui,sans-serif}',
        '.qc-chat.open{opacity:1;transform:none;pointer-events:auto}',

        '.qc-chat-head{background:linear-gradient(135deg,#0c6f3f,#16a65f);color:#fff;padding:13px 15px;display:flex;',
        'align-items:center;gap:11px;flex-shrink:0}',
        '.qc-chat-head img{width:34px;height:34px;border-radius:50%;object-fit:cover;background:#fff;flex-shrink:0}',
        '.qc-chat-head h4{margin:0;font-size:14px;font-weight:800;font-family:"Playfair Display",Georgia,serif}',
        '.qc-chat-head p{margin:1px 0 0;font-size:10px;opacity:.85}',
        '.qc-chat-head button{margin-left:auto;background:rgba(255,255,255,.16);border:0;color:#fff;width:26px;',
        'height:26px;border-radius:8px;cursor:pointer;font-size:13px;line-height:1}',

        '.qc-chat-body{flex:1;min-height:0;overflow-y:auto;padding:13px;background:#f7fbf8;display:flex;',
        'flex-direction:column;gap:9px;scrollbar-width:thin}',
        '.qc-msg{max-width:84%;padding:9px 12px;border-radius:14px;font-size:12.5px;line-height:1.5;word-wrap:break-word}',
        '.qc-msg.bot{background:#fff;border:1px solid #d8ebdf;color:#123524;border-bottom-left-radius:5px;align-self:flex-start}',
        '.qc-msg.customer{background:linear-gradient(135deg,#12864e,#16a65f);color:#fff;border-bottom-right-radius:5px;align-self:flex-end}',
        '.qc-msg strong{font-weight:800}',
        '.qc-quick{display:flex;flex-wrap:wrap;gap:6px;align-self:flex-start;max-width:100%}',
        '.qc-quick button{background:#fff;border:1px solid #16a65f;color:#12864e;border-radius:999px;padding:5px 11px;',
        'font-size:11px;font-weight:700;cursor:pointer;font-family:inherit}',
        '.qc-quick button:hover{background:#16a65f;color:#fff}',
        '.qc-typing{align-self:flex-start;color:#5f7f6b;font-size:11px;font-style:italic;padding:2px 4px}',

        '.qc-chat-foot{display:flex;gap:7px;padding:10px;border-top:1px solid #d8ebdf;background:#fff;flex-shrink:0}',
        '.qc-chat-foot input{flex:1;min-width:0;border:1px solid #d8ebdf;border-radius:10px;padding:9px 11px;',
        'font-size:12.5px;font-family:inherit;outline:none;color:#123524}',
        '.qc-chat-foot input:focus{border-color:#16a65f}',
        '.qc-chat-foot button{border:0;border-radius:10px;width:38px;background:linear-gradient(135deg,#12864e,#16a65f);',
        'color:#fff;cursor:pointer;font-size:13px}',
        '.qc-chat-foot button:disabled{opacity:.5;cursor:not-allowed}',

        '@media(max-width:520px){.qc-chat{right:10px;left:10px;width:auto;bottom:78px;height:min(70vh,470px)}',
        '.qc-chat-launch{right:14px;bottom:14px}}',
    ].join('');

    function el(tag, className, html) {
        var node = document.createElement(tag);
        if (className) node.className = className;
        if (html !== undefined) node.innerHTML = html;
        return node;
    }

    function QueensChat(options) {
        this.base = options.base || '';
        this.csrf = options.csrf || '';
        this.logo = options.logo || '';
        this.open = false;
        this.loaded = false;
        this.busy = false;
        this.build();
    }

    QueensChat.prototype.build = function () {
        var style = el('style');
        style.textContent = STYLE;
        document.head.appendChild(style);

        this.launch = el('button', 'qc-chat-launch');
        this.launch.type = 'button';
        this.launch.setAttribute('aria-label', 'Chat with us');
        this.launch.innerHTML = '<i class="fas fa-comments"></i><span class="qc-dot"></span>';
        document.body.appendChild(this.launch);

        this.panel = el('div', 'qc-chat');
        this.panel.setAttribute('role', 'dialog');
        this.panel.setAttribute('aria-label', "The Queen's Cup assistant");
        this.panel.innerHTML =
            '<div class="qc-chat-head">' +
                (this.logo ? '<img src="' + this.logo + '" alt="">' : '') +
                '<div><h4>Queen\'s Cup Assistant</h4><p>Menu, reservations and directions</p></div>' +
                '<button type="button" aria-label="Close">&times;</button>' +
            '</div>' +
            '<div class="qc-chat-body"></div>' +
            '<div class="qc-chat-foot">' +
                '<input type="text" placeholder="Ask about the menu…" autocomplete="off" maxlength="500">' +
                '<button type="button" aria-label="Send"><i class="fas fa-paper-plane"></i></button>' +
            '</div>';
        document.body.appendChild(this.panel);

        this.body = this.panel.querySelector('.qc-chat-body');
        this.input = this.panel.querySelector('input');
        this.send = this.panel.querySelector('.qc-chat-foot button');

        var self = this;
        this.launch.addEventListener('click', function () { self.toggle(); });
        this.panel.querySelector('.qc-chat-head button').addEventListener('click', function () { self.toggle(false); });
        this.send.addEventListener('click', function () { self.submit(); });
        this.input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') { event.preventDefault(); self.submit(); }
        });
    };

    QueensChat.prototype.toggle = function (force) {
        this.open = force === undefined ? !this.open : force;
        this.panel.classList.toggle('open', this.open);

        if (this.open) {
            this.input.focus();
            if (!this.loaded) this.load();
        }
    };

    QueensChat.prototype.load = function () {
        this.loaded = true;
        var self = this;

        fetch(this.base + '/chat', { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
            .then(function (response) { return response.ok ? response.json() : { data: [] }; })
            .then(function (payload) {
                (payload.data || []).forEach(function (message) {
                    self.render(message.author, message.body, message.quick_replies);
                });

                // A returning customer picks up where they left off; anyone
                // else is greeted.
                if (!(payload.data || []).length) {
                    self.render('bot', "Hello! 👑 I'm the Queen's Cup assistant. What can I help you with?",
                        ['See the menu', 'How do I reserve?', 'Where are you?', 'Opening hours']);
                }
            })
            .catch(function () {
                self.render('bot', 'I could not load our chat just now. Please try again in a moment.', []);
            });
    };

    QueensChat.prototype.render = function (author, body, quickReplies) {
        var message = el('div', 'qc-msg ' + (author === 'customer' ? 'customer' : 'bot'));

        // Bot copy is composed server side and may carry simple markup; what a
        // person typed is inserted as text so it can never become markup.
        if (author === 'customer') message.textContent = body;
        else message.innerHTML = body;

        this.body.appendChild(message);

        if (quickReplies && quickReplies.length) {
            var row = el('div', 'qc-quick');
            var self = this;

            quickReplies.forEach(function (reply) {
                var button = el('button', null, '');
                button.type = 'button';
                button.textContent = reply;
                button.addEventListener('click', function () {
                    row.remove();
                    self.submit(reply);
                });
                row.appendChild(button);
            });

            this.body.appendChild(row);
        }

        this.body.scrollTop = this.body.scrollHeight;
    };

    QueensChat.prototype.submit = function (preset) {
        var text = (preset !== undefined ? preset : this.input.value).trim();
        if (!text || this.busy) return;

        this.busy = true;
        this.send.disabled = true;
        if (preset === undefined) this.input.value = '';

        this.render('customer', text, []);

        var typing = el('div', 'qc-typing', 'typing…');
        this.body.appendChild(typing);
        this.body.scrollTop = this.body.scrollHeight;

        var self = this;

        fetch(this.base + '/chat', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': this.csrf },
            body: JSON.stringify({ message: text })
        })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                typing.remove();
                self.render('bot', payload.body || 'Sorry, I did not catch that.', payload.quick_replies || []);
            })
            .catch(function () {
                typing.remove();
                self.render('bot', 'I could not reach the shop just now. Please try again shortly.', []);
            })
            .finally(function () {
                self.busy = false;
                self.send.disabled = false;
            });
    };

    window.QueensChat = QueensChat;

    // Any page can opt in by tagging the script with the details it needs.
    document.addEventListener('DOMContentLoaded', function () {
        var tag = document.querySelector('script[data-queens-chat]');
        if (!tag) return;

        window.queensChat = new QueensChat({
            base: tag.getAttribute('data-base') || '',
            csrf: tag.getAttribute('data-csrf') || '',
            logo: tag.getAttribute('data-logo') || ''
        });
    });
}());
