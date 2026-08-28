<?php
// ═══════════════ API ENDPOINTS ═══════════════
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    if ($action === 'head_demo') {
        $response = [
            'method' => 'HEAD',
            'url' => 'https://example.com/resource',
            'status' => 200,
            'headers' => [
                'HTTP/1.1 200 OK',
                'Content-Type: text/html; charset=UTF-8',
                'Content-Length: 3526',
                'Last-Modified: Thu, 28 Aug 2026 03:48:00 GMT',
                'ETag: "abc123def456"',
                'Cache-Control: max-age=3600',
                'Server: Apache/2.4.51',
                'X-Powered-By: PHP/8.2',
            ],
            'body' => null,
            'body_size' => 0,
            'time_ms' => rand(42, 120),
        ];
        echo json_encode($response);
        exit;
    }

    if ($action === 'get_demo') {
        $response = [
            'method' => 'GET',
            'url' => 'https://example.com/resource',
            'status' => 200,
            'headers' => [
                'HTTP/1.1 200 OK',
                'Content-Type: text/html; charset=UTF-8',
                'Content-Length: 3526',
                'Last-Modified: Thu, 28 Aug 2026 03:48:00 GMT',
                'ETag: "abc123def456"',
                'Cache-Control: max-age=3600',
                'Server: Apache/2.4.51',
                'X-Powered-By: PHP/8.2',
            ],
            'body' => '<!DOCTYPE html><html><head><title>Example Page</title></head><body><h1>Hello World</h1><p>This is a full HTML body response with all its content...</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p><p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></body></html>',
            'body_size' => 3526,
            'time_ms' => rand(180, 400),
        ];
        echo json_encode($response);
        exit;
    }

    if ($action === 'live_head') {
        $url = $_GET['url'] ?? '';
        if (empty($url)) {
            echo json_encode(['error' => 'No URL provided']);
            exit;
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo json_encode(['error' => 'Invalid URL format']);
            exit;
        }

        $start = microtime(true);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'HTTP-HEAD-Presentation/1.0');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $elapsed = round((microtime(true) - $start) * 1000);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo json_encode(['error' => $error]);
            exit;
        }

        $headerLines = [];
        if ($response) {
            $lines = explode("\r\n", trim($response));
            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    $headerLines[] = trim($line);
                }
            }
        }

        echo json_encode([
            'method' => 'HEAD',
            'url' => $url,
            'status' => $httpCode,
            'headers' => $headerLines,
            'body' => null,
            'body_size' => 0,
            'time_ms' => $elapsed,
        ]);
        exit;
    }

    echo json_encode(['error' => 'Unknown action']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interactive presentation on the HTTP HEAD method — learn how HEAD works, differs from GET, and see live demonstrations.">
    <title>HTTP HEAD Method – Interactive Presentation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ═══════════════ NAVIGATION ═══════════════ -->
<nav class="nav" id="mainNav">
    <div class="nav-inner">
        <div class="nav-logo">
            <span class="nav-badge">HTTP</span>
            <span class="nav-title">HEAD Method</span>
        </div>
        <ul class="nav-links" id="navLinks">
            <li><a href="#what-is" class="nav-link">What is HEAD?</a></li>
            <li><a href="#how-it-works" class="nav-link">How It Works</a></li>
            <li><a href="#use-cases" class="nav-link">Use Cases</a></li>
            <li><a href="#demo" class="nav-link">Live Demo</a></li>
            <li><a href="#code" class="nav-link">Code</a></li>
            <li><a href="#quiz" class="nav-link">Quiz</a></li>
        </ul>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- ═══════════════ HERO SECTION ═══════════════ -->
<section class="hero" id="hero">
    <div class="hero-bg">
        <div class="grid-overlay"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <canvas id="particleCanvas"></canvas>
    </div>
    <div class="hero-content">
        <div class="hero-eyebrow">
            <span class="pulse-dot"></span>
            HTTP Protocol Methods
        </div>
        <h1 class="hero-title">
            The <span class="gradient-text">HEAD</span><br>Method
        </h1>
        <p class="hero-subtitle">
            Fetch metadata without the body. Discover how HTTP HEAD lets you inspect resources efficiently — 
            no wasted bandwidth, no unnecessary data transfer.
        </p>
        <div class="hero-actions">
            <a href="#demo" class="btn btn-primary">
                <span>Try Live Demo</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="#what-is" class="btn btn-ghost">Learn More</a>
        </div>
        <div class="hero-stats">
            <div class="stat">
                <span class="stat-num" data-count="50">0</span><span class="stat-symbol">%</span>
                <span class="stat-label">Bandwidth Saved</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat">
                <span class="stat-text">RFC 9110</span>
                <span class="stat-label">Specification</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat">
                <span class="stat-text">HTTP/1.0+</span>
                <span class="stat-label">Supported Since</span>
            </div>
        </div>
    </div>
    <div class="hero-visual">
        <div class="terminal-window">
            <div class="terminal-header">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span class="terminal-title">Terminal</span>
            </div>
            <div class="terminal-body" id="typingTerminal"></div>
        </div>
    </div>
    <div class="scroll-hint">
        <span>Scroll to explore</span>
        <div class="scroll-arrow"></div>
    </div>
</section>

<!-- ═══════════════ WHAT IS HEAD ═══════════════ -->
<section class="section" id="what-is">
    <div class="container">
        <div class="section-header appear">
            <span class="section-tag">01 — Definition</span>
            <h2 class="section-title">What is the HEAD Method?</h2>
            <p class="section-desc">The HTTP HEAD method is identical to GET — but the server <strong>must not</strong> include a message body in the response.</p>
        </div>
        <div class="definition-grid">
            <div class="definition-card glass-card appear">
                <div class="card-icon icon-blue">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <h3>RFC 9110 Definition</h3>
                <p>The HEAD method is identical to GET except that the server <em>MUST NOT</em> send content in the response. HEAD is used to obtain metadata about the selected representation without transferring the content itself.</p>
            </div>
            <div class="definition-card glass-card appear">
                <div class="card-icon icon-purple">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Safe &amp; Idempotent</h3>
                <p>HEAD is both <strong>safe</strong> (does not modify server state) and <strong>idempotent</strong> (making the same request multiple times produces the same result). It's read-only by design.</p>
            </div>
            <div class="definition-card glass-card appear">
                <div class="card-icon icon-green">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <h3>Bandwidth Efficient</h3>
                <p>By omitting the body, HEAD responses are significantly smaller — making them ideal for metadata retrieval, link validation, and health checks without wasting data.</p>
            </div>
        </div>
        <div class="callout appear">
            <div class="callout-icon">💡</div>
            <div class="callout-content">
                <strong>Key Insight:</strong> The Content-Length header in a HEAD response reflects the size of the body that <em>would</em> have been returned by GET — even though no body is actually sent. This lets you know the file size before downloading.
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ HOW IT WORKS ═══════════════ -->
<section class="section section-dark" id="how-it-works">
    <div class="container">
        <div class="section-header appear">
            <span class="section-tag">02 — Mechanics</span>
            <h2 class="section-title">How It Works</h2>
            <p class="section-desc">See the structural difference between HEAD and GET at the protocol level.</p>
        </div>

        <div class="comparison-wrapper appear">
            <div class="comparison-col">
                <div class="comparison-label label-get">GET Request</div>
                <div class="flow-diagram">
                    <div class="flow-step">
                        <div class="flow-node flow-client">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            Client
                        </div>
                        <div class="flow-arrow-right">
                            <span class="flow-label">GET /resource HTTP/1.1</span>
                        </div>
                        <div class="flow-node flow-server">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                            Server
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-node flow-client">Client</div>
                        <div class="flow-arrow-left">
                            <span class="flow-label">Headers + <span class="highlight-body">Full Body ✖</span></span>
                        </div>
                        <div class="flow-node flow-server">Server</div>
                    </div>
                </div>
                <div class="flow-info">
                    <div class="info-row"><span class="info-key">Response:</span><span class="info-val bad">Headers + Full Body</span></div>
                    <div class="info-row"><span class="info-key">Bandwidth:</span><span class="info-val bad">High (~3.5 KB+)</span></div>
                    <div class="info-row"><span class="info-key">Latency:</span><span class="info-val bad">Higher</span></div>
                </div>
            </div>
            <div class="comparison-vs">VS</div>
            <div class="comparison-col">
                <div class="comparison-label label-head">HEAD Request</div>
                <div class="flow-diagram">
                    <div class="flow-step">
                        <div class="flow-node flow-client">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            Client
                        </div>
                        <div class="flow-arrow-right flow-arrow-head">
                            <span class="flow-label">HEAD /resource HTTP/1.1</span>
                        </div>
                        <div class="flow-node flow-server">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                            Server
                        </div>
                    </div>
                    <div class="flow-step">
                        <div class="flow-node flow-client">Client</div>
                        <div class="flow-arrow-left flow-arrow-head">
                            <span class="flow-label">Headers Only <span class="highlight-none">✔ No Body</span></span>
                        </div>
                        <div class="flow-node flow-server">Server</div>
                    </div>
                </div>
                <div class="flow-info">
                    <div class="info-row"><span class="info-key">Response:</span><span class="info-val good">Headers Only</span></div>
                    <div class="info-row"><span class="info-key">Bandwidth:</span><span class="info-val good">Low (~300 B)</span></div>
                    <div class="info-row"><span class="info-key">Latency:</span><span class="info-val good">Lower</span></div>
                </div>
            </div>
        </div>

        <div class="rules-grid">
            <div class="rule-card appear">
                <div class="rule-number">01</div>
                <h4>Identical Headers</h4>
                <p>The server must send the same headers as it would for a GET request, including Content-Length and Content-Type.</p>
            </div>
            <div class="rule-card appear">
                <div class="rule-number">02</div>
                <h4>No Body Allowed</h4>
                <p>The response MUST NOT include a message body. Any body sent is in violation of the HTTP specification.</p>
            </div>
            <div class="rule-card appear">
                <div class="rule-number">03</div>
                <h4>Cacheable</h4>
                <p>HEAD responses are cacheable. A cache entry updated by HEAD can be used for subsequent GET requests if fresh.</p>
            </div>
            <div class="rule-card appear">
                <div class="rule-number">04</div>
                <h4>Conditional Requests</h4>
                <p>HEAD supports If-Modified-Since, If-None-Match, and other conditional headers — just like GET.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ USE CASES ═══════════════ -->
<section class="section" id="use-cases">
    <div class="container">
        <div class="section-header appear">
            <span class="section-tag">03 — Applications</span>
            <h2 class="section-title">Real-World Use Cases</h2>
            <p class="section-desc">Where HEAD shines in production environments.</p>
        </div>
        <div class="use-cases-grid">
            <div class="use-case-card glass-card featured appear">
                <div class="use-case-icon">🔗</div>
                <h3>Link Validation</h3>
                <p>Web crawlers and link checkers use HEAD to verify if a URL returns 200 OK without downloading the entire page — massively reducing bandwidth for large-scale crawls.</p>
                <div class="use-case-example">
                    <code>HEAD /page.html → 200 OK ✓</code>
                    <code>HEAD /missing → 404 Not Found ✗</code>
                </div>
            </div>
            <div class="use-case-card glass-card appear">
                <div class="use-case-icon">📦</div>
                <h3>File Size Check</h3>
                <p>Check Content-Length before deciding whether to download a file. Essential for download managers and mobile apps on limited data plans.</p>
                <div class="use-case-example">
                    <code>Content-Length: 104857600 (100 MB)</code>
                </div>
            </div>
            <div class="use-case-card glass-card appear">
                <div class="use-case-icon">🕐</div>
                <h3>Cache Validation</h3>
                <p>Use Last-Modified or ETag headers from HEAD to check if a cached resource is still fresh without re-downloading it.</p>
                <div class="use-case-example">
                    <code>ETag: "abc123def456"</code>
                </div>
            </div>
            <div class="use-case-card glass-card appear">
                <div class="use-case-icon">💚</div>
                <h3>Health Checks</h3>
                <p>Load balancers and monitoring tools use HEAD to check server availability without impacting server load with full content transfers.</p>
                <div class="use-case-example">
                    <code>HEAD /health → 200 OK (42ms)</code>
                </div>
            </div>
            <div class="use-case-card glass-card appear">
                <div class="use-case-icon">🔄</div>
                <h3>Resumable Downloads</h3>
                <p>Download managers send HEAD to check if the server supports Accept-Ranges before splitting a file into chunks for parallel downloads.</p>
                <div class="use-case-example">
                    <code>Accept-Ranges: bytes</code>
                </div>
            </div>
            <div class="use-case-card glass-card appear">
                <div class="use-case-icon">🔍</div>
                <h3>Content Negotiation</h3>
                <p>Verify the content type or encoding the server will deliver for a given URL before committing to a full GET request.</p>
                <div class="use-case-example">
                    <code>Content-Type: application/json</code>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ LIVE DEMO ═══════════════ -->
<section class="section section-dark" id="demo">
    <div class="container">
        <div class="section-header appear">
            <span class="section-tag">04 — Interactive</span>
            <h2 class="section-title">Live Demonstration</h2>
            <p class="section-desc">Compare HEAD vs GET side-by-side and try a real HEAD request.</p>
        </div>

        <div class="demo-tabs appear">
            <button class="demo-tab active" data-tab="compare" id="tab-compare">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="3" width="7" height="18" rx="1"/></svg>
                HEAD vs GET
            </button>
            <button class="demo-tab" data-tab="live" id="tab-live">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                Live HEAD Request
            </button>
        </div>

        <!-- COMPARE PANEL -->
        <div class="demo-panel" id="panel-compare">
            <div class="demo-controls">
                <button class="btn btn-primary" id="runComparison">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    <span>Run Comparison</span>
                </button>
                <span class="demo-hint">Click to simulate HEAD vs GET requests to <code>example.com/resource</code></span>
            </div>
            <div class="compare-panels">
                <div class="request-panel" id="headPanel">
                    <div class="request-panel-header head-header">
                        <span class="method-badge method-head">HEAD</span>
                        <span class="panel-url">/resource</span>
                        <span class="panel-status" id="headStatus"></span>
                    </div>
                    <div class="request-panel-body">
                        <div class="panel-section">
                            <div class="panel-section-title">📤 Request</div>
                            <pre class="code-block"><code>HEAD /resource HTTP/1.1
Host: example.com
Accept: */*</code></pre>
                        </div>
                        <div class="panel-section">
                            <div class="panel-section-title">📥 Response Headers</div>
                            <pre class="code-block" id="headHeaders"><code class="placeholder">— Click "Run Comparison" —</code></pre>
                        </div>
                        <div class="panel-section">
                            <div class="panel-section-title">📄 Response Body</div>
                            <pre class="code-block body-empty"><code class="empty-body">⊘ No body (HEAD response)</code></pre>
                        </div>
                        <div class="panel-metrics" id="headMetrics"></div>
                    </div>
                </div>
                <div class="request-panel" id="getPanel">
                    <div class="request-panel-header get-header">
                        <span class="method-badge method-get">GET</span>
                        <span class="panel-url">/resource</span>
                        <span class="panel-status" id="getStatus"></span>
                    </div>
                    <div class="request-panel-body">
                        <div class="panel-section">
                            <div class="panel-section-title">📤 Request</div>
                            <pre class="code-block"><code>GET /resource HTTP/1.1
Host: example.com
Accept: */*</code></pre>
                        </div>
                        <div class="panel-section">
                            <div class="panel-section-title">📥 Response Headers</div>
                            <pre class="code-block" id="getHeaders"><code class="placeholder">— Click "Run Comparison" —</code></pre>
                        </div>
                        <div class="panel-section">
                            <div class="panel-section-title">📄 Response Body</div>
                            <pre class="code-block" id="getBody"><code class="placeholder">— Click "Run Comparison" —</code></pre>
                        </div>
                        <div class="panel-metrics" id="getMetrics"></div>
                    </div>
                </div>
            </div>
            <div class="comparison-summary" id="comparisonSummary" style="display:none">
                <div class="summary-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    HEAD saved <strong id="savedBytes">—</strong> bytes of body data
                </div>
                <div class="summary-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    HEAD was <strong id="savedTime">—</strong>ms faster
                </div>
                <div class="summary-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Both returned <strong>identical headers</strong>
                </div>
            </div>
        </div>

        <!-- LIVE PANEL -->
        <div class="demo-panel hidden" id="panel-live">
            <div class="live-demo-form">
                <label class="form-label" for="urlInput">Enter a URL to send a HEAD request:</label>
                <div class="form-row">
                    <input type="url" id="urlInput" class="form-input" placeholder="https://example.com" value="https://www.php.net">
                    <button class="btn btn-primary" id="sendHead">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Send HEAD
                    </button>
                </div>
                <div class="form-hint">⚠️ Request is proxied through PHP on the server. HTTPS is supported.</div>
            </div>
            <div class="live-result" id="liveResult" style="display:none">
                <div class="result-meta">
                    <span class="method-badge method-head">HEAD</span>
                    <span class="result-url" id="resultUrl"></span>
                    <span class="result-code" id="resultCode"></span>
                    <span class="result-time" id="resultTime"></span>
                </div>
                <pre class="code-block result-headers" id="resultHeaders"></pre>
                <div class="result-body-note">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    No response body — this is a HEAD request!
                </div>
            </div>
            <div class="live-error" id="liveError" style="display:none"></div>
            <div class="live-loading" id="liveLoading" style="display:none">
                <div class="spinner"></div>
                <span>Sending HEAD request…</span>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ CODE EXAMPLES ═══════════════ -->
<section class="section" id="code">
    <div class="container">
        <div class="section-header appear">
            <span class="section-tag">05 — Implementation</span>
            <h2 class="section-title">Code Examples</h2>
            <p class="section-desc">How to send HEAD requests in different languages and tools.</p>
        </div>
        <div class="code-tabs appear">
            <button class="code-tab active" data-lang="php" id="code-tab-php">
                <span class="code-tab-icon">🐘</span> PHP
            </button>
            <button class="code-tab" data-lang="js" id="code-tab-js">
                <span class="code-tab-icon">⚡</span> JavaScript
            </button>
            <button class="code-tab" data-lang="curl" id="code-tab-curl">
                <span class="code-tab-icon">🖥️</span> cURL
            </button>
            <button class="code-tab" data-lang="python" id="code-tab-python">
                <span class="code-tab-icon">🐍</span> Python
            </button>
        </div>
        <div class="code-display appear">
            <div class="code-panel active" id="code-php">
                <div class="code-header">
                    <span class="code-filename">head_request.php</span>
                    <button class="copy-btn" data-target="php-code">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="code-block" id="php-code"><code><span class="syn-kw">&lt;?php</span>
<span class="syn-comment">// Using cURL to send a HEAD request</span>
<span class="syn-var">$ch</span> = <span class="syn-fn">curl_init</span>(<span class="syn-str">'https://example.com/resource'</span>);
<span class="syn-fn">curl_setopt</span>(<span class="syn-var">$ch</span>, <span class="syn-const">CURLOPT_NOBODY</span>, <span class="syn-kw">true</span>);        <span class="syn-comment">// HEAD request</span>
<span class="syn-fn">curl_setopt</span>(<span class="syn-var">$ch</span>, <span class="syn-const">CURLOPT_RETURNTRANSFER</span>, <span class="syn-kw">true</span>);
<span class="syn-fn">curl_setopt</span>(<span class="syn-var">$ch</span>, <span class="syn-const">CURLOPT_HEADER</span>, <span class="syn-kw">true</span>);
<span class="syn-fn">curl_setopt</span>(<span class="syn-var">$ch</span>, <span class="syn-const">CURLOPT_FOLLOWLOCATION</span>, <span class="syn-kw">true</span>);

<span class="syn-var">$response</span>      = <span class="syn-fn">curl_exec</span>(<span class="syn-var">$ch</span>);
<span class="syn-var">$httpCode</span>      = <span class="syn-fn">curl_getinfo</span>(<span class="syn-var">$ch</span>, <span class="syn-const">CURLINFO_HTTP_CODE</span>);
<span class="syn-var">$contentLength</span> = <span class="syn-fn">curl_getinfo</span>(<span class="syn-var">$ch</span>, <span class="syn-const">CURLINFO_CONTENT_LENGTH_DOWNLOAD</span>);
<span class="syn-var">$contentType</span>   = <span class="syn-fn">curl_getinfo</span>(<span class="syn-var">$ch</span>, <span class="syn-const">CURLINFO_CONTENT_TYPE</span>);
<span class="syn-fn">curl_close</span>(<span class="syn-var">$ch</span>);

<span class="syn-kw">echo</span> <span class="syn-str">"Status: <span class="syn-var">$httpCode</span>\n"</span>;
<span class="syn-kw">echo</span> <span class="syn-str">"Content-Length: <span class="syn-var">$contentLength</span> bytes\n"</span>;
<span class="syn-kw">echo</span> <span class="syn-str">"Content-Type: <span class="syn-var">$contentType</span>\n"</span>;
<span class="syn-kw">?&gt;</span></code></pre>
            </div>
            <div class="code-panel" id="code-js">
                <div class="code-header">
                    <span class="code-filename">head_request.js</span>
                    <button class="copy-btn" data-target="js-code">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="code-block" id="js-code"><code><span class="syn-comment">// Using the Fetch API</span>
<span class="syn-kw">const</span> response = <span class="syn-kw">await</span> <span class="syn-fn">fetch</span>(<span class="syn-str">'https://example.com/resource'</span>, {
  <span class="syn-prop">method</span>: <span class="syn-str">'HEAD'</span>,
});

<span class="syn-comment">// Read metadata from headers (no body!)</span>
<span class="syn-kw">const</span> status     = response.<span class="syn-prop">status</span>;
<span class="syn-kw">const</span> type       = response.<span class="syn-prop">headers</span>.<span class="syn-fn">get</span>(<span class="syn-str">'content-type'</span>);
<span class="syn-kw">const</span> size       = response.<span class="syn-prop">headers</span>.<span class="syn-fn">get</span>(<span class="syn-str">'content-length'</span>);
<span class="syn-kw">const</span> lastMod    = response.<span class="syn-prop">headers</span>.<span class="syn-fn">get</span>(<span class="syn-str">'last-modified'</span>);
<span class="syn-kw">const</span> etag       = response.<span class="syn-prop">headers</span>.<span class="syn-fn">get</span>(<span class="syn-str">'etag'</span>);

console.<span class="syn-fn">log</span>(<span class="syn-str">`Status: <span class="syn-var">${status}</span>`</span>);
console.<span class="syn-fn">log</span>(<span class="syn-str">`Size: <span class="syn-var">${size}</span> bytes`</span>);
console.<span class="syn-fn">log</span>(<span class="syn-str">`ETag: <span class="syn-var">${etag}</span>`</span>);

<span class="syn-comment">// response.body is null for HEAD</span>
console.<span class="syn-fn">log</span>(response.<span class="syn-prop">body</span>); <span class="syn-comment">// null</span></code></pre>
            </div>
            <div class="code-panel" id="code-curl">
                <div class="code-header">
                    <span class="code-filename">terminal</span>
                    <button class="copy-btn" data-target="curl-code">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="code-block" id="curl-code"><code><span class="syn-comment"># Basic HEAD request</span>
<span class="syn-fn">curl</span> <span class="syn-flag">-I</span> https://example.com/resource

<span class="syn-comment"># HEAD with verbose output</span>
<span class="syn-fn">curl</span> <span class="syn-flag">-v</span> <span class="syn-flag">-X HEAD</span> https://example.com/resource

<span class="syn-comment"># HEAD following redirects</span>
<span class="syn-fn">curl</span> <span class="syn-flag">-I</span> <span class="syn-flag">-L</span> https://example.com/resource

<span class="syn-comment"># HEAD with custom headers</span>
<span class="syn-fn">curl</span> <span class="syn-flag">-I</span> https://example.com/resource \
     <span class="syn-flag">-H</span> <span class="syn-str">"Accept: application/json"</span> \
     <span class="syn-flag">-H</span> <span class="syn-str">"Authorization: Bearer token123"</span>

<span class="syn-comment"># Show only specific header</span>
<span class="syn-fn">curl</span> <span class="syn-flag">-I</span> https://example.com | <span class="syn-fn">grep</span> <span class="syn-flag">-i</span> <span class="syn-str">"content-length"</span></code></pre>
            </div>
            <div class="code-panel" id="code-python">
                <div class="code-header">
                    <span class="code-filename">head_request.py</span>
                    <button class="copy-btn" data-target="python-code">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="code-block" id="python-code"><code><span class="syn-kw">import</span> requests

<span class="syn-comment"># Simple HEAD request</span>
url = <span class="syn-str">'https://example.com/resource'</span>
response = requests.<span class="syn-fn">head</span>(url, <span class="syn-prop">allow_redirects</span>=<span class="syn-kw">True</span>)

<span class="syn-fn">print</span>(<span class="syn-str">f"Status:   <span class="syn-var">{response.status_code}</span>"</span>)
<span class="syn-fn">print</span>(<span class="syn-str">f"Size:     <span class="syn-var">{response.headers.get('Content-Length')}</span> bytes"</span>)
<span class="syn-fn">print</span>(<span class="syn-str">f"Type:     <span class="syn-var">{response.headers.get('Content-Type')}</span>"</span>)
<span class="syn-fn">print</span>(<span class="syn-str">f"ETag:     <span class="syn-var">{response.headers.get('ETag')}</span>"</span>)

<span class="syn-comment"># Body is always empty for HEAD</span>
<span class="syn-fn">print</span>(<span class="syn-str">f"Body: '<span class="syn-var">{response.text}</span>'"</span>)  <span class="syn-comment"># ''</span>

<span class="syn-comment"># Utility: check if resource exists</span>
<span class="syn-kw">def</span> <span class="syn-fn">resource_exists</span>(url: <span class="syn-const">str</span>) -> <span class="syn-const">bool</span>:
    <span class="syn-kw">try</span>:
        r = requests.<span class="syn-fn">head</span>(url, <span class="syn-prop">timeout</span>=<span class="syn-num">5</span>)
        <span class="syn-kw">return</span> r.status_code == <span class="syn-num">200</span>
    <span class="syn-kw">except</span> requests.RequestException:
        <span class="syn-kw">return</span> <span class="syn-kw">False</span></code></pre>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ HEADERS TABLE ═══════════════ -->
<section class="section section-dark" id="headers">
    <div class="container">
        <div class="section-header appear">
            <span class="section-tag">06 — Reference</span>
            <h2 class="section-title">Common Response Headers</h2>
            <p class="section-desc">Key headers returned by HEAD and what information they carry.</p>
        </div>
        <div class="headers-table-wrapper appear">
            <table class="headers-table">
                <thead>
                    <tr>
                        <th>Header</th>
                        <th>Example Value</th>
                        <th>Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>Content-Length</code></td><td><code>3526</code></td><td>Size of the body that GET would return (in bytes)</td></tr>
                    <tr><td><code>Content-Type</code></td><td><code>text/html; charset=UTF-8</code></td><td>MIME type and encoding of the resource</td></tr>
                    <tr><td><code>Last-Modified</code></td><td><code>Thu, 28 Aug 2026 03:48:00 GMT</code></td><td>When the resource was last changed</td></tr>
                    <tr><td><code>ETag</code></td><td><code>"abc123def456"</code></td><td>Unique version identifier for cache validation</td></tr>
                    <tr><td><code>Cache-Control</code></td><td><code>max-age=3600</code></td><td>Caching directives for clients and proxies</td></tr>
                    <tr><td><code>Accept-Ranges</code></td><td><code>bytes</code></td><td>Server supports partial/ranged requests</td></tr>
                    <tr><td><code>Location</code></td><td><code>https://example.com/new</code></td><td>Redirect target (on 3xx status codes)</td></tr>
                    <tr><td><code>Server</code></td><td><code>Apache/2.4.51</code></td><td>Software handling the request</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ═══════════════ QUIZ ═══════════════ -->
<section class="section" id="quiz">
    <div class="container">
        <div class="section-header appear">
            <span class="section-tag">07 — Test Yourself</span>
            <h2 class="section-title">Quick Quiz</h2>
            <p class="section-desc">Test your understanding of the HTTP HEAD method.</p>
        </div>
        <div class="quiz-container appear" id="quizContainer">
            <div class="quiz-progress">
                <div class="quiz-progress-bar" id="quizProgressBar"></div>
            </div>
            <div class="quiz-card" id="quizCard"></div>
            <div class="quiz-score" id="quizScore" style="display:none"></div>
        </div>
    </div>
</section>

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo">
            <span class="nav-badge">HTTP</span>
            <span>HEAD Method Presentation</span>
        </div>
        <p class="footer-text">Built with PHP · HTML · CSS · JavaScript</p>
        <div class="footer-links">
            <a href="https://www.rfc-editor.org/rfc/rfc9110#section-9.3.2" target="_blank" rel="noopener">RFC 9110 §9.3.2</a>
            <span class="footer-sep">·</span>
            <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD" target="_blank" rel="noopener">MDN Docs</a>
        </div>
    </div>
</footer>

<script src="app.js"></script>
</body>
</html>
