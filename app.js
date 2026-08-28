/* ═══════════════════════════════════════════════
   HTTP HEAD METHOD PRESENTATION — APP.JS
   All interactivity: terminal animation, demos,
   quiz, scroll effects, particles, and more.
   ═══════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    // ─── NAV SCROLL & TOGGLE ───
    const nav = document.getElementById('mainNav');
    const navToggle = document.getElementById('navToggle');
    const navLinks = document.getElementById('navLinks');
    const navLinkItems = document.querySelectorAll('.nav-link');

    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 40);
    });

    navToggle.addEventListener('click', () => {
        navToggle.classList.toggle('open');
        navLinks.classList.toggle('open');
    });

    navLinkItems.forEach(link => {
        link.addEventListener('click', () => {
            navToggle.classList.remove('open');
            navLinks.classList.remove('open');
        });
    });

    // Active nav tracking
    const sections = document.querySelectorAll('section[id]');
    const observerNav = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinkItems.forEach(l => l.classList.remove('active'));
                const active = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
                if (active) active.classList.add('active');
            }
        });
    }, { threshold: 0.3, rootMargin: '-80px 0px -40% 0px' });
    sections.forEach(s => observerNav.observe(s));

    // ─── SCROLL ANIMATIONS ───
    const appearElements = document.querySelectorAll('.appear');
    const observerAppear = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    appearElements.forEach(el => observerAppear.observe(el));

    // ─── STAT COUNTER ANIMATION ───
    const statNums = document.querySelectorAll('.stat-num[data-count]');
    const observerStat = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.count);
                animateCounter(el, target, 1200);
                observerStat.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    statNums.forEach(el => observerStat.observe(el));

    function animateCounter(el, target, duration) {
        const start = performance.now();
        function update(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = '~' + Math.round(target * eased);
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    // ─── PARTICLE CANVAS ───
    const canvas = document.getElementById('particleCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let particles = [];
        let animFrameId;

        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        class Particle {
            constructor() { this.reset(); }
            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 0.5;
                this.speedX = (Math.random() - 0.5) * 0.4;
                this.speedY = (Math.random() - 0.5) * 0.4;
                this.opacity = Math.random() * 0.4 + 0.1;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(99, 102, 241, ${this.opacity})`;
                ctx.fill();
            }
        }

        for (let i = 0; i < 60; i++) particles.push(new Particle());

        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => { p.update(); p.draw(); });

            // Draw connections
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(99, 102, 241, ${0.08 * (1 - dist / 120)})`;
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }
            animFrameId = requestAnimationFrame(animateParticles);
        }
        animateParticles();
    }

    // ─── HERO TERMINAL ANIMATION ───
    const terminal = document.getElementById('typingTerminal');
    if (terminal) {
        const lines = [
            { text: '$ curl -I https://example.com/resource', cls: 'cmd' },
            { text: '', cls: '' },
            { text: 'HTTP/1.1 200 OK', cls: 'status' },
            { text: 'Content-Type: text/html; charset=UTF-8', cls: '' },
            { text: 'Content-Length: 3526', cls: '' },
            { text: 'Last-Modified: Thu, 28 Aug 2026 03:48:00 GMT', cls: '' },
            { text: 'ETag: "abc123def456"', cls: '' },
            { text: 'Cache-Control: max-age=3600', cls: '' },
            { text: 'Server: Apache/2.4.51', cls: '' },
            { text: '', cls: '' },
            { text: '# ✔ No body returned — only headers!', cls: 'cmd' },
        ];

        let lineIndex = 0;

        function showLine() {
            if (lineIndex >= lines.length) {
                // Add blinking cursor
                const cursorEl = document.createElement('span');
                cursorEl.className = 'cursor';
                terminal.appendChild(cursorEl);
                return;
            }

            const line = lines[lineIndex];
            const div = document.createElement('div');
            div.className = 'line';
            div.style.animationDelay = '0s';

            if (line.text === '') {
                div.innerHTML = '&nbsp;';
            } else if (line.cls === 'cmd') {
                div.innerHTML = `<span class="cmd">${escapeHtml(line.text)}</span>`;
            } else if (line.cls === 'status') {
                div.innerHTML = `<span class="status">${escapeHtml(line.text)}</span>`;
            } else {
                const parts = line.text.split(':');
                if (parts.length >= 2) {
                    div.innerHTML = `<span class="header-key">${escapeHtml(parts[0])}:</span><span class="header-val">${escapeHtml(parts.slice(1).join(':'))}</span>`;
                } else {
                    div.textContent = line.text;
                }
            }

            terminal.appendChild(div);
            lineIndex++;
            setTimeout(showLine, lineIndex <= 1 ? 600 : 180);
        }

        setTimeout(showLine, 800);
    }

    function escapeHtml(str) {
        const el = document.createElement('span');
        el.textContent = str;
        return el.innerHTML;
    }

    // ─── DEMO TABS ───
    document.querySelectorAll('.demo-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.demo-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.dataset.tab;
            document.querySelectorAll('.demo-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById(`panel-${target}`).classList.remove('hidden');
        });
    });

    // ─── COMPARISON DEMO ───
    const runBtn = document.getElementById('runComparison');
    if (runBtn) {
        runBtn.addEventListener('click', async () => {
            runBtn.disabled = true;
            runBtn.querySelector('span').textContent = 'Running…';

            // Reset
            document.getElementById('headStatus').textContent = '';
            document.getElementById('getStatus').textContent = '';
            document.getElementById('headMetrics').innerHTML = '';
            document.getElementById('getMetrics').innerHTML = '';
            document.getElementById('comparisonSummary').style.display = 'none';
            document.getElementById('headHeaders').innerHTML = '<code class="placeholder">Loading…</code>';
            document.getElementById('getHeaders').innerHTML = '<code class="placeholder">Loading…</code>';
            document.getElementById('getBody').innerHTML = '<code class="placeholder">Loading…</code>';

            try {
                const [headRes, getRes] = await Promise.all([
                    fetch('?action=head_demo').then(r => r.json()),
                    fetch('?action=get_demo').then(r => r.json()),
                ]);

                // HEAD panel
                const headStatusEl = document.getElementById('headStatus');
                headStatusEl.textContent = headRes.status + ' OK';
                headStatusEl.className = 'panel-status ok';

                document.getElementById('headHeaders').innerHTML =
                    '<code>' + headRes.headers.map(h => escapeHtml(h)).join('\n') + '</code>';

                document.getElementById('headMetrics').innerHTML = `
                    <div class="metric"><span class="metric-label">Time:</span><span class="metric-value">${headRes.time_ms}ms</span></div>
                    <div class="metric"><span class="metric-label">Body size:</span><span class="metric-value">0 bytes</span></div>
                    <div class="metric"><span class="metric-label">Transfer:</span><span class="metric-value">~${headRes.headers.length * 35} bytes</span></div>
                `;

                // GET panel
                const getStatusEl = document.getElementById('getStatus');
                getStatusEl.textContent = getRes.status + ' OK';
                getStatusEl.className = 'panel-status ok';

                document.getElementById('getHeaders').innerHTML =
                    '<code>' + getRes.headers.map(h => escapeHtml(h)).join('\n') + '</code>';

                document.getElementById('getBody').innerHTML =
                    '<code>' + escapeHtml(getRes.body) + '</code>';

                document.getElementById('getMetrics').innerHTML = `
                    <div class="metric"><span class="metric-label">Time:</span><span class="metric-value">${getRes.time_ms}ms</span></div>
                    <div class="metric"><span class="metric-label">Body size:</span><span class="metric-value">${getRes.body_size} bytes</span></div>
                    <div class="metric"><span class="metric-label">Transfer:</span><span class="metric-value">~${getRes.body_size + getRes.headers.length * 35} bytes</span></div>
                `;

                // Summary
                const summary = document.getElementById('comparisonSummary');
                document.getElementById('savedBytes').textContent = getRes.body_size.toLocaleString();
                document.getElementById('savedTime').textContent = (getRes.time_ms - headRes.time_ms);
                summary.style.display = 'flex';

            } catch (err) {
                console.error(err);
            }

            runBtn.disabled = false;
            runBtn.querySelector('span').textContent = 'Run Comparison';
        });
    }

    // ─── LIVE HEAD DEMO ───
    const sendBtn = document.getElementById('sendHead');
    const urlInput = document.getElementById('urlInput');
    if (sendBtn) {
        sendBtn.addEventListener('click', sendHeadRequest);
        urlInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendHeadRequest(); });
    }

    async function sendHeadRequest() {
        const url = urlInput.value.trim();
        if (!url) return;

        const resultEl = document.getElementById('liveResult');
        const errorEl = document.getElementById('liveError');
        const loadingEl = document.getElementById('liveLoading');

        resultEl.style.display = 'none';
        errorEl.style.display = 'none';
        loadingEl.style.display = 'flex';

        try {
            const res = await fetch(`?action=live_head&url=${encodeURIComponent(url)}`);
            const data = await res.json();

            loadingEl.style.display = 'none';

            if (data.error) {
                errorEl.textContent = '❌ ' + data.error;
                errorEl.style.display = 'block';
                return;
            }

            document.getElementById('resultUrl').textContent = data.url;

            const codeEl = document.getElementById('resultCode');
            codeEl.textContent = data.status;
            codeEl.className = 'result-code';
            if (data.status >= 200 && data.status < 300) codeEl.classList.add('code-2xx');
            else if (data.status >= 300 && data.status < 400) codeEl.classList.add('code-3xx');
            else if (data.status >= 400 && data.status < 500) codeEl.classList.add('code-4xx');
            else codeEl.classList.add('code-5xx');

            document.getElementById('resultTime').textContent = data.time_ms + 'ms';
            document.getElementById('resultHeaders').innerHTML =
                '<code>' + data.headers.map(h => escapeHtml(h)).join('\n') + '</code>';

            resultEl.style.display = 'block';

        } catch (err) {
            loadingEl.style.display = 'none';
            errorEl.textContent = '❌ Network error: ' + err.message;
            errorEl.style.display = 'block';
        }
    }

    // ─── CODE TABS ───
    document.querySelectorAll('.code-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.code-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const lang = tab.dataset.lang;
            document.querySelectorAll('.code-panel').forEach(p => p.classList.remove('active'));
            document.getElementById(`code-${lang}`).classList.add('active');
        });
    });

    // ─── COPY BUTTONS ───
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.target;
            const codeEl = document.getElementById(targetId);
            const text = codeEl.textContent;
            navigator.clipboard.writeText(text).then(() => {
                btn.classList.add('copied');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copied!`;
                setTimeout(() => {
                    btn.classList.remove('copied');
                    btn.innerHTML = originalHTML;
                }, 2000);
            });
        });
    });

    // ─── QUIZ ───
    const quizData = [
        {
            question: 'What does the HTTP HEAD method return?',
            options: [
                'Only the response headers (no body)',
                'Only the response body (no headers)',
                'Both headers and body',
                'Nothing at all',
            ],
            correct: 0,
            explanation: 'HEAD returns the same headers as GET, but the server MUST NOT send a message body. This is the core distinction defined in RFC 9110.',
        },
        {
            question: 'How does HEAD differ from GET?',
            options: [
                'HEAD uses a different port',
                'HEAD does not include a response body',
                'HEAD cannot use HTTPS',
                'HEAD modifies the resource on the server',
            ],
            correct: 1,
            explanation: 'HEAD is identical to GET in every way except the server must not return a message body. The response headers are identical.',
        },
        {
            question: 'Is the HEAD method considered "safe" and "idempotent"?',
            options: [
                'Yes, both safe and idempotent',
                'Safe but not idempotent',
                'Idempotent but not safe',
                'Neither safe nor idempotent',
            ],
            correct: 0,
            explanation: 'HEAD is both safe (it does not modify server state) and idempotent (making the same request multiple times has the same effect as making it once).',
        },
        {
            question: 'What does the Content-Length header in a HEAD response indicate?',
            options: [
                'The size of the HEAD response itself',
                'The size of the body that GET would return',
                'Always zero for HEAD',
                'The number of headers returned',
            ],
            correct: 1,
            explanation: 'Content-Length in a HEAD response reflects the size of the body that would have been returned by a GET request — even though no body is actually sent.',
        },
        {
            question: 'Which is NOT a common use case for HEAD?',
            options: [
                'Link validation / checking if a URL exists',
                'Submitting form data to a server',
                'Checking file size before download',
                'Health checks / server monitoring',
            ],
            correct: 1,
            explanation: 'HEAD is a read-only method used for metadata inspection. Submitting form data requires POST or PUT. HEAD is commonly used for link checking, file size queries, and health monitoring.',
        },
    ];

    let quizIndex = 0;
    let quizScore = 0;
    let answered = false;

    function renderQuiz() {
        const card = document.getElementById('quizCard');
        const q = quizData[quizIndex];
        const letters = ['A', 'B', 'C', 'D'];
        const progress = ((quizIndex) / quizData.length) * 100;
        document.getElementById('quizProgressBar').style.width = progress + '%';

        card.innerHTML = `
            <div class="quiz-question-num">Question ${quizIndex + 1} of ${quizData.length}</div>
            <div class="quiz-question">${q.question}</div>
            <div class="quiz-options">
                ${q.options.map((opt, i) => `
                    <div class="quiz-option" data-index="${i}">
                        <span class="quiz-option-letter">${letters[i]}</span>
                        <span>${opt}</span>
                    </div>
                `).join('')}
            </div>
            <div id="quizFeedback"></div>
        `;

        answered = false;

        card.querySelectorAll('.quiz-option').forEach(opt => {
            opt.addEventListener('click', () => {
                if (answered) return;
                answered = true;
                const selected = parseInt(opt.dataset.index);

                // Highlight
                card.querySelectorAll('.quiz-option').forEach((o, i) => {
                    if (i === q.correct) o.classList.add('correct');
                    if (i === selected && i !== q.correct) o.classList.add('wrong');
                    if (i === selected) o.classList.add('selected');
                });

                if (selected === q.correct) quizScore++;

                const feedback = document.getElementById('quizFeedback');
                feedback.innerHTML = `
                    <div class="quiz-explanation">${q.explanation}</div>
                    ${quizIndex < quizData.length - 1
                        ? `<button class="btn btn-primary quiz-next" id="quizNext">Next Question →</button>`
                        : `<button class="btn btn-primary quiz-next" id="quizFinish">See Results</button>`
                    }
                `;

                const nextBtn = document.getElementById('quizNext') || document.getElementById('quizFinish');
                nextBtn.addEventListener('click', () => {
                    quizIndex++;
                    if (quizIndex < quizData.length) {
                        renderQuiz();
                    } else {
                        showQuizScore();
                    }
                });
            });
        });
    }

    function showQuizScore() {
        document.getElementById('quizProgressBar').style.width = '100%';
        document.getElementById('quizCard').style.display = 'none';
        const scoreEl = document.getElementById('quizScore');
        scoreEl.style.display = 'block';

        const pct = Math.round((quizScore / quizData.length) * 100);
        let message = '';
        let emoji = '';
        if (pct === 100) { message = 'Perfect Score!'; emoji = '🏆'; }
        else if (pct >= 80) { message = 'Excellent!'; emoji = '🌟'; }
        else if (pct >= 60) { message = 'Good Job!'; emoji = '👍'; }
        else { message = 'Keep Learning!'; emoji = '📚'; }

        scoreEl.innerHTML = `
            <div style="font-size: 3rem; margin-bottom: 16px;">${emoji}</div>
            <div class="score-value">${quizScore}/${quizData.length}</div>
            <div class="score-label">Questions Answered Correctly</div>
            <div class="score-message">${message}</div>
            <p style="color: var(--text-secondary); margin-bottom: 28px; font-size: 0.92rem;">You scored ${pct}% on the HTTP HEAD method quiz.</p>
            <button class="btn btn-primary" id="quizRetry">Retry Quiz</button>
        `;

        document.getElementById('quizRetry').addEventListener('click', () => {
            quizIndex = 0;
            quizScore = 0;
            scoreEl.style.display = 'none';
            document.getElementById('quizCard').style.display = 'block';
            renderQuiz();
        });
    }

    renderQuiz();

    // ─── SMOOTH SCROLL for hero buttons ───
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});
